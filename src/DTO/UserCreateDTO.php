<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UserCreateDTO
{
    public function __construct(
        #[Assert\Type('string')]
        #[Assert\NotBlank]
        public readonly ?string $username,

//        #[Assert\NotBlank]
        #[Assert\Type('array')]
        public readonly ?array $roles,

        #[Assert\NotBlank]
        public readonly ?string $password,

//        #[Assert\NotBlank]
        public readonly ?string $email,

    )
    {
    }
}

