<?php

namespace App\Entity;

use App\Repository\GnSubscriptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GnSubscriptionRepository::class)
 * @ORM\Table(name="gn_subscriptions")
 */
class GnSubscription
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $subscriptionName;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $subscriptionPrice;

    /**
     * @ORM\OneToMany(targetEntity=GnUser::class, mappedBy="gnSubscription")
     */
    private $user;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $SubscriptionDescription;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $SubscriptionDelay;

    /**
     * @ORM\ManyToOne(targetEntity=GnRole::class)
     */
    private $subscriptionRole;

 

    public function __construct()
    {
        $this->user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubscriptionName(): ?string
    {
        return $this->subscriptionName;
    }

    public function setSubscriptionName(string $subscriptionName): self
    {
        $this->subscriptionName = $subscriptionName;

        return $this;
    }

    public function getSubscriptionPrice(): ?float
    {
        return $this->subscriptionPrice;
    }

    public function setSubscriptionPrice(?float $subscriptionPrice): self
    {
        $this->subscriptionPrice = $subscriptionPrice;

        return $this;
    }

    /**
     * @return Collection|GnUser[]
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(GnUser $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
            $user->setGnSubscription($this);
        }

        return $this;
    }

    public function removeUser(GnUser $user): self
    {
        if ($this->user->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getGnSubscription() === $this) {
                $user->setGnSubscription(null);
            }
        }

        return $this;
    }

    public function getSubscriptionDescription(): ?string
    {
        return $this->SubscriptionDescription;
    }

    public function setSubscriptionDescription(?string $SubscriptionDescription): self
    {
        $this->SubscriptionDescription = $SubscriptionDescription;

        return $this;
    }

    public function getSubscriptionDelay(): ?int
    {
        return $this->SubscriptionDelay;
    }

    public function setSubscriptionDelay(?int $SubscriptionDelay): self
    {
        $this->SubscriptionDelay = $SubscriptionDelay;

        return $this;
    }

    public function getSubscriptionRole(): ?GnRole
    {
        return $this->subscriptionRole;
    }

    public function setSubscriptionRole(?GnRole $subscriptionRole): self
    {
        $this->subscriptionRole = $subscriptionRole;

        return $this;
    }
}
