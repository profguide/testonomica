<?php
/**
 * @author: adavydov
 * @since: 16.11.2020
 */

namespace App\Payment;

use App\Entity\Payment;

class Robokassa
{
    const LOGIN = 'testonomica';
    const PASS_1_PROD = 't0sWsj0BU5Fy1D2tChRG';
    const PASS_2_PROD = 'e4dfrT90T4pwzYHgopS4';
    const PASS_1_DEV = 'wWeZ6xi54bZIW3wVYM5Y';
    const PASS_2_DEV = 'lChzkIy69g022uKItDwu';

    public function createUrl(Payment $payment, bool $isTest)
    {
        $id = $payment->getId();
        $sum = floatval($payment->getSum());
        $params = [
            'MerchantLogin' => self::LOGIN,
            'OutSum' => $sum,
            'InvId' => $id,
            'SignatureValue' => md5(self::LOGIN . ":$sum:$id:" . self::getPass1()),
            'Description' => 'Тест на профориентацию'
        ];
        if ($isTest) {
            $params['IsTest'] = 1;
        }
        $url = 'https://auth.robokassa.ru/Merchant/Index.aspx?' . http_build_query($params);
        return $url;
    }

    private static function getPass1()
    {
        return self::PASS_1_PROD;
    }

    private static function getPass2()
    {
        return self::PASS_2_PROD;
    }

    public static function getCrc2(int $id, int $sum)
    {
        return md5("$sum:$id:" . self::getPass2());
    }

    public function assertCode(int $id, int $sum, string $crc)
    {
        if (static::getCrc2($id, $sum) !== mb_strtolower($crc)) {
            throw new \RuntimeException("Hash not match to expected");
        }
    }
}