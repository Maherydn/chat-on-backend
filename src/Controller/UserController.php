<?php

namespace App\Controller;

use App\DTO\UserCreateDTO;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/user', name: 'user.')]
class UserController extends AbstractController
{
    #[Route( '/create', name: '.create', methods: ['POST'])]
    public function createUser(
        #[MapRequestPayload] UserCreateDTO $userCreateDTO,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
    ): Response {
        $user = new User();

        $userProps = [
            'username' => 'setUsername',
//            'email' => 'setEmail',
            'roles' => 'setRoles',
        ];

        foreach ($userProps as $prop => $setter) {
            $value = $userCreateDTO->$prop;

            if ($value !== null && $value !== '') {
                if ($prop === 'roles' && !is_array($value)) {
                    return $this->json(['error' => 'Le champ roles doit être un tableau.'], Response::HTTP_BAD_REQUEST);
                }
                $user->$setter($value);
            }
        }

        // Pour mdp
        if (isset($userCreateDTO->password) && !empty($userCreateDTO->password)) {
            $hashedPassword = $passwordHasher->hashPassword($user, $userCreateDTO->password);
            $user->setPassword($hashedPassword);
        }

        $em->persist($user);
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
}
