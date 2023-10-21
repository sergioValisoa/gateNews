<?php

namespace App\Entity;

use App\Repository\GnPostCommentRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GnPostCommentRepository::class)
 * @ORM\Table(name="gn_post_comments")
 */
class GnPostComment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $postCommentContent;

    /**
     * @ORM\ManyToOne(targetEntity=GnUser::class, inversedBy="gnPostComments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=GnPost::class, inversedBy="gnPostComments")
     */
    private $post;

    /**
     * @ORM\Column(type="boolean", options={"default":"0"})
     */
    private $isApprouved;

    /**
     * @ORM\Column(type="boolean", options={"default":"0"})
     */
    private $isDeleted;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPostCommentContent(): ?string
    {
        return $this->postCommentContent;
    }

    public function setPostCommentContent(string $postCommentContent): self
    {
        $this->postCommentContent = $postCommentContent;

        return $this;
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

    public function getPost(): ?GnPost
    {
        return $this->post;
    }

    public function setPost(?GnPost $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getIsApprouved(): ?bool
    {
        return $this->isApprouved;
    }

    public function setIsApprouved(?bool $isApprouved): self
    {
        $this->isApprouved = $isApprouved;

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
}
