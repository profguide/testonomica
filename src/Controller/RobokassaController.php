<?php
/**
 * @author: adavydov
 * @since: 16.11.2020
 */

namespace App\Controller;


use App\Payment\Robokassa;
use App\Service\AccessService;
use App\Service\PaymentService;
use http\Exception\RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\UnsupportedException;

/**
 * @Route("/robokassa")
 */
class RobokassaController extends AbstractController
{
    /**@var PaymentService */
    private $paymentService;

    /**@var AccessService */
    private $accessService;

    /**@var Robokassa */
    private $robokassa;

    /**
     * @param PaymentService $paymentService
     * @param AccessService $accessService
     * @param Robokassa $robokassa
     */
    public function __construct(
        PaymentService $paymentService,
        AccessService $accessService,
        Robokassa $robokassa)
    {
        $this->paymentService = $paymentService;
        $this->accessService = $accessService;
        $this->robokassa = $robokassa;
    }


    /**
     * @Route("/done/")
     * @param Request $request
     * @return Response
     */
    public function done(Request $request)
    {
        $id = $request->get('inv_id');
        $price = $request->get('OutSum');
        $crc = $request->get('SignatureValue');
        if (($payment = $this->paymentService->findOneById($id)) == null) {
            throw new BadRequestHttpException("Payment {$id} not found.");
        }
        $this->robokassa->assertCode($id, $price, $crc);
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
    public function success(Request $request)
    {
        if (($paymentId = $request->cookies->get('payment')) == null) {
            throw new ServiceUnavailableHttpException(null, 'Платёж не обнаружен. Вернитесь на сайт партнёра.');
        }
        if (($payment = $this->paymentService->findOneById($paymentId)) == null) {
            throw new NotFoundHttpException('Платёж не обнаружен.');
        }
        if (!$payment->isExecuted()) {
            throw new NotFoundHttpException('Нет информации о платеже. Это может быть вызвано задержками обмена с платёжной системой. Обновить страницу через минуту.');
        }
        $response = new RedirectResponse($this->generateUrl('tests.view', [
            'categorySlug' => 'psychology',
            'slug' => 'test_2'
        ]));
        $this->accessService->saveToCookie($this->accessService->create(), $response);
        return $response;
    }

    /**
     * TODO сделать редирект на страницу партнёра.
     * Для этого надо либо получать ее в урле, либо определять и писать в куку, на странице /partner/access/provide/
     * и хранить в куке с именем provider.backUrl
     * @Route("/fail/")
     */
    public function fail()
    {
        return new Response('Оплата не прошла. Вернитесь на сайт партнёра.');
    }
}