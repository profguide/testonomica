<?php
/**
 * @author: adavydov
 * @since: 09.11.2020
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table
 * @author: adavydov
 * @since: 9.11.2020
 */
class PaymentStatus
{
    const STATUS_PENDING = 0;
    const STATUS_EXECUTED = 1;
    const STATUS_FAILED = 2;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @var Payment
     * @ORM\ManyToOne(targetEntity="Payment")
     * @ORM\JoinColumn(name="payment_id")
     */
    private $payment;

    /**
     * @ORM\Column(type="smallint", length=1)
     * @var integer
     */
    private $status;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    private $createdAt;
}