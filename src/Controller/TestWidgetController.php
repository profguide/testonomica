<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Test;
use App\Repository\TestRepository;
use App\Service\PublicTokenService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Тест в виджете
 *
 * Class TestWidgetController
 * @package App\Controller
 */
class TestWidgetController extends AbstractController implements HostAuthenticatedController
{
    private TestRepository $tests;

    private PublicTokenService $publicTokenService;

    public function __construct(TestRepository $tests, PublicTokenService $publicTokenService)
    {
        $this->tests = $tests;
        $this->publicTokenService = $publicTokenService;
    }

    /**
     * @Route("/tests/widget/{id}/", name="test.widget")
     * Страница должна быть загружена на сайте партнёра в Iframe.
     * Данная страница предоставляет тест полностью - оплата, приветствие, прогресс, результат.
     * Нет ни шапки, ни подвала. 100% ширина и высота отданы под тест.
     *
     * Вопрос про оплату. Если юзать Robokassa, то чё-то кажется их виджет не имеет нормального API, и об оплате мы не узнаем. Там будет кнопка вернуться в магазин или закрыть окно наверно.
     * Если там будет кнопка вернуться в магазин, то мы попадаем на стандарную страницу перенаправления
     * и оттуда можно было бы попасть в виджет.
     * Просто переустанавливаем токен на access, редиректим на test/widget?token={ACCESS_TOKEN} и ОК.
     * Если вместо кнопки вернуться в магазин будет кнопка "закрыть окно"?
     * То JS обращается куда следует и спрашивает была ли оплата по токену.
     * Если оплата была, то JS обращается куда-то и там меняют токен оплаты на токен доступа.
     * С этого момента PaymentScreen вызывает метод сокрытия себя и заускается тест.
     *
     * Боюсь, виджет робокассы не даст нам пользы. Его можно закрыть и это не вызывет колбека.
     * Рассмотрим другие варианты.
     *
     * Отправка на страницу платежа.
     * Допустим, TestWidgetController фильтруется с помощью PaymentRequiredEventSubscriber,
     * который по токену определет есть ли оплата. Если токен платёжный, и он не оплачен, то
     * PaymentRequiredEventSubscriber делает редирект ($event->setResponse(new RedirectResponse($route));)
     * на роут, где формируется урл робокассы и последующий редирект на Робокассу.
     * А можно и прямо из PaymentRequiredEventSubscriber на робокассу пойти через вызов специального сервиса, конечно.
     *
     * А можно прямо в TestWidgetController делать проверку и отправлять.
     *
     * Дальше робокасса редиректит после оплаты на установленную страницу, там мы распознаём что произошло,
     * устанавливаем токен в урл и с ним редиректим на виджет. Дальше работа происходит обычным способом.
     * Таким образом, фронт не страдает необходимостью заниматься платежами и он знает свое дело - Access передавать.
     *
     * Вопрос: как по красоте и правильно сделать определение роута.
     * Робокасса позволяет установить в url доппараметры а-ля Shp_route=test.widget.
     * но она так же требует их захешировать перед платежом и после него,
     * что означает, что мы должны заранее в Payment установить значение CRC, а потом сравнивать.
     * Кука не работает, сессия слишком короткая, да и тоже она на куках.
     *
     * Есть такой вариант. Можно добавить в Payment поле backRoute и использовать константные значения в контроллере RobokassaController.
     * Метод success опосредованно через PaymentBackUrlResolver
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function widget(Request $request, int $id): Response
    {
        $test = $this->getTest($id);

//        if (!$test->isFree()) {
//            $token = $request->get('token');
//            if (!$token) {
//                throw new AccessDeniedHttpException('Token is required.');
//            }
//            $tokenObject = $this->publicTokenService->find($token);
//            if (!$tokenObject) {
//                throw new AccessDeniedHttpException('Token not found.');
//            }
//            if ($tokenObject instanceof ProviderPayment) {
//                $payment = $tokenObject->getPayment();
//                if ($payment->isExecuted() === false) {
//                    return $this->redirectToRoute('robokassa.go', [
//                        'paymentId' => $payment->getId(),
//                        'backRoute' => PaymentBackRoute::TEST_WIDGET
//                    ]);
//                }
//            }
//        }

        $token = $request->get('token');
        // for development would be http://127.0.0.1:8080
        $host = $request->getScheme() . '://' . $request->getHttpHost();
        return $this->render('tests/widget.html.twig', [
            'testId' => $id,
            'host' => $host,
            'token' => $token
        ]);
    }

//    // todo loadTestByKey like testometrika does
    private function getTest(int $id): Test
    {
        if (($test = $this->tests->findOneById($id)) == null) {
            self::createNotFoundException();
        }
        return $test;
    }
}