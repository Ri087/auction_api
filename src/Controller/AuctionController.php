<?php

namespace App\Controller;

use App\Entity\Auction;
use App\Repository\AuctionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuctionController extends AbstractController
{
    #[Route('/auction', name: 'app_auction')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/AuctionController.php',
        ]);
    }

    #[Route('/api/auction', name: 'auction.getAll', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN', message: 'Only the owner can access this resource')]

    public function getAllAuction(AuctionRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $auction = $repository->findAll();
        $jsonAuction = $serializer->serialize($auction, 'json', ['groups' => 'getAllAuction']);
        return new JsonResponse($jsonAuction, Response::HTTP_OK, [], true);
    }

    #[Route('/api/auction/{auction}', name: 'auction.get', methods: ['GET'])]
    public function getAuction(Auction $auction, SerializerInterface $serializer): JsonResponse
    {
        $jsonAuction = $serializer->serialize($auction, 'json', ['groups' => 'getAllAuction']);
        return new JsonResponse($jsonAuction, Response::HTTP_OK, ['accept' => 'json'], true);
    }


    #[Route('/api/auction', name: "auction.create", methods: ['POST'])]
    public function createAuction(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): JsonResponse
    {

        $auction = $serializer->deserialize($request->getContent(), Auction::class, 'json');
        $auction->setCreatedAt(new \DateTimeImmutable());
        $auction->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->persist($auction);
        $entityManager->flush();


        $jsonAuction = $serializer->serialize($auction, 'json');

        $location = $urlGenerator->generate('auction.get', ['auction' => $auction->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonAuction, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/auction/{auction}', name: 'auction.delete', methods: ['DELETE'])]
    public function deleteAuction(Auction $auction, EntityManagerInterface $entityManager): JsonResponse
    {
        $auction->setUpdatedAt(new \DateTimeImmutable());
        $auction->setStatus('DELETE');

        $entityManager->persist($auction);

        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/auction/{auction}', name: 'auction.update', methods: ['PATCH', "PUT"])]
    public function updateAuction(Auction $auction, ValidatorInterface $validator, EntityManagerInterface $entityManager, SerializerInterface $serializer, Request $request): JsonResponse
    {
        $auction = $serializer->deserialize($request->getContent(), Auction::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $auction]);
        $auction->setUpdatedAt(new \DateTimeImmutable());
        $errors = $validator->validate($auction);

        if (count($errors) > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), Response::HTTP_BAD_REQUEST, [], true);
        }
        $entityManager->persist($auction);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
