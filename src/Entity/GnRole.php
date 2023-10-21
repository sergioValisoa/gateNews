<?php

namespace App\Entity;

use App\Repository\GnRoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GnRoleRepository::class)
 * @ORM\Table(name="gn_user_role")
 */
class GnRole
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $roleName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $roleDescription;

    /**
     * @ORM\OneToMany(targetEntity=GnUser::class, mappedBy="gnRole", orphanRemoval=true)
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity=GnUser::class, mappedBy="gnRoles")
     */
    private $gnUsers;

    

    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->gnUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoleName(): ?string
    {
        return $this->roleName;
    }

    public function setRoleName(string $roleName): self
    {
        $this->roleName = $roleName;

        return $this;
    }

    public function getRoleDescription(): ?string
    {
        return $this->roleDescription;
    }

    public function setRoleDescription(?string $roleDescription): self
    {
        $this->roleDescription = $roleDescription;

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
            $user->addGnRole($this);
        }

        return $this;
    }

    public function removeUser(GnUser $user): self
    {
        if ($this->user->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getGnRoles() === $this) {
                $user->addGnRole(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|GnUser[]
     */
    public function getGnUsers(): Collection
    {
        return $this->gnUsers;
    }

    public function addGnUser(GnUser $gnUser): self
    {
        if (!$this->gnUsers->contains($gnUser)) {
            $this->gnUsers[] = $gnUser;
            $gnUser->addGnRole($this);
        }

        return $this;
    }

    public function removeGnUser(GnUser $gnUser): self
    {
        if ($this->gnUsers->removeElement($gnUser)) {
            $gnUser->removeGnRole($this);
        }

        return $this;
    }

    public function getGnSubscription(): ?GnSubscription
    {
        return $this->gnSubscription;
    }

    public function setGnSubscription(?GnSubscription $gnSubscription): self
    {
        // unset the owning side of the relation if necessary
        if ($gnSubscription === null && $this->gnSubscription !== null) {
            $this->gnSubscription->setSubscriptionRole(null);
        }

        // set the owning side of the relation if necessary
        if ($gnSubscription !== null && $gnSubscription->getSubscriptionRole() !== $this) {
            $gnSubscription->setSubscriptionRole($this);
        }

        $this->gnSubscription = $gnSubscription;

        return $this;
    }
}
