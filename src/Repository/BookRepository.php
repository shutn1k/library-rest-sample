<?php

namespace App\Repository;

use App\Entity\Book;
use App\Model\BookFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {

        parent::__construct($registry, Book::class);
    }

    /**
     * @param BookFilter $bookFilter
     *
     * @return Book[]
     */
    public function findByFilter(BookFilter $bookFilter): array {

        $qb = $this->createQueryBuilder('b')
            ->leftJoin('b.translations', 'bt', Expr\Join::WITH, 'bt.locale = :locale')
            // ToDo перенести locale в фильтр
            ->setParameter('locale', 'ru');

        // Можно сделать цепочку обязанностей
        if ($bookFilter->getName()) {
            $qb->andWhere('bt.name LIKE :name')
                ->setParameter('name', "%{$bookFilter->getName()}%");
        }

        $qb->setMaxResults(BookFilter::BOOK_LIMIT);

        return $qb->getQuery()->getResult();
    }
}
