<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 31/03/14
 * Time: 19:43
 */

namespace AM\MusicBundle\Repository;


use Doctrine\ORM\EntityRepository;

class MusicRepository extends EntityRepository
{

    public function findAllByDateWithUser()
    {
        $queryBuilder = $this->createQueryBuilder('music')
            ->leftJoin('music.user', 'user')
            ->addSelect('user')
            ->orderBy('music.uploadedAt','DESC')
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    public function findAllUserMusics($userId)
    {
        $queryBuilder = $this->createQueryBuilder('music')
            ->leftJoin('music.user', 'user')
            ->where('user.id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('music.uploadedAt','DESC')
        ;

        return $queryBuilder->getQuery()->getResult();
    }

} 