<?php

namespace App\Repository;

use App\Entity\DownloadFiles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DownloadFiles>
 *
 * @method DownloadFiles|null find($id, $lockMode = null, $lockVersion = null)
 * @method DownloadFiles|null findOneBy(array $criteria, array $orderBy = null)
 * @method DownloadFiles[]    findAll()
 * @method DownloadFiles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DownloadFilesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DownloadFiles::class);
    }

//    /**
//     * @return DownloadFiles[] Returns an array of DownloadFiles objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?DownloadFiles
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
