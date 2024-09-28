<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin/user', name: 'admin.user')]
class UserController extends AbstractController
{
    #[Route('/{id}', name: '.delete', requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])]
    public function deleteUser(User $user, EntityManagerInterface $em): Response
    {
        $em->remove($user);
        $em->flush();

        return $this->json(
            $user,
            Response::HTTP_OK,
            [],
            [
                'groups' => ['admin.show']
            ]
        );
    }
}
