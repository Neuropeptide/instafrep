<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function findMostLiked() {

        $query = $this->_em->createQuery("
            SELECT p, COUNT(p.id) as nbLikes
            FROM App\Entity\Post p 
            JOIN p.likers l 
            GROUP BY p.id
            ORDER BY nbLikes DESC ");

        $query->setMaxResults(1);
        $result = $query->getOneOrNullResult();

        // Uncomment the next line to understand why returning $result[0] is needed here
        // dd($result);
        if (empty($result)) return null;

        return $result[0];

    }

    public function countLikedPostBy(User $user) {

        $query = $this->_em->createQuery("
            SELECT COUNT(p.id) as nbLikes
            FROM App\Entity\Post p 
            JOIN p.likers l
            WHERE l = :userId
            ");

        $query->execute(['userId' => $user->getId()]);

        $result = $query->getSingleScalarResult();

        // Uncomment the next line to understand why returning $result[0] is needed here
        // dd($result);

        return $result;

    }

    /**
     * Count all posts in DB
     */
    public function countAllPost()
    {
        $query = $this->_em->createQuery("
            SELECT COUNT(p.id)
            FROM App\Entity\Post p
        ");

        return $query->getSingleScalarResult();
    }

    /**
     * @param $limit
     * @param $total
     * @return false|float
     */
    public function pageNumber($limit, $total)
    {
        return ceil($total / $limit);
    }

    /**
     * take page and all posts within
     * @param int $page
     * @param int $limit
     * @return array|int|string
     */
    public function findPage(int $page, int $limit)
    {
        $offset = ($page - 1) * $limit;


        $query = $this->_em->createQuery("
            SELECT p
            FROM App\Entity\Post p 
        ")
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return $query->getResult();
    }

}
