<?php

namespace App\Entity;

use App\Repository\GnPostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


/**
 * @ORM\Entity(repositoryClass=GnPostRepository::class)
 * @ORM\Table(name="gn_posts")
 * @UniqueEntity("postUrl")
 * @Vich\Uploadable
 */
class GnPost
{

    /**
     * @Vich\UploadableField(mapping="images", fileNameProperty="postPhotos")
     *
     * @var File|null
     */
    private $imageFile;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GnPostImages", mappedBy="post")
     */
    private $postImages;

    /**
     * @Vich\UploadableField(mapping="videos", fileNameProperty="postVideo")
     *
     * @var File|null
     */
    private $videoFile;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $postContent;

    /**
     * @ORM\Column(type="text")
     */
    private $postTitle;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $postCreatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $postPhotos;

    /**
     * @ORM\ManyToOne(targetEntity=GnUser::class, inversedBy="post")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity=GnPostCategory::class, inversedBy="gnPosts")
     * @ORM\JoinTable(name="gn_post_category_post")
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity=GnPostComment::class, mappedBy="post")
     */
    private $postComments;

    /**
     * @ORM\Column(type="boolean", options={"default": "0"})
     */
    private $isApprouved;

    /**
     * @ORM\Column(type="boolean", options={"default": "0"})
     */
    private $isDeleted;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $PostDateGmt;

    /**
     * @ORM\Column(type="boolean", nullable=true,options={"default": "0"})
     */
    private $isCommented;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAtGmt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $postVideo;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $metaKey;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $metaDescription;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $postUrl;


    public function __construct()
    {
        $this->postComments = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->postImages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPostContent(): ?string
    {
        return $this->postContent;
    }

    public function setPostContent(string $postContent): self
    {
        $this->postContent = $postContent;

        return $this;
    }

    public function getPostTitle(): ?string
    {
        return $this->postTitle;
    }

    public function setPostTitle(string $postTitle): self
    {
        $this->postTitle = $postTitle;

        return $this;
    }

    public function getPostCreatedAt(): ?\DateTime
    {
        return $this->postCreatedAt;
    }

    public function setPostCreatedAt(\DateTime $postCreatedAt): self
    {
        $this->postCreatedAt = $postCreatedAt;

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

    public function getPostPhotos(): ?string
    {
        return $this->postPhotos;
    }

    /**
     * @param null|File $postPhotos
     * @return Property
     */
    public function setPostPhotos(?string $postPhotos): self
    {
        $this->postPhotos = $postPhotos;

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

    /**
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $videoFile
     */
    public function setVideoFile(?File $videoFile = null): void
    {
        $this->videoFile = $videoFile;
        if($videoFile){
            $this->setUpdatedAt(new \DateTime());
        }
    }

    public function getVideoFile(): ?File
    {
        return $this->videoFile;
    }

    public function getUser(): ?GnUser
    {
        return $this->user;
    }

    public function setUser(?GnUser $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|GnPostComment[]
     */
    public function getPostComments(): Collection
    {
        return $this->postComments;
    }

    public function addPostComment(GnPostComment $postComment): self
    {
        if (!$this->postComments->contains($postComment)) {
            $this->postComments[] = $postComment;
            $postComment->setPost($this);
        }

        return $this;
    }

    public function removePostComment(GnPostComment $postComment): self
    {
        if ($this->postComments->removeElement($postComment)) {
            // set the owning side to null (unless already changed)
            if ($postComment->getPost() === $this) {
                $postComment->setPost(null);
            }
        }

        return $this;
    }

    public function getIsApprouved(): ?bool
    {
        return $this->isApprouved;
    }

    public function setIsApprouved(bool $isApprouved): self
    {
        $this->isApprouved = $isApprouved;

        return $this;
    }

    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(?bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function getPostDateGmt(): ?\DateTimeInterface
    {
        return $this->PostDateGmt;
    }

    public function setPostDateGmt(\DateTimeInterface $PostDateGmt): self
    {
        $this->PostDateGmt = $PostDateGmt;

        return $this;
    }

    public function getIsCommented(): ?bool
    {
        return $this->isCommented;
    }

    public function setIsCommented(bool $isCommented): self
    {
        $this->isCommented = $isCommented;

        return $this;
    }

    public function getUpdatedAtGmt(): ?\DateTimeInterface
    {
        return $this->updatedAtGmt;
    }

    public function setUpdatedAtGmt(?\DateTimeInterface $updatedAtGmt): self
    {
        $this->updatedAtGmt = $updatedAtGmt;

        return $this;
    }

    public function getPostVideo(): ?string
    {
        return $this->postVideo;
    }

    public function setPostVideo(?string $postVideo): self
    {
        $this->postVideo = $postVideo;

        return $this;
    }

    /**
     * @return Collection|GnPostCategory[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(GnPostCategory $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }

        return $this;
    }

    public function removeCategory(GnPostCategory $category): self
    {
        $this->categories->removeElement($category);

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
     * @return mixed
     */
    public function getPostUrl()
    {
        return $this->postUrl;
    }

    /**
     * @param mixed $postUrl
     */
    public function setPostUrl($postUrl): void
    {
        $this->postUrl = $postUrl;
    }

    /**
     * @return Collection|GnPostImages[]
     */
    public function getPostImages(): Collection
    {
        return $this->postImages;
    }

    public function addPostImage(GnPostImages $postImage): self
    {
        if (!$this->postImages->contains($postImage)) {
            $this->postImages[] = $postImage;
            $postImage->setPost($this);
        }

        return $this;
    }

    public function removePostImage(GnPostImages $postImage): self
    {
        if ($this->postImages->contains($postImage)) {
            $this->postImages->removeElement($postImage);
            // set the owning side to null (unless already changed)
            if ($postImage->getPost() === $this) {
                $postImage->setPost(null);
            }
        }

        return $this;
    }
}
