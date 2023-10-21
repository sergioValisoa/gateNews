<?php

namespace App\Entity;

use App\Repository\GnCountryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


/**
 * @ORM\Entity(repositoryClass=GnCountryRepository::class)
 * @Vich\Uploadable
 */
class GnCountry
{
    
     /**
    * @Vich\UploadableField(mapping="pays", fileNameProperty="imageFlag")
    *
    * @var File|null
    */
    private $imageFile;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $countryName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $countryIsocode;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=GnUser::class, mappedBy="gnCountry")
     */
    private $user;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $imageFlag;

    public function __construct()
    {
        $this->user = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

     public function getCountryName(): ?string
    {
        return $this->countryName;
    }

    public function setCountryName(string $countryName): self
    {
        $this->countryName = $countryName;

        return $this;
    }

    public function getCountryIsocode(): ?string
    {
        return $this->countryIsocode;
    }

    public function setCountryIsocode(?string $countryIsocode): self
    {
        $this->countryIsocode = $countryIsocode;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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
            $user->setGnCountry($this);
        }

        return $this;
    }

    public function removeUser(GnUser $user): self
    {
        if ($this->user->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getGnCountry() === $this) {
                $user->setGnCountry(null);
            }
        }

        return $this;
    }

    public function getImageFlag(): ?string
    {
        return $this->imageFlag;
    }

    public function setImageFlag(?string $imageFlag): self
    {
        $this->imageFlag = $imageFlag;

        return $this;
    }

    /**
    * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
    */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;
        if($imageFile){
            $this->setUpdatedAt(new \DateTime());
        }

     
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }
}
