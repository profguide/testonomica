<?php
/**
 * @author: adavydov
 * @since: 09.11.2020
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * Когда понадобится ввести услуги, такие как конкретные тесты и наборы тестов, для этого
 * можно сделать отдельные структуры данных, которые будут связаны с Payment, но не наоборот
 * @ORM\Entity
 * @ORM\Table
 * @ORM\HasLifecycleCallbacks()
 * @author: adavydov
 * @since: 9.11.2020
 */
class Payment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @var Service
     * @ORM\ManyToOne(targetEntity="Service")
     * @ORM\JoinColumn(name="service_id", nullable=false)
     */
    private $service;

    /**
     * @var PaymentStatus
     * @ORM\OneToMany(targetEntity="PaymentStatus", mappedBy="payment", cascade={"persist"})
     * @ORM\JoinColumn(name="payment_id")
     */
    private $statuses;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $sum;

    /**
     * @ORM\Column(type="datetime", name="created_at", nullable=false)
     */
    private $createdAt;

    public function __construct()
    {
        $this->statuses = new ArrayCollection();
    }

    public static function init(Service $service, int $sum)
    {
        $payment = new self();
        $payment->service = $service;
        $payment->sum = $sum;
        return $payment;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Collection|PaymentStatus[]
     */
    public function getStatuses(): Collection
    {
        return $this->statuses;
    }

    /**
     * @return mixed
     */
    public function getSum()
    {
        return $this->sum;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param PaymentStatus $status
     */
    public function addStatus(PaymentStatus $status): void
    {
        if (!$this->statuses->contains($status)) {
            $this->statuses[] = $status;
            $status->setPayment($this);
        }
    }

    /**
     * @param mixed $sum
     */
    public function setSum($sum): void
    {
        $this->sum = $sum;
    }

    /**
     * @return Service
     */
    public function getService(): Service
    {
        return $this->service;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->createdAt = new \DateTime();
    }

    public function isExecuted(): bool
    {
        $criteria = Criteria::create()->where(PaymentStatus::criteriaExecuted());
        return $this->statuses->matching($criteria)->count() > 0;
    }

    public function addStatusPending(): void
    {
        $this->addStatus(new PaymentStatus(PaymentStatus::STATUS_PENDING));
    }

    public function addStatusExecuted(): void
    {
        $this->addStatus(new PaymentStatus(PaymentStatus::STATUS_EXECUTED));
    }

    public function addStatusFailed(): void
    {
        $this->addStatus(new PaymentStatus(PaymentStatus::STATUS_FAILED));
    }
}