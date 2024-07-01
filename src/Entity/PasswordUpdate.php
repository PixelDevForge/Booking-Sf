<?php

namespace App\Entity;

use App\Repository\PasswordUpdateRepository;
use Symfony\Component\Validator\Constraints as Assert;

class PasswordUpdate
{
        
    private ?string $oldPassword = null;

    #[Assert\Length(
        min: 8,
        minMessage: "Merci de mettre au moins 8 caractères."
    )]
    private ?string $newPassword = null;

    #[Assert\EqualTo(propertyPath:"newPassword", message:"les mot de passe ne correspondent pas")]
    private ?string $confirmPassword = null;

  
    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(?string $oldPassword): static
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(?string $newPassword): static
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(?string $confirmPassword): static
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }
}
