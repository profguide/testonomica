<?php
/**
 * @author: adavydov
 * @since: 16.11.2020
 */

//declare(strict_types=1);

namespace App\Payment;

use App\Entity\Payment;
use Psr\Log\LoggerInterface;

class Robokassa
{
    const LOGIN = 'testonomica';

    const PASS_1_PROD = 'CR33GHiwwaD5MD22xpag'; // for payment
    const PASS_2_PROD = 'uVtNe469XGI6yB6IKIKs'; // for checking the pay
    const PASS_1_DEV = 'wWeZ6xi54bZIW3wVYM5Y';  // for payment
    const PASS_2_DEV = 'lChzkIy69g022uKItDwu'; // for checking the pay

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

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

    public static function crc2(int $id, float $sum, bool $testMode): string
    {
        return md5("$sum:$id:" . self::pass2($testMode));
    }

    public function guardCode(Payment $payment, int $id, float $sum, string $crc)
    {
        $genCrc = static::crc2($id, $sum, $payment->isTestMode());
        $this->logger->info("id: $id");
        $this->logger->info("sum: $sum");
        $this->logger->info("crc come: $crc");
        $this->logger->info("Check crc: $genCrc");
        if ($genCrc !== mb_strtolower($crc)) {
            throw new \RuntimeException("Hash not match to expected.");
        }
    }
}