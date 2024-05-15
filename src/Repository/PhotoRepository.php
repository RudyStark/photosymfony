<?php

namespace App\Repository;

use App\Entity\Photo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Photo>
 *
 * @method Photo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Photo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Photo[]    findAll()
 * @method Photo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Photo::class);
    }

    /**
     * Cherche les photos par titre, description ou tags
     * @param string $query
     * @return array
     */
    public function search(string $query): array
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.tags', 't')
            ->where('p.title LIKE :query')
            ->orWhere('p.description LIKE :query')
            ->orWhere('t.name LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * Cherche les photos par tags
     * @param array $tags
     * @return array
     */
    public function findByTags(array $tags): array
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.tags', 't')
            ->where('t.name IN (:tags)')
            ->setParameter('tags', $tags)
            ->getQuery()
            ->getResult();
    }
}
