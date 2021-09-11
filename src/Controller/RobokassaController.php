<?php
/**
 * @author: adavydov
 * @since: 16.11.2020
 */

namespace App\Controller;


use App\Payment\Robokassa;
use App\Repository\ServiceRepository;
use App\Service\AccessService;
use App\Service\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/robokassa")
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
     * @Route("/done/")
     * @param Request $request
     * @return Response
     */
    public function done(Request $request): Response
    {
        $id = $request->get('inv_id');
        $price = $request->get('OutSum');
        $crc = $request->get('SignatureValue');
        if (($payment = $this->paymentService->findOneById($id)) == null) {
            throw new BadRequestHttpException("Payment {$id} not found.");
        }
        $this->robokassa->guardCode($payment, $id, $price, $crc);
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
        if (($paymentId = $this->paymentService->getCookie($request)) == null) {
            throw new \LogicException('Платёж не обнаружен. Вернитесь на сайт партнёра.');
        }
        if (($payment = $this->paymentService->findOneById($paymentId)) == null) {
            throw new NotFoundHttpException('Платёж не обнаружен.');
        }
        if (!$payment->isExecuted()) {
            throw new NotFoundHttpException('Нет информации о поступившем платеже. Это может быть вызвано задержками обмена с платёжной системой. Пожалуйста, обновить страницу через 1 минуту.');
        }
        // todo route get from somewhere
        $response = new RedirectResponse($this->generateUrl('tests.view', [
            'categorySlug' => 'business',
            'slug' => 'proforientation-v2'
        ]));
        $this->accessService->setCookie($this->accessService->create($payment->getService()), $response);
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