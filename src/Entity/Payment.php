<?php
/**
 * @author: adavydov
 * @since: 09.11.2020
 */

namespace App\Entity;

use App\Payment\PaymentBackRoute;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * Когда понадобится ввести услуги, такие как конкретные тесты и наборы тестов, для этого
 * можно сделать отдельные структуры данных, которые будут связаны с Payment, но не наоборот
 * @author: adavydov
 * @since: 9.11.2020
 */
#[ORM\Table]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Payment
{
    /**
     * @var int
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    /**
     * @var Service
     */
    #[ORM\ManyToOne(targetEntity: 'Service')]
    #[ORM\JoinColumn(name: 'service_id', nullable: false)]
    private $service;

    /**
     * @var PaymentStatus
     */
    #[ORM\OneToMany(targetEntity: 'PaymentStatus', mappedBy: 'payment', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'payment_id')]
    private $statuses;

    #[ORM\Column(type: 'integer', nullable: false)]
    private $sum;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $backRoute = null;

    #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => 0])]
    private bool $testMode = false;

    #[ORM\Column(type: 'datetime', name: 'created_at', nullable: false)]
    private $createdAt;

    public function __construct()
    {
        $this->statuses = new ArrayCollection();
    }

    public static function init(Service $service, int $sum, bool $testMode = false): Payment
    {
        $payment = new self();
        $payment->service = $service;
        $payment->sum = $sum;
        $payment->testMode = $testMode;
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
     * @param mixed $sum
     */
    public function setSum($sum): void
    {
        $this->sum = $sum;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getBackRoute(): PaymentBackRoute
    {
        return new PaymentBackRoute($this->backRoute);
    }

    public function setBackRoute(PaymentBackRoute $backRoute)
    {
        $this->backRoute = $backRoute->getValue();
    }

    /**
     * @return Service
     */
    public function getService(): Service
    {
        return $this->service;
    }

    #[ORM\PrePersist]
    public function prePersist()
    {
        $this->createdAt = new \DateTime();
    }

    public function isExecuted(): bool
    {
        $criteria = Criteria::create()->where(PaymentStatus::criteriaExecuted());
        return $this->statuses->matching($criteria)->count() > 0;
    }

    public function isTestMode(): bool
    {
        return $this->testMode;
    }

    public function addStatusPending(): void
    {
        $this->addStatus(new PaymentStatus(PaymentStatus::STATUS_PENDING));
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

    public function addStatusExecuted(): void
    {
        $this->addStatus(new PaymentStatus(PaymentStatus::STATUS_EXECUTED));
    }

    public function addStatusFailed(): void
    {
        $this->addStatus(new PaymentStatus(PaymentStatus::STATUS_FAILED));
    }
}