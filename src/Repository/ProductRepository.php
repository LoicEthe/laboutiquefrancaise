<?php

namespace App\Repository;

use App\Classe\Search;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Requete qui recup les produits en fonction des filtres
     */
    public function findWithSearch(Search $search){
        $query = $this
            ->createQueryBuilder('p') // on crée une query avec la table produit, représentée par un p
            ->select('c','p') // on séléctionne c pour catégorie et p pour product
            ->join('p.category','c'); // on fait la jointure entre le produit de la catégorie et la categ

            if(!empty($search->categories)){
                $query = $query
                    ->andWhere('c.id IN (:categories)')
                    ->setParameter('categories',$search->categories);
            }

            if (!empty($search->string)) {
                $query = $query
                    ->andWhere('p.Name LIKE :string')
                    ->setParameter('string', "%{$search->string}%");
            }


        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return Product[] Returns an array of Product objects
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
    public function findOneBySomeField($value): ?Product
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
