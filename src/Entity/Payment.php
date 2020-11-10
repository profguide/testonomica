<?php
/**
 * @author: adavydov
 * @since: 09.11.2020
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Когда понадобится ввести услуги, такие как конкретные тесты и наборы тестов, для этого
 * можно сделать отдельные структуры данных, которые будут связаны с Payment, но не наоборот
 * @ORM\Entity
 * @ORM\Table
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
     * @var PaymentStatus
     * @ORM\OneToMany(targetEntity="PaymentStatus", mappedBy="payment")
     * @ORM\JoinColumn(name="payment_id")
     */
    private $statuses;

    /**
     * @ORM\Column(type="integer")
     */
    private $sum;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    private $createdAt;
}