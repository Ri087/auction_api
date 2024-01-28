<?php

namespace App\Controller;

use App\Entity\Auction;
use App\Entity\DownloadFiles;

use App\Entity\User;
use App\Repository\AuctionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
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
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;



class AuctionController extends AbstractController
{


    #[Route('api/admin/auctions', name: 'app_auction', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN', message: 'Only the owner can access this resource')]

    public function index(Request $request, AuctionRepository $repository, SerializerInterface $serializer, PaginatorInterface $paginator): JsonResponse
    {
        $filesystemAdapter = new FilesystemAdapter();
        $cache = new TagAwareAdapter($filesystemAdapter);

        $page = $request->query->getInt('page', 1);
        $pageSize = 30;

        $idCache = "getAllAuctions" . $page;
        $cache->invalidateTags(["AuctionsCache" . $page]);


        $jsonAuctions = $cache->get($idCache, function (ItemInterface $item) use ($repository, $serializer, $paginator, $page, $pageSize) {
            $item->tag("AuctionsCache" . $page);

            $query = $repository->createQueryBuilder('a')
                ->leftJoin('a.downloadFiles', 'df')
                ->addSelect('df')
                ->getQuery();
            $pagination = $paginator->paginate($query, $page, $pageSize);


            return $serializer->serialize($pagination, 'json', ['groups' => 'getAllAuction']);
        });

        return new JsonResponse($jsonAuctions, Response::HTTP_OK, [], true);
    }


    #[Route('/api/auctions', name: 'auction.getAll', methods: ['GET'])]
    public function getAllUserAuction(#[CurrentUser] User $user, Request $request, AuctionRepository $repository, SerializerInterface $serializer, PaginatorInterface $paginator): JsonResponse
    {
        $filesystemAdapter = new FilesystemAdapter();
        $cache = new TagAwareAdapter($filesystemAdapter);

        $page = $request->query->getInt('page', 1);
        $pageSize = 10;

        $idCache = "getAllAuctions" . $page;
        $cache->invalidateTags(["AuctionsCache" . $page]);

        $query = $repository->createQueryBuilder('a')
            ->where('a.user = :user') // Filtrez par l'utilisateur connecté
            ->setParameter('user', $user)
            ->leftJoin('a.downloadFiles', 'df')
            ->addSelect('df')
            ->getQuery();

        $jsonAuctions = $cache->get($idCache, function (ItemInterface $item) use ($serializer, $paginator, $page, $pageSize, $query, $user) {
            $item->tag("AuctionsCache" . $page);

            $pagination = $paginator->paginate($query, $page, $pageSize);

            return $serializer->serialize($pagination, 'json', ['groups' => 'getAllAuction']);
        });

        return new JsonResponse($jsonAuctions, Response::HTTP_OK, [], true);
    }

    #[Route('/api/auctions/{auction}', name: 'auction.get', methods: ['GET'])]
    public function getAuction(AuctionRepository $auctionRepository, Auction $auction, SerializerInterface $serializer): JsonResponse
    {
        $auction = $auctionRepository->findAuctionWithDownloadFiles($auction->getId());
        if (!$auction) {
            return new JsonResponse(['error' => 'Auction not found'], Response::HTTP_NOT_FOUND);
        }
        $jsonAuction = $serializer->serialize($auction, 'json', ['groups' => 'getAllAuction']);
        return new JsonResponse($jsonAuction, Response::HTTP_OK, ['accept' => 'json'], true);
    }


    #[Route('/api/auctions', name: "auction.create", methods: ['POST'])]
    public function createAuction(#[CurrentUser] User $user, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator, LoggerInterface $logger): JsonResponse
    {
        // $auction = $serializer->deserialize($request->getContent(), Auction::class, 'json');
        $auction = new Auction();
        $auction->setUser($user);
        $auction->setItemName($request->request->get("item_name"));
        $auction->setItemDescription($request->request->get("item_description"));
        $auction->setPrice($request->request->get("price"));
        $auction->setMinBid($request->request->get("min_bid"));
        $auction->setStartDate(new \DateTimeImmutable($request->request->get("end_date")));
        $auction->setEndDate(new \DateTimeImmutable($request->request->get("end_date")));
        $auction->setStatus('ACTIVE');
        $auction->setCreatedAt(new \DateTimeImmutable());
        $auction->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->persist($auction);
        $entityManager->flush();

        $newFile = new DownloadFiles();
        $file = $request->files->get("file");
        if ($file) {
            $newFile->setFile($file);
            $newFile->setAuction($auction);
            $entityManager->persist($newFile);
            $entityManager->flush();
        }

        $jsonAuction = $serializer->serialize($auction, 'json', ['groups' => 'createAuction']);

        $location = $urlGenerator->generate('auction.get', ['auction' => $auction->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonAuction, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    #[Route('/api/auctions/{auction}/soft', name: 'auction.soft.delete', methods: ['DELETE'])]
    public function softDeleteAuction(Auction $auction, EntityManagerInterface $entityManager): JsonResponse
    {
        $auction->setUpdatedAt(new \DateTimeImmutable());
        $auction->setStatus('DELETE');

        $entityManager->persist($auction);

        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }


    #[Route('/api/auctions/{auction}/hard', name: 'auction.hard.delete', methods: ['DELETE'])]
    public function hardDeleteAuction(Auction $auction, EntityManagerInterface $entityManager): JsonResponse
    {
        $faker = Factory::create();

        $auction->setUpdatedAt(new \DateTimeImmutable());
        $auction->setStatus('DELETE');

        $auction->setItemName($faker->word);
        $auction->setItemDescription($faker->sentence);
        $auction->setPrice($faker->randomFloat(2, 10, 1000));
        $auction->setMinBid($faker->numberBetween(1, 100));
        $auction->setEndDate($faker->dateTimeBetween('now', '+1 years'));
        $auction->setUser(null);

        $auction->getDownloadFiles()->map(function (DownloadFiles $downloadFiles) use ($entityManager) {
            $entityManager->remove($downloadFiles);
        });

        $entityManager->persist($auction);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/auctions/{auction}', name: 'auction.update', methods: ['PATCH', "PUT"])]
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

    //Pas Possibile de delte / update / une offre 
}
