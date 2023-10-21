<?php

namespace App\Entity;

use App\Repository\GnPostCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


/**
 * @ORM\Entity(repositoryClass=GnPostCategoryRepository::class)
 * @UniqueEntity("postCategoryUrl")
 * @Vich\Uploadable
 */
class GnPostCategory
{

    /**
     * @Vich\UploadableField(mapping="images_category", fileNameProperty="postCategoryPhotos")
     *
     * @var File|null
     */
    private $categoryImageFile;
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $categoryTitle;

    /**
     * @ORM\Column(type="text")
     */
    private $categoryDescription;
    
   /**
     * @ORM\ManyToMany(targetEntity=GnPost::class, mappedBy="categories")
     */
    private $gnPosts;

    /**
     * @ORM\Column(type="boolean", options={"default": "0"})
     */
    private $isDeleted;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $metaKey;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $metaDescription;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $postCategoryPhotos;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $postCategoryUrl;

    public function __toString() {
        return $this->categoryTitle;
    }

    public function __construct()
    {
        $this->isDeleted = 0;
        $this->gnPosts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoryTitle(): ?string
    {
        return $this->categoryTitle;
    }

    public function setCategoryTitle(string $categoryTitle): self
    {
        $this->categoryTitle = $categoryTitle;

        return $this;
    }

    public function getCategoryDescription(): ?string
    {
        return $this->categoryDescription;
    }

    public function setCategoryDescription(string $categoryDescription): self
    {
        $this->categoryDescription = $categoryDescription;

        return $this;
    }

    /**
     * @return Collection|GnPost[]
     */
    public function getGnPosts(): Collection
    {
        return $this->gnPosts;
    }

    public function addGnPost(GnPost $gnPost): self
    {
        if (!$this->gnPosts->contains($gnPost)) {
            $this->gnPosts[] = $gnPost;
            $gnPost->addCategory($this);
        }

        return $this;
    }

    

    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function removeGnPost(GnPost $gnPost): self
    {
        if ($this->gnPosts->removeElement($gnPost)) {
            $gnPost->removeCategory($this);
        }

        return $this;
    }

    public function getMetaKey(): ?string
    {
        return $this->metaKey;
    }

    public function setMetaKey(?string $metaKey): self
    {
        $this->metaKey = $metaKey;

        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): self
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * @return File|null
     */
    public function getCategoryImageFile(): ?File
    {
        return $this->categoryImageFile;
    }

    /**
     * @param File|null $categoryImageFile
     */
    public function setCategoryImageFile(?File $categoryImageFile): void
    {
        $this->categoryImageFile = $categoryImageFile;
    }

    /**
     * @return mixed
     */
    public function getPostCategoryPhotos()
    {
        return $this->postCategoryPhotos;
    }

    /**
     * @param mixed $postCategoryPhotos
     */
    public function setPostCategoryPhotos($postCategoryPhotos): void
    {
        $this->postCategoryPhotos = $postCategoryPhotos;
    }

    /**
     * @return mixed
     */
    public function getPostCategoryUrl()
    {
        return $this->postCategoryUrl;
    }

    /**
     * @param mixed $postCategoryUrl
     */
    public function setPostCategoryUrl($postCategoryUrl): void
    {
        $this->postCategoryUrl = $postCategoryUrl;
    }
}
