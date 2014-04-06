<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 05/04/14
 * Time: 17:57
 */

namespace AM\UserBundle\Repository;


use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findWithAllMusics($userId)
    {
        $queryBuilder = $this->createQueryBuilder('user')
            ->leftJoin('user.musics', 'music')
            ->addSelect('music')
            ->leftJoin('music.musicFiles', 'musicFiles')
            ->addSelect('musicFiles')
            ->orderBy('music.uploadedAt','DESC')
            ->where('user.id = :userId')
            ->setParameter('userId', $userId)
        ;

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function findFavWithMusics($userId)
    {
        $qb = $this->createQueryBuilder('user')
            ->leftJoin('user.favMusics', 'favMusics')
            ->addSelect('favMusics')
            ->leftJoin('favMusics.musicFiles','musicFiles')
            ->addSelect('musicFiles')
            ->where('user.id = :userId')
            ->setParameter('userId', $userId)
        ;

        return $qb->getQuery()->getResult();
    }
} 