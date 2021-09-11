<?php
/**
 * @author: adavydov
 * @since: 16.11.2020
 */

declare(strict_types=1);

namespace App\Payment;

use App\Entity\Payment;

class Robokassa
{
    const LOGIN = 'testonomica';

    const PASS_1_PROD = 't0sWsj0BU5Fy1D2tChRG'; // for payment
    const PASS_2_PROD = 'e4dfrT90T4pwzYHgopS4'; // for checking the pay
    const PASS_1_DEV = 'wWeZ6xi54bZIW3wVYM5Y';  // for payment
    const PASS_2_DEV = 'lChzkIy69g022uKItDwu'; // for checking the pay

    public function createUrl(Payment $payment): string
    {
        $id = $payment->getId();
        $sum = floatval($payment->getSum());
        $params = [
            'MerchantLogin' => self::LOGIN,
            'OutSum' => $sum,
            'InvId' => $id,
            'SignatureValue' => md5(self::LOGIN . ":$sum:$id:" . self::pass1($payment->isTestMode())),
            'Description' => $payment->getService()->getName(),
        ];
        if ($payment->isTestMode()) {
            $params['IsTest'] = 1;
        }
        return 'https://auth.robokassa.ru/Merchant/Index.aspx?' . http_build_query($params);
    }

    private static function pass1(bool $testMode): string
    {
        return $testMode ? self::PASS_1_DEV : self::PASS_1_PROD;
    }

    private static function pass2(bool $testMode): string
    {
        return $testMode ? self::PASS_2_DEV : self::PASS_2_PROD;
    }

    public static function crc2(int $id, int $sum, bool $testMode): string
    {
        return md5("$sum:$id:" . self::pass2($testMode));
    }

    public function guardCode(Payment $payment, int $id, int $sum, string $crc)
    {
        if (static::crc2($id, $sum, $payment->isTestMode()) !== mb_strtolower($crc)) {
            throw new \RuntimeException("Hash not match to expected.");
        }
    }
}