<?php

namespace App\Controller;

use App\Entity\Auction;
use App\Entity\Offer;
use App\Entity\User;
use App\Repository\OfferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Knp\Component\Pager\PaginatorInterface;


class OfferController extends AbstractController
{
    #[Route('/api/admin/offers', name: 'app_offer')]
    #[IsGranted('ROLE_ADMIN', message: 'Only the owner can access this resource')]

    public function index(Request $request, OfferRepository $repository, SerializerInterface $serializer, PaginatorInterface $paginator): JsonResponse
    {
        $filesystemAdapter = new FilesystemAdapter();
        $cache = new TagAwareAdapter($filesystemAdapter);

        $page = $request->query->getInt('page', 1);
        $pageSize = 30;


        $idCache = "getAllOffer" . $page;
        $cache->invalidateTags(["OfferCache" . $page]);

        $jsonOffers = $cache->get($idCache, function (ItemInterface $item) use ($repository, $serializer, $paginator, $page, $pageSize) {
            $item->tag("OfferCache" . $page);
            $query = $repository->createQueryBuilder('o')
                ->getQuery();

            $pagination = $paginator->paginate($query, $page, $pageSize);

            return $serializer->serialize($pagination, 'json', ['groups' => 'getAllOffer']);
        });
        return new JsonResponse($jsonOffers, Response::HTTP_OK, [], true);
    }

    #[Route('/api/offers', name: 'offer.getAll', methods: ['GET'])]
    public function getAllOffer(#[CurrentUser] User $user, Request $request, OfferRepository $repository, SerializerInterface $serializer, PaginatorInterface $paginator): JsonResponse
    {
        $filesystemAdapter = new FilesystemAdapter();
        $cache = new TagAwareAdapter($filesystemAdapter);

        $page = $request->query->getInt('page', 1);
        $pageSize = 30;

        $idCache = "getAllOffer" . $page;
        $cache->invalidateTags(["OfferCache" . $page]);

        $jsonOffers = $cache->get($idCache, function (ItemInterface $item) use ($repository, $serializer, $paginator, $page, $pageSize, $user) {
            $item->tag("OfferCache" . $page);
            $query = $repository->createQueryBuilder('o')
                ->where('o.user = :user') // Filtrez par l'utilisateur connecté
                ->setParameter('user', $user)
                ->getQuery();

            $pagination = $paginator->paginate($query, $page, $pageSize);

            return $serializer->serialize($pagination, 'json', ['groups' => 'getAllOffer']);
        });
        return new JsonResponse($jsonOffers, Response::HTTP_OK, [], true);
    }


    #[Route('/api/offers/{auction}', name: "offer.create", methods: ['POST'])]
    public function createOffer(#[CurrentUser] User $user, Auction $auction, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator): JsonResponse
    {
        if ($auction->getIsFinished()) {
            return new JsonResponse("The auction is finished", Response::HTTP_BAD_REQUEST);
        }

        $offer = $serializer->deserialize($request->getContent(), Offer::class, 'json');

        if ($offer->getAmount() <= $auction->getMinBid()) {
            return new JsonResponse("The amount must be greater than the minimum bid", Response::HTTP_BAD_REQUEST);
        }

        $offer->setCreatedAt(new \DateTimeImmutable());
        $offer->setAuction($auction);
        $offer->setUser($user);

        $auction->setPrice($auction->getPrice() + $offer->getAmount());

        $entityManager->persist($offer);
        $entityManager->persist($auction);

        $entityManager->flush();

        $jsonOffer = $serializer->serialize($offer, 'json', ['groups' => 'createOffer']);

        $location = $urlGenerator->generate('offer.create', ['offer' => $offer->getId(), 'auction' => $auction->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonOffer, Response::HTTP_CREATED, ["offer" => $location], true);
    }
}
