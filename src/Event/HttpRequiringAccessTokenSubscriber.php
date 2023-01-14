<?php

declare(strict_types=1);

namespace App\Event;

use App\Controller\AccessTokenAuthenticatedController;
use App\Entity\Test;
use App\Repository\ServiceRepository;
use App\Repository\TestRepository;
use App\Service\AccessService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpClient\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Http-фильтр
 * Валидирует наличие и актуальность токена доступа (Access).
 * В отличие от TokenSubscriber, данный класс предназначен только
 * для проверки наличия права доступа, а не оплаты.
 *
 * @see AccessTokenAuthenticatedController
 */
class HttpRequiringAccessTokenSubscriber implements EventSubscriberInterface
{
    private TestRepository $tests;

    private AccessService $accessService;

    private ServiceRepository $services;

    public function __construct(TestRepository $tests, AccessService $accessService, ServiceRepository $services)
    {
        $this->tests = $tests;
        $this->accessService = $accessService;
        $this->services = $services;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController', // intercept request
            KernelEvents::RESPONSE => 'onKernelResponse', // filtering response
        ];
    }

    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if ($controller instanceof AccessTokenAuthenticatedController) {
            $request = $event->getRequest();

            $test = $this->getTest($request);
            if ($test->isFree()) {
                return;
            }

            $access = $this->accessService->findOneByToken($this->getToken($request));
            if (!$access) {
                throw new AccessDeniedHttpException('Token access not found.');
            }
            if ($access->isUsed()) {
                throw new AccessDeniedHttpException('Expired token.');
            }

//            // check access related to test's service
//            $testServices = $test->getServices();
//            if (!$testServices->contains($access->getService())) {
//                throw new AccessDeniedHttpException('Token is wrong.');
//            }

            // make access used
            $this->accessService->utilize($access);

            $request->attributes->set('access_token', $access->getToken());
        }
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        $accessToken = $event->getRequest()->attributes->get('access_token');
        if ($accessToken) {
            $service = $this->services->getById(1);
            $access = $this->accessService->create($service);
            $event->getResponse()->headers->set('Access-Control-Expose-Headers', ['X-TOKEN']);
            $event->getResponse()->headers->set('X-TOKEN', $access->getToken());
            $this->accessService->setCookie($access, $event->getResponse());
        }
    }

    private function getTest(Request $request): Test
    {
        if (($id = $request->attributes->get('testId')) == null) {
            throw new InvalidArgumentException('No "testId" specified.');
        }
        if (($test = $this->tests->findOneById((int)$id)) == null) {
            throw new NotFoundHttpException('Test not found.');
        }
        return $test;
    }

    private function getToken(Request $request): string
    {
        $token = $request->headers->get('token');
        if (!$token) {
            $token = $this->accessService->getCookie($request);
            if (!$token) {
                throw new InvalidArgumentException('No token.');
            }
        }
        return $token;
    }
}