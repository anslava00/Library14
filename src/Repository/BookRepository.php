<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Book $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Book $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return Book[] Returns an array of Book objects
     */
    public function findAndSort($par): array
    {
        $mode = 'ASC';
        $orderByPar = 'books.id';
        if (substr($par['sort'], -2) == "Up")
        {
            $mode = 'ASC';
            $type = substr($par['sort'], 0, strlen($par['sort']) - 2);
        } else
        {
            $mode = 'DESC';
            $type = substr($par['sort'], 0, strlen($par['sort']) - 4);
        }
        switch ($type)
        {
            case 'title':
                $orderByPar = 'books.title';
                break;
            case 'author':
                $orderByPar = 'nAuthors';
                break;
            case 'date':
                $orderByPar = 'books.year';
                break;
        }
        $books =  $this->createQueryBuilder('books')
            ->addSelect('COUNT(authors.id) as HIDDEN nAuthors')
            ->leftJoin('books.authors', 'authors')
            ->groupBy('books.id')
            ->orderBy($orderByPar, $mode);
        $expr = $books->expr();

        if (!empty($par['titleFilter']))
            $books->where($expr->like($expr->lower('books.title'), $expr->lower(':title')))
                ->setParameter('title', "%$par[titleFilter]%");
        if (!empty($par['dateStart']) && !empty($par['dateEnd']))
            $books->andWhere('books.year >= :dateStart')
                ->andWhere('books.year <= :dateEnd')
                ->setParameter('dateStart',\DateTime::createFromFormat('Y-m-d', $par['dateStart']))
                ->setParameter('dateEnd',\DateTime::createFromFormat('Y-m-d', $par['dateEnd']));
        if (!empty($par['countAuthorStart']))
            $books->andHaving('COUNT(authors.id) >= '.$par['countAuthorStart']);
        if (!empty($par['countAuthorEnd']))
            $books->andHaving('COUNT(authors.id) <='.$par['countAuthorEnd']);
        if (!empty($par['imageFilter']))
            switch ($par['imageFilter']){
                case 'imageExist':
                    $books->andWhere($expr->isNotNull('books.image'));
                    break;
                case 'imageNotExist':
                    $books->andWhere($expr->isNull('books.image'));
                    break;
            }
        return $books->getQuery()->getResult();
    }

     /**
      * @return Book[] Returns an array of Book objects
      */
    public function findOrherRequestORM()
    {
        return $this->createQueryBuilder('books')
            ->addSelect('COUNT(authors.id) as HIDDEN nAuthors')
            ->leftJoin('books.authors', 'authors')
            ->groupBy('books.id')
            ->andHaving('COUNT(authors.id) >= 2')
            ->getQuery()->getResult();
    }

    /**
     * @return Book[] Returns an array of Book objects
     */
    public function findOrherRequestSQL(): array
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "SELECT books.*
            FROM book books 
            LEFT JOIN book_author authors
            ON books.id = authors.book_id
            GROUP BY books.id
            HAVING COUNT(authors.book_id) >= 2";
        return $conn->prepare($sql)->executeQuery()->fetchAllAssociative();
    }

    // /**
    //  * @return Book[] Returns an array of Book objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Book
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
