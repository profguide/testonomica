<?php

declare(strict_types=1);

namespace App\Event;

use App\Controller\TokenAuthenticatedController;
use App\Entity\Access;
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
 * @see https://symfony.com/doc/current/event_dispatcher/before_after_filters.html
 */
class TokenSubscriber implements EventSubscriberInterface
{
    private TestRepository $tests;

    private AccessService $accessService;

    private ServiceRepository $services;

    public function __construct(
        TestRepository $tests,
        ServiceRepository $services,
        AccessService $accessService)
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

    /**
     * @param ControllerEvent $event
     * Validates and utilizes token
     */
    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if ($controller instanceof TokenAuthenticatedController) {
            $request = $event->getRequest();
            $test = $this->test($request);
            $testServices = $test->getServices();
            // free test - no token required
            if ($testServices->count() === 0) {
                return;
            }
            $token = $this->token($request);
            $access = $this->accessService->findOneByToken($token);
            if (!$access || $access->isUsed()) {
                throw new AccessDeniedHttpException('Wrong token.');
            }
            // check access related to test's service
            if (!$testServices->contains($access->getService())) {
                throw new AccessDeniedHttpException('Wrong token.');
            }
            // make access used
            $this->accessService->utilize($access);
            // mark the request as having passed token authentication
            $event->getRequest()->attributes->set('auth_token', $token);
        }
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        // check to see if onKernelController marked this as a token "auth'ed" request
        if (!$token = $event->getRequest()->attributes->get('auth_token')) {
            return;
        }
        $service = $this->services->getById(1); // todo find way hot to get it.
        $access = Access::init($service);
        $this->accessService->save($access);
        $response = $event->getResponse();
        $response->headers->set('X-TOKEN', $access->getToken());
    }

    private function test(Request $request): Test
    {
        if (($id = $request->attributes->get('testId')) == null) {
            throw new InvalidArgumentException('No "testId" specified.');
        }
        if (($test = $this->tests->findOneById((int)$id)) == null) {
            throw new NotFoundHttpException('Test not found.');
        }
        return $test;
    }

    private function token(Request $request): string
    {
        $token = $request->headers->get('token');
        if (!$token) {
            throw new InvalidArgumentException('No "token" specified.');
        }
        return $token;
    }
}