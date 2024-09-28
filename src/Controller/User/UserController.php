<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\DTO\UserCreateDTO;
use App\DTO\UserUpdateDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/api/user', name: 'api.user.')]
class UserController extends AbstractController
{
    #[Route('', name: '.index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $user = $userRepository->findAll();
        return $this->json(
            $user,
            Response::HTTP_OK,
            [],
            [
                'groups' => ['user.show']
            ]
        );
    }

    #[Route('/{id}', name: '.show', requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function showUser(User $user): Response
    {
        return $this->json(
            $user,
            Response::HTTP_OK,
            [],
            [
            ]
        );
    }

    #[Route('/{id}', name: '.update', requirements: ['id' => Requirement::DIGITS], methods: ['PUT'])]
    public function updateUser(
        User $user,
        #[MapRequestPayload] UserUpdateDTO $userUpdateDTO,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
    ): Response {
        // Mapping des champs à mettre à jour
        $userProps = [
            'username' => 'setUsername',
            'email' => 'setEmail',
            'roles' => 'setRoles',
        ];

        foreach ($userProps as $prop => $setter) {
            $value = $userUpdateDTO->$prop;

            if ($value !== null && $value !== '') {
                if ($prop === 'roles' && !is_array($value)) {
                    return $this->json(['error' => 'Le champ roles doit être un tableau.'], Response::HTTP_BAD_REQUEST);
                }
                    $user->$setter($value);
            }
        }

        // Pour mdp
        if (isset($userUpdateDTO->password) && !empty($userUpdateDTO->password)) {
            $hashedPassword = $passwordHasher->hashPassword($user, $userUpdateDTO->password);
            $user->setPassword($hashedPassword);
        }

        $em->flush();

        return $this->json(
            $user,
            Response::HTTP_OK,
            [],
            [
                'groups' => ['user.show']
            ]
        );
    }

    #[Route('/me', name: '.me', methods: ['GET'])]
    public function me(): Response
    {
        $user = $this->getUser();
        return $this->json(
            $user,
            Response::HTTP_OK,
            [],
            [
                'groups' => ['user.me']
            ]
        );
    }
}
