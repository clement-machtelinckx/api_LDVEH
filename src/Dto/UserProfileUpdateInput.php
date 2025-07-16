<?php
// src/Dto/UserProfileUpdateInput.php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UserProfileUpdateInput
{

    #[Assert\Email]
    public string $email;

    #[Assert\Length(min: 8)]
    public ?string $newPassword = null;
}
