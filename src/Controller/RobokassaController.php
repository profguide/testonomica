<?php
/**
 * @author: adavydov
 * @since: 16.11.2020
 */

declare(strict_types=1);

namespace App\Controller;

use App\Payment\PaymentBackRoute;
use App\Payment\Robokassa;
use App\Repository\ServiceRepository;
use App\Service\AccessService;
use App\Service\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/robokassa", name="robokassa.")
 */
class RobokassaController extends AbstractController
{
    private PaymentService $paymentService;

    private AccessService $accessService;

    private ServiceRepository $serviceRepository;

    private Robokassa $robokassa;

    /**
     * @param PaymentService $paymentService
     * @param AccessService $accessService
     * @param ServiceRepository $serviceRepository
     * @param Robokassa $robokassa
     */
    public function __construct(
        PaymentService $paymentService,
        AccessService $accessService,
        ServiceRepository $serviceRepository,
        Robokassa $robokassa)
    {
        $this->paymentService = $paymentService;
        $this->accessService = $accessService;
        $this->serviceRepository = $serviceRepository;
        $this->robokassa = $robokassa;
    }

    /**
     * @Route ("/go/{paymentId}/", name="go")
     * @param Request $request
     * @param string $paymentId
     * @return Response
     */
    public function go(Request $request, string $paymentId): Response
    {
        $payment = $this->paymentService->getOneById($paymentId);
        if ($payment->isExecuted()) {
            throw new AccessDeniedHttpException('The token has already been used.');
        }
        $backRoute = new PaymentBackRoute($request->get('backRoute'));
        $payment->setBackRoute($backRoute);
        $this->paymentService->save($payment);
        return new RedirectResponse($this->robokassa->createUrl($payment));
    }

    /**
     * @Route("/done/")
     * @param Request $request
     * @return Response
     */
    public function done(Request $request): Response
    {
        $id = $request->get('inv_id');
        $price = $request->get('OutSum');
        $crc = $request->get('SignatureValue');
        $payment = $this->paymentService->getOneById($id);
        $this->robokassa->validateCrc($payment, $id, $price, $crc);
        if (!$payment->isExecuted()) {
            $payment->addStatusExecuted();
        }
        $this->paymentService->save($payment);
        return new Response("OK{$id}");
    }

    /**
     * @Route("/success/")
     * @param Request $request
     * @return RedirectResponse
     */
    public function success(Request $request): RedirectResponse
    {
        $payment = $this->paymentService->getOneById($request->get('InvId'));
        if (!$payment->isExecuted()) {
            throw new NotFoundHttpException('Нет информации о поступившем платеже. Это может быть вызвано задержками обмена с платёжной системой. Пожалуйста, обновить страницу через 1 минуту.');
        }

        // give disposable access
        $access = $this->accessService->create($payment->getService());

        // redirect to the test
        $tests = $payment->getService()->tests();
        $firstTest = $tests[0];

        $backRoute = $payment->getBackRoute()->getValue();

        // todo раньше был виджет, теперь его нет
        if ($backRoute === PaymentBackRoute::TEST_WIDGET) {
            return new RedirectResponse($this->generateUrl('test.widget', [
                'id' => $firstTest->getId(),
                'token' => $access->getToken()
            ]));
        }

        // default route
        $response = new RedirectResponse($this->generateUrl('tests.view', [
            'slug' => $firstTest->getSlug()
        ]));
        $this->accessService->setCookie($access, $response);
        return $response;
    }

    /**
     * TODO сделать редирект на страницу партнёра.
     * Для этого надо либо получать ее в урле, либо определять и писать в куку, на странице /partner/access/provide/
     * и хранить в куке с именем provider.backUrl
     * @Route("/fail/")
     */
    public function fail(): Response
    {
        return new Response('Оплата не прошла. Вернитесь на сайт партнёра.');
    }
}