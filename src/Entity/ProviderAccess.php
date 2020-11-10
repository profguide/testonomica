<?php
/**
 * @author: adavydov
 * @since: 09.11.2020
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(indexes={@ORM\Index(columns={"token"})})
 * @author: adavydov
 * @since: 9.11.2020
 */
class ProviderAccess
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32)
     * @var string
     */
    private $token;

    /**
     * @var Provider
     * @ORM\OneToOne(targetEntity="Provider")
     * @ORM\JoinColumn(name="provider_id")
     */
    private $provider;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", name="used_at", nullable=true)
     */
    private $usedAt;
}