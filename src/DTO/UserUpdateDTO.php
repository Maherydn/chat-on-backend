<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UserUpdateDTO
{
    public function __construct(
        #[Assert\Type('string')]
        public readonly ?string $username,

        #[Assert\Type('array')]
        public readonly ?array $roles,

        public readonly ?string $password,

        public readonly ?string $email,

    )
    {
    }
}