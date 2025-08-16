<?php

namespace App\Repository;

use App\Entity\GoogleDriveFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GoogleDriveFile>
 *
 * @method GoogleDriveFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method GoogleDriveFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method GoogleDriveFile[]    findAll()
 * @method GoogleDriveFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GoogleDriveFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GoogleDriveFile::class);
    }

    public function add(GoogleDriveFile $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GoogleDriveFile $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return GoogleDriveFile[] Returns an array of GoogleDriveFile objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?GoogleDriveFile
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
