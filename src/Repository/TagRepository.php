<?php

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tag>
 *
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    /**
     * Cherche les tags les plus utilisés
     * @param int $limit
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findMostUsedTags(int $limit)
    {
        return $this->createQueryBuilder('t')
            ->select('t, COUNT(p.id) as HIDDEN count')
            ->join('t.photos', 'p')
            ->groupBy('t.id')
            ->orderBy('count', 'DESC')
            ->setMaxResults($limit);
    }
}
