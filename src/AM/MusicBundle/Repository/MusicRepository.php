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
            ->leftJoin('music.musicFiles', 'musicFiles')
            ->addSelect('musicFiles')
            ->orderBy('music.uploadedAt','DESC')
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    public function findAllUserMusics($userId)
    {
        $queryBuilder = $this->createQueryBuilder('music')
            ->leftJoin('music.user', 'user')
            ->addSelect('user')
            ->leftJoin('music.musicFiles','musicFiles')
            ->addSelect('musicFiles')
            ->where('user.id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('music.uploadedAt','DESC')
        ;

        return $queryBuilder->getQuery()->getResult();
    }

    public function findWithCommentsAndUser($musicId)
    {
        $qb = $this->createQueryBuilder('music')
            ->leftJoin('music.musicFiles', 'musicFiles')
            ->addSelect('musicFiles')
            ->leftJoin('music.comments', 'comment')
            ->addSelect('comment')
            ->leftJoin('music.user', 'musicUser')
            ->addSelect('musicUser')
            ->leftJoin('comment.user', 'commentUser')
            ->addSelect('commentUser')
            ->where('music.id = :musicId')
            ->setParameter('musicId', $musicId)
            ->orderBy('comment.postedAt', 'DESC')
        ;

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findLastMusics($max = 4)
    {
        $qb = $this->createQueryBuilder('music')
            ->leftJoin('music.musicFiles', 'musicFiles')
            ->addSelect('musicFiles')
            ->orderBy('music.uploadedAt', 'DESC')
            ->setMaxResults($max)
        ;
        return $qb->getQuery()->getResult();
    }

    public function searchMusic($research)
    {
        $qb = $this->createQueryBuilder('music');

        $qb->leftJoin('music.user', 'user')
            ->addSelect('user')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->like('user.username', ':research'),
                    $qb->expr()->like('music.title', ':research'),
                    $qb->expr()->like('music.style', ':research'),
                    $qb->expr()->like('music.album', ':research')
                )
            )
            ->setParameter('research', '%'.$research.'%')
        ;

        return $qb->getQuery()->getResult();
    }


    public function findFavWithMusics($userId)
    {
        $qb = $this->createQueryBuilder('music')
            ->leftJoin('music.musicFiles', 'musicFiles')
            ->addSelect('musicFiles')
            ->leftJoin('music.user', 'user')
            ->addSelect('user')
            ->leftJoin('user.favMusics', 'favMusics')
            ->addSelect('favMusics')
            ->where('user.id = :userId')
            //->where('favMusics.user_id = :userId')
            //->where('IDENTITY(user.favMusics) = :userId')
            ->setParameter('userId', $userId)
        ;

        return $qb->getQuery()->getResult();
    }


    public function orderMusicBy($order)
    {
        $qb = $this->createQueryBuilder('music');
        //$orderBy = 'user.username';

        switch($order)
        {
            case 'artist' :
                $qb->leftJoin('music.user', 'user')
                    ->addSelect('user')
                    ->leftJoin('music.musicFiles', 'musicFiles')
                    ->addSelect('musicFiles')
                    ->orderBy('user.username', 'ASC');
                //$orderBy = 'user.username';
                break;
            case 'title' :
                $qb->leftJoin('music.user', 'user')
                    ->addSelect('user')
                    ->leftJoin('music.musicFiles', 'musicFiles')
                    ->addSelect('musicFiles')
                    ->orderBy('music.title', 'ASC');
                //$orderBy = 'music.title';
                break;
            case 'album' :
                $qb->leftJoin('music.user', 'user')
                    ->addSelect('user')
                    ->leftJoin('music.musicFiles', 'musicFiles')
                    ->addSelect('musicFiles')
                    ->orderBy('music.album', 'ASC');
                //$orderBy = 'music.album';
                break;
            case 'style' :
                $qb->leftJoin('music.user', 'user')
                    ->addSelect('user')
                    ->leftJoin('music.musicFiles', 'musicFiles')
                    ->addSelect('musicFiles')
                    ->orderBy('music.style', 'ASC');
               // $orderBy = 'music.style';
                break;
            case 'duration' :
                $qb->leftJoin('music.user', 'user')
                    ->addSelect('user')
                    ->leftJoin('music.musicFiles', 'musicFiles')
                    ->addSelect('musicFiles')
                    ->orderBy('music.duration', 'ASC');
                //$orderBy = 'music.duration';
                break;

        }

            //->setParameter('orderBy', $orderBy);

        return $qb->getQuery()->getResult();
    }
} 