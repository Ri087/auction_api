<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app.register.index')]
    public function index(): void
    {
    }

    #[Route('/api/register', name: 'app.register', methods: ['POST'])]
    public function Register(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {

        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        $user->setRoles(["ROLE_USER"]);

        $entityManager->persist($user);
        $entityManager->flush();

        // dd($user);

        $jsonAuction = $serializer->serialize($user, 'json');

        dd($jsonAuction);

        return new JsonResponse($jsonAuction, JsonResponse::HTTP_CREATED);
    }
}
