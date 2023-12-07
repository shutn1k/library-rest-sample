<?php

namespace App\Repository\Library;

use App\Entity\Library\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 *
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    /**
     * @param Author $entity
     * @param bool $flush
     *
     * @return void
     */
    public function save(Author $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Author|null
     */
    public function getRandomAuthor(): ?Author
    {
        $authorCount = $this->count([]);

        $qb = $this->createQueryBuilder('a');

        return $qb->setMaxResults(1)
            ->setFirstResult(mt_rand(0, $authorCount - 1))
            ->getQuery()
            ->getOneOrNullResult();
    }
}
