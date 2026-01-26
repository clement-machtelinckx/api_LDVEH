<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class RefreshTokenRequest
{
    #[Assert\NotBlank(message: 'Refresh token is required')]
    #[Assert\Type('string')]
    #[Assert\Length(min: 20, minMessage: 'Refresh token must be at least {{ limit }} characters long')]
    private ?string $refreshToken = null;

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(?string $refreshToken): void
    {
        $this->refreshToken = trim($refreshToken ?? '');
    }
}
