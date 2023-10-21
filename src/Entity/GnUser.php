<?php

namespace App\Entity;

use App\Repository\GnUserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=GnUserRepository::class)
 * @ORM\Table(name="gn_users")
 * @Vich\Uploadable
 * @method string getUserIdentifier()
 */
class GnUser implements UserInterface
{

    /**
    * @Vich\UploadableField(mapping="avatars", fileNameProperty="usrPhoto")
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
    private $userName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $userFullname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $userAdress;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $userPassword;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $userBirthdate;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $confirmAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updateAt;

    /**
     * @ORM\OneToMany(targetEntity=GnPost::class, mappedBy="user", orphanRemoval=true)
     */
    private $post;

    /**
     * @ORM\OneToMany(targetEntity=GnPostComment::class, mappedBy="user", orphanRemoval=true)
     */
    private $postComments;

    /**
     * @ORM\ManyToOne(targetEntity=GnCountry::class, inversedBy="user")
     */
    private $gnCountry;

    /**
     * @ORM\ManyToOne(targetEntity=GnSubscription::class, inversedBy="user")
     */
    private $gnSubscription;

    /**
     * @ORM\ManyToMany(targetEntity=GnPage::class, mappedBy="user")
     */
    private $gnPages;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $userEmail;

    /**
     * @ORM\Column(type="boolean", options={"default":"0"})
     */
    private $isDeleted;

    /**
     * @ORM\ManyToMany(targetEntity=GnRole::class, inversedBy="gnUsers")
     */
    private $gnRoles;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $usrPhoto;

    
     public function __toString() {
        return $this->userName;
    }
    public function __construct()
    {
        $this->post = new ArrayCollection();
        $this->postComments = new ArrayCollection();
        $this->gnPages = new ArrayCollection();
        $this->setIsDeleted(0);
        $this->gnRoles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    public function getUserFullname(): ?string
    {
        return $this->userFullname;
    }

    public function setUserFullname(?string $userFullname): self
    {
        $this->userFullname = $userFullname;

        return $this;
    }

    public function getUserAdress(): ?string
    {
        return $this->userAdress;
    }

    public function setUserAdress(?string $userAdress): self
    {
        $this->userAdress = $userAdress;

        return $this;
    }

    public function getUserPassword(): ?string
    {
        return $this->userPassword;
    }

    public function setUserPassword(string $userPassword): self
    {
        $this->userPassword = $userPassword;

        return $this;
    }

    public function getUserBirthdate(): ?string
    {
        return $this->userBirthdate;
    }

    public function setUserBirthdate(?string $userBirthdate): self
    {
        $this->userBirthdate = $userBirthdate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getConfirmAt(): ?\DateTimeInterface
    {
        return $this->confirmAt;
    }

    public function setConfirmAt(?\DateTimeInterface $confirmAt): self
    {
        $this->confirmAt = $confirmAt;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->updateAt;
    }

    public function setUpdateAt(?\DateTimeInterface $updateAt): self
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    /**
     * @return Collection|GnPost[]
     */
    public function getPost(): Collection
    {
        return $this->post;
    }

    public function addPost(GnPost $post): self
    {
        if (!$this->post->contains($post)) {
            $this->post[] = $post;
            $post->setUser($this);
        }

        return $this;
    }

    public function removePost(GnPost $post): self
    {
        if ($this->post->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getUser() === $this) {
                $post->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PostComment[]
     */
    public function getPostComments(): Collection
    {
        return $this->postComments;
    }

    public function addPostComment(GnPostComment $postComment): self
    {
        if (!$this->postComments->contains($postComment)) {
            $this->postComments[] = $postComment;
            $postComment->setUser($this);
        }

        return $this;
    }

    public function removePostComment(GnPostComment $postComment): self
    {
        if ($this->postComments->removeElement($postComment)) {
            // set the owning side to null (unless already changed)
            if ($postComment->getUser() === $this) {
                $postComment->setUser(null);
            }
        }

        return $this;
    }

    public function getGnCountry(): ?GnCountry
    {
        return $this->gnCountry;
    }

    public function setGnCountry(?GnCountry $gnCountry): self
    {
        $this->gnCountry = $gnCountry;

        return $this;
    }

    public function getGnSubscription(): ?GnSubscription
    {
        return $this->gnSubscription;
    }

    public function setGnSubscription(?GnSubscription $gnSubscription): self
    {
        $this->gnSubscription = $gnSubscription;

        return $this;
    }

    /**
     * @return Collection|GnPage[]
     */
    public function getGnPages(): Collection
    {
        return $this->gnPages;
    }

    public function addGnPage(GnPage $gnPage): self
    {
        if (!$this->gnPages->contains($gnPage)) {
            $this->gnPages[] = $gnPage;
            $gnPage->addUser($this);
        }

        return $this;
    }

    public function removeGnPage(GnPage $gnPage): self
    {
        if ($this->gnPages->removeElement($gnPage)) {
            $gnPage->removeUser($this);
        }

        return $this;
    }

    /**
     *@see UserInteface
     */
     public function getRoles(){
         $userRoles = $this->getGnRoles();
         $roles = [];
         foreach ($userRoles as $userRole){
            $roles[] = $userRole->getRoleName();
         }
        return array_unique($roles);
     }

    /**
     *@see UserInteface
     */
    public function getPassword(){
        return $this->userPassword;
    }

    /**
     * Returns the salt that was originally used to hash the password.
     *
     * This can return null if the password was not hashed using a salt.
     *
     * This method is deprecated since Symfony 5.3, implement it from {@link LegacyPasswordAuthenticatedUserInterface} instead.
     *
     * @return string|null The salt
     */
    public function getSalt(){

    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials(){

    }

    public function getUserEmail(): ?string
    {
        return $this->userEmail;
    }

    public function setUserEmail(string $userEmail): self
    {
        $this->userEmail = $userEmail;

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

    /**
     * @return Collection|GnRole[]
     */
    public function getGnRoles(): Collection
    {
        return $this->gnRoles;
    }

    public function addGnRole(GnRole $gnRole): self
    {
        if (!$this->gnRoles->contains($gnRole)) {
            $this->gnRoles[] = $gnRole;
        }

        return $this;
    }

    public function removeGnRole(GnRole $gnRole): self
    {
        $this->gnRoles->removeElement($gnRole);

        return $this;
    }

    public function getUsrPhoto(): ?string
    {
        return $this->usrPhoto;
    }

    public function setUsrPhoto(?string $usrPhoto): self
    {
        $this->usrPhoto = $usrPhoto;

        return $this;
    }

    /**
    * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
    */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;
        if($imageFile){
            $this->setUpdateAt(new \DateTime());
        }

     
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

}
