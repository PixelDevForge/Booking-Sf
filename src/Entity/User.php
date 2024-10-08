<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(
    fields: ["email"],
    message: "Email déjà enregistré !"
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    #[Assert\Email(message:"Renseigner un email valide !")]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Url()]
    private ?string $avatar = null;

    #[ORM\Column(length: 255)]
    private ?string $hash= null;
    
    #[Assert\EqualTo(propertyPath:"hash", message:"les mot de passe ne correspondent pas")]
    public $passwordConfirm;

    #[ORM\Column(length: 255)]
    private ?string $introduction = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(
        min: 10,
        minMessage: "Merci de mettre au moins 10 caractères."
    )]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 10,
        minMessage: "Merci de mettre au moins 10 caractères."
    )]
    private ?string $slug = null;

    /**
     * @var Collection<int, Ad>
     */
    #[ORM\OneToMany(targetEntity: Ad::class, mappedBy: 'author', orphanRemoval: true)]
    private Collection $ads;

    /**
     * @var Collection<int, Role>
     */
    #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: 'users')]
    private Collection $userRoles;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'booker')]
    private Collection $bookings;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'author', orphanRemoval: true)]
    private Collection $comments;

    

    public function getFullName(){
        return $this->firstname.' '.$this->lastname;

    }

    public function __construct()
    {
        $this->ads = new ArrayCollection();
        $this->userRoles = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->comments = new ArrayCollection();
   
    }

        /*
    Creation d'une fonction d'init du slug avant la persistance et avant la MAJ 
    */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]

    public function initSlug(): void
    {
        if (empty($this->slug)) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->firstname.' '.$this->lastname);
        }
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): static
    {
        $this->hash = $hash;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): static
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Ad>
     */
    public function getAds(): Collection
    {
        return $this->ads;
    }

    public function addAd(Ad $ad): static
    {
        if (!$this->ads->contains($ad)) {
            $this->ads->add($ad);
            $ad->setAuthor($this);
        }

        return $this;
    }

    public function removeAd(Ad $ad): static
    {
        if ($this->ads->removeElement($ad)) {
            // set the owning side to null (unless already changed)
            if ($ad->getAuthor() === $this) {
                $ad->setAuthor(null);
            }
        }

        return $this;
    }

    public function getRoles():array
    {
        $roles = $this->userRoles->map(function($role){
            return $role->getTitle();

        })->toArray();

        $roles[]='ROLE_USER';

        return $roles;
    }  
    public function getPassword(): ?string
    {
        return $this->hash;
    }  
    public function getSalt(){}  
    public function getUsername():string
    {
        return $this->email;
    }  
    public function eraseCredentials():void
    {
    
    }  
    public function getUserIdentifier(): string
    {
        return (string) $this->getEmail();
    }

    /**
     * @return Collection<int, Role>
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function addUserRole(Role $userRole): static
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles->add($userRole);
            $userRole->addUser($this);
        }

        return $this;
    }

    public function removeUserRole(Role $userRole): static
    {
        if ($this->userRoles->removeElement($userRole)) {
            $userRole->removeUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setBooker($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getBooker() === $this) {
                $booking->setBooker(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

   
    
}
