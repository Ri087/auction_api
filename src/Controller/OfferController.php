<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Repository\OfferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;

class OfferController extends AbstractController
{
    #[Route('/offer', name: 'app_offer')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/OfferController.php',
        ]);
    }
    #[Route('/api/offer', name: 'offer.getAll', methods: ['GET'])]
    public function getAllOffer(OfferRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $offer = $repository->findAll();
        $jsonOffer = $serializer->serialize($offer, 'json', ['groups' => 'getAllOffer']);
        return new JsonResponse($jsonOffer, Response::HTTP_OK, [], true);
    }

    #[Route('/api/offer/{offer}', name: 'offer.get', methods: ['GET'])]
    public function getOffer(Offer $offer, SerializerInterface $serializer): JsonResponse
    {
        $jsonOffer = $serializer->serialize($offer, 'json', ['groups' => 'getAllOffer']);
        return new JsonResponse($jsonOffer, Response::HTTP_OK, ['accept' => 'json'], true);
    }

    #[Route('/api/offer', name: "offer.create", methods: ['POST'])]
    public function createOffer(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): JsonResponse
    {

        $offer = $serializer->deserialize($request->getContent(), Offer::class, 'json');

        $offer->setPrice((float)$request->request->get("amount"));
        $offer->setAuction($request->request->get("auction"));

        $offer->setCreatedAt(new \DateTimeImmutable());

        $entityManager->persist($offer);
        $entityManager->flush();


        $jsonOffer = $serializer->serialize($offer, 'json');

        $location = $urlGenerator->generate('offer.get', ['offer' => $offer->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonOffer, Response::HTTP_CREATED, ["Location" => $location], true);
    }
}
