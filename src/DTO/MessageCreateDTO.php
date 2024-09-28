<?php

namespace App\DTO;

use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Validator\Constraints as Assert;
class MessageCreateDTO
{
    public function __construct(
        #[Assert\Type('string')]
        #[Assert\NotBlank]
        public readonly ?string $content,

        #[Assert\NotBlank]
        #[Assert\Type('integer')]
        public readonly ?int $conversationId,
    )
    {
    }
}