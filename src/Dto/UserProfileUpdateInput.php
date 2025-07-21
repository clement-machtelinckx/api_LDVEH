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

    #[Assert\Length(min: 2, max: 100)]
    public ?string $firstname = null;

    #[Assert\Length(min: 2, max: 100)]
    public ?string $lastname = null;

    #[Assert\Length(min: 2, max: 100)]
    public ?string $nickname = null;

    #[Assert\Choice(choices: ['male', 'female', 'other'])]
    public ?string $gender = null;

    #[Assert\Range(min: 6, max: 130)]
    public ?int $age = null;
}
