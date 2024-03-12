<?php

namespace App\Repository;

use App\Entity\Attatchment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Attatchment>
 *
 * @method Attatchment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Attatchment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Attatchment[]    findAll()
 * @method Attatchment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttatchmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attatchment::class);
    }

//    /**
//     * @return Attatchment[] Returns an array of Attatchment objects
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

//    public function findOneBySomeField($value): ?Attatchment
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
