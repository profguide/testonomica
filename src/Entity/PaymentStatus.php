<?php
/**
 * @author: adavydov
 * @since: 09.11.2020
 */

namespace App\Entity;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * @author: adavydov
 * @since: 9.11.2020
 */
#[ORM\Table]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class PaymentStatus
{
    const STATUS_PENDING = 0;
    const STATUS_EXECUTED = 1;
    const STATUS_FAILED = 2;

    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    /**
     * @var Payment
     */
    #[ORM\ManyToOne(targetEntity: 'Payment')]
    #[ORM\JoinColumn(name: 'payment_id')]
    private $payment;

    /**
     * @var integer
     */
    #[ORM\Column(type: 'smallint', length: 1)]
    private $status;

    #[ORM\Column(type: 'datetime', name: 'created_at')]
    private $createdAt;

    /**
     * PaymentStatus constructor.
     * @param int $status
     */
    public function __construct(int $status)
    {
        $this->status = $status;
    }

    public static function criteriaExecuted()
    {
        return Criteria::expr()->eq("status", self::STATUS_EXECUTED);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Payment
     */
    public function getPayment(): Payment
    {
        return $this->payment;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param Payment $payment
     */
    public function setPayment(Payment $payment): void
    {
        $this->payment = $payment;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function prePersist()
    {
        $this->createdAt = new \DateTime();
    }
}