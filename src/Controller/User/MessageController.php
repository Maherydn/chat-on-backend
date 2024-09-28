<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\DTO\MessageCreateDTO;
use App\Entity\Message;
use App\Repository\ConversationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/user/message', name: 'user.message')]
class MessageController extends AbstractController
{
    #[Route( '/create', name: '.create', methods: ['POST'])]
    public function createUser(
        #[MapRequestPayload] MessageCreateDTO $messageCreateDTO,
        EntityManagerInterface $em,
        UserRepository $userRepository,
        ConversationRepository $conversationRepository
    ): Response {
        $message = new Message();

        $messageProps = [
            'content' => 'setContent',
            'conversationId' => 'setConversation'
        ];

        foreach ($messageProps as $prop => $setter) {
            $value = $messageCreateDTO->$prop;

            if ($value !== null && $value !== '') {
                if ($prop === 'conversationId') {
                    $conversation = $conversationRepository->find($value);
                    $message->$setter($conversation);
                }
                if ( $prop !== 'conversationId') {
                    $message->$setter($value);
                }
            }
        }
//        $user = $this->getUser();
        $user = $userRepository->find(2);
        $message->setSender($user);

        $message->setSendAt(new \DateTimeImmutable());

        $em->persist($message);
        $em->flush();

        return $this->json(
            $message,
            Response::HTTP_OK,
            [],
            [
                'groups' => ['user.message']
            ]
        );
    }

    #[Route('/{id}', name: '.read', requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function readMessage(Message $message): Response
    {
        return $this->json(
            $message,
            Response::HTTP_OK,
            [],
            [
                'groups' => ['user.message']
            ]
        );
    }

    #[Route('/{id}', name: '.delete', requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])]
    public function deleteMessage(Message $message): Response
    {
        return $this->json(
            $message,
            Response::HTTP_OK,
            [],
            [
                'groups' => ['user.message']
            ]
        );
    }
}
