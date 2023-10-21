<?php

namespace App\Entity;

use App\Repository\GnPageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GnPageRepository::class)
 * @ORM\Table(name="gn_pages")
 */
class GnPage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $pageContent;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $pageDescription;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $pageKey;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pageTemplate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $pageTitle;

    /**
     * @ORM\ManyToMany(targetEntity=GnUser::class, inversedBy="gnPages")
     */
    private $user;

    public function __construct()
    {
        $this->user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPageContent(): ?string
    {
        return $this->pageContent;
    }

    public function setPageContent(?string $pageContent): self
    {
        $this->pageContent = $pageContent;

        return $this;
    }

    public function getPageDescription(): ?string
    {
        return $this->pageDescription;
    }

    public function setPageDescription(?string $pageDescription): self
    {
        $this->pageDescription = $pageDescription;

        return $this;
    }

    public function getPageKey(): ?string
    {
        return $this->pageKey;
    }

    public function setPageKey(?string $pageKey): self
    {
        $this->pageKey = $pageKey;

        return $this;
    }

    public function getPageTemplate(): ?string
    {
        return $this->pageTemplate;
    }

    public function setPageTemplate(?string $pageTemplate): self
    {
        $this->pageTemplate = $pageTemplate;

        return $this;
    }

    public function getPageTitle(): ?string
    {
        return $this->pageTitle;
    }

    public function setPageTitle(?string $pageTitle): self
    {
        $this->pageTitle = $pageTitle;

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
        }

        return $this;
    }

    public function removeUser(GnUser $user): self
    {
        $this->user->removeElement($user);

        return $this;
    }
}
