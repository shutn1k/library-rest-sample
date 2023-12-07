<?php

namespace App\Repository\Library;

use App\Constants\Locale;
use App\Entity\Library\Book;
use App\Model\Search\BookSearchParams;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Gedmo\Translatable\Entity\Translation;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @param Book $entity
     * @param bool $flush
     *
     * @return void
     */
    public function save(Book $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param BookSearchParams $bookSearchParams
     *
     * @return Book[]
     */
    public function search(BookSearchParams $bookSearchParams): array
    {
        $qb = $this->createQueryBuilder('b');

        if ($bookSearchParams->getLocale() !== Locale::DEFAULT_LOCALE) {
            $qb->leftJoin(
                Translation::class,
                't',
                Query\Expr\Join::WITH,
                sprintf(
                    "t.locale = '%s' AND t.field = 'title' AND t.objectClass = '%s' AND t.foreignKey = b.id",
                    $bookSearchParams->getLocale(),
                    Book::class
                )
            )
                ->where($qb->expr()->like('t.content', ':title'))
                ->orderBy('t.content');
        } else {
            $qb->where($qb->expr()->like('b.title', ':title'))
                ->orderBy('b.title');
        }

        $query = $qb->setParameter('title', "%{$bookSearchParams->getBookTitle()}%")
            ->setMaxResults(10)
            ->getQuery();

        return $query->getResult();
    }
}
