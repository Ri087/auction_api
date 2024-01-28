<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class RegisterController extends AbstractController
{

    #[Route('/api/register', name: 'app.register', methods: ['POST'])]
    public function register(UserPasswordHasherInterface $userPasswordHasher, Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        // Hacher le mot de passe
        $hashedPassword = $userPasswordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);

        $user->setRoles(["ROLE_USER"]);

        $entityManager->persist($user);
        $entityManager->flush();

        // Serializer l'utilisateur pour la réponse
        $jsonUser = $serializer->serialize($user, 'json');

        return new JsonResponse($jsonUser, JsonResponse::HTTP_CREATED, [], true);
    }
}
