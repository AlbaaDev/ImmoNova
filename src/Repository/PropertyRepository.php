<?php

namespace App\Repository;

use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;
use PhpParser\Node\Expr\Array_;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }


    /**
     * @return Property[]
     */
    public function findAllVisible() : array {
        return $this->findVisibleQuery()
                    ->getQuery()
                    ->getResult();
    }

    /**
     * @return Property[]
     */
    public function findLatest() : array {
        return $this->findVisibleQuery()
             ->setMaxResults(4)
             ->getQuery()
             ->getResult();
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function findVisibleQuery() : QueryBuilder {
        return $this->createQueryBuilder('p')
                    ->where('p.sold = false');
    }


    // /**
    //  * @return Property[] Returns an array of Property objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Property
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findCity($city) {
        return $this->createQueryBuilder('p')
            ->where('p.city LIKE :value%')
            ->andWhere('p.sold = false')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();
    }

    public function findProperties($type, $city, $price, $mode) : array {
        return $this->createQueryBuilder('p')
            ->where('p.sold = false')
            ->andWhere('p.price >= :price')
            ->andWhere('p.city = :city')
            ->andWhere('p.type = :type')
            ->andWhere('p.mode = :mode')
            ->setParameters(new ArrayCollection(array(
                new Parameter('price', $price),
                new Parameter('city',  $city),
                new Parameter('type',  $type),
                new Parameter('mode',  $mode),
            )))
            ->getQuery()
            ->getResult();
    }

    public function estimate($type, array $city, array $cp) {
        return $this->createQueryBuilder('p')
            ->where('p.sold = false')
            ->andWhere('p.city = :city')
            ->andWhere('p.postalcode = :cp ')
            ->setParameters(new ArrayCollection(array(
                new Parameter('city',  $city),
                new Parameter('cp', $cp),
            )))
            ->getQuery()
            ->getResult();
    }

    public function findArray($array) {
        return $this->createQueryBuilder('p')
            ->where('p.id IN (:array)')
            ->setParameter('array', $array)
            ->getQuery()
            ->getResult();

    }


}
