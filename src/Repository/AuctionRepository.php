<?php

namespace App\Repository;

use App\Entity\Auction;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Auction>
 *
 * @method Auction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Auction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Auction[]    findAll()
 * @method Auction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuctionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Auction::class);
    }

    //    /**
    //     * @return Auction[] Returns an array of Auction objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Auction
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findOpenAuctionsByUser(User $user)
    {
        return $this->createQueryBuilder('a')
            ->where('a.user = :user')
            ->andWhere('a.status != :status')
            ->setParameter('user', $user)
            ->setParameter('status', 'CLOSED')
            ->getQuery()
            ->getResult();
    }
    public function findCloseAuctionsByUser(User $user)
    {
        return $this->createQueryBuilder('a')
            ->where('a.user = :user')
            ->andWhere('a.status != :status')
            ->setParameter('user', $user)
            ->setParameter('status', 'OPEN')
            ->getQuery()
            ->getResult();
    }

    public function findAuctionWithDownloadFiles(int $auctionId): ?Auction
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.downloadFiles', 'df')
            ->addSelect('df') // sélectionnez les DownloadFiles pour les charger
            ->where('a.id = :auctionId')
            ->setParameter('auctionId', $auctionId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
