<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\DTO\ConversationDTO;
use App\Entity\Conversation;
use App\Entity\User;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/user/conversation', name: 'user.conversation')]
class ConversationController extends AbstractController
{
    #[Route( '/create', name: '.create', methods: ['POST'])]
    public function createUser(
        #[MapRequestPayload] ConversationDTO $conversationDTO,
        EntityManagerInterface $em,
        UserRepository $userRepository
    ): Response {
        $conversation = new Conversation();


        $conversationProps = [
            'title' => 'setTitle',
            'users' => 'addUser'
        ];

        foreach ($conversationProps as $prop => $setter) {
            $value = $conversationDTO->$prop;

            if ($value !== null && $value !== '') {
                if ($prop === 'users') {
                    // On suppose que $value est un tableau d'IDs ou de participants
                    foreach ($value as $participantId) {
                        $participant = $userRepository->find($participantId);

                        if ($participant) {
                            // On appelle le setter pour ajouter le participant
                            $conversation->$setter($participant);

                        }
                    }
                }else{
                    $conversation->$setter($value);
                }

            }
        }

        $conversation->setCreatedAt(new \DateTimeImmutable());

        $em->persist($conversation);
        $em->flush();

        return $this->json(
            $conversation,
            Response::HTTP_OK,
            [],
            [
                'groups' => ['user.conversation']
            ]
        );
    }

    public function getConversationTitle(Conversation $conversation, User $connectUser): string
    {
        $participants = $conversation->getUsers();

//        if(count($participants) > 2) {
//            if ($conversation->getTitle()){
//                return $conversation->getTitle();
//            }
//            return "Conversation de groupe .";
//        }

        if ($conversation->getTitle()){
            return $conversation->getTitle();
        }

        foreach ($participants as $participant) {
            if ($participant->getId() === $connectUser->getId()) {
                return $participant->getUsername();
            }
        }
        return "Aucun autre participants. ";
    }

    #[Route('', name: '.reads', methods: ['GET'])]
    public function readConversations(MessageRepository $messageRepository, UserRepository $userRepository): Response
    {
        $connectUser = $userRepository->find(2);
//        $connectUser = $this->getUser();

        $conversations = $connectUser->getConversations();

        $conversationData = [];

        foreach ($conversations as $conversation) {
            $title = $this->getConversationTitle($conversation, $connectUser);
            $conversationId = $conversation->getId();
            $lastMessage =  $messageRepository->findLastMessageByConversation($conversationId);
            $participants = $conversation->getUsers();
            $isGroup = false;
            if(count($participants) > 2) {
                $isGroup = true;
            }

            $conversationData[] = [
                'id' => $conversationId,
                'title' => $title,
                'lastMessage' => $lastMessage,
                'isGroups' => $isGroup
            ];
        }
        return $this->json(
            $conversationData,
            Response::HTTP_OK,
            [],
            [
                'groups' => ['user.conversation']
            ]
        );
    }

    #[Route('/{id}', name: '.read', requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function readConversation(Conversation $conversation): Response
    {
        $messages = $conversation->getMessages();
        return $this->json(
            $messages,
            Response::HTTP_OK,
            [],
            [
                'groups' => ['user.conversation']
            ]
        );
    }
}
