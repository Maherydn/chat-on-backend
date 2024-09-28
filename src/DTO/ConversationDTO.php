<?php

namespace App\DTO;

use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Validator\Constraints as Assert;
class ConversationDTO
{
    public function __construct(
        #[Assert\Type('string')]
        public readonly ?string $title,

        #[Assert\NotBlank]
        #[Assert\Type('array')]
        public readonly ?array $users,

    )
    {
    }
}