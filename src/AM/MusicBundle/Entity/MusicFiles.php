<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 01/04/14
 * Time: 17:45
 */

namespace AM\MusicBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table()
 * @ORM\Entity
 */
class MusicFiles
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Assert\File(maxSize="6000000")
     */
    protected $cover;

    /**
     * @Assert\File(maxSize="6000000")
     */
    protected $song;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $songPath;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $coverPath;

    public function setCover($cover)
    {
        $this->cover = $cover;
        return $this;
    }

    public function getCover()
    {
        return $this->cover;
    }

    public function setSong($song)
    {
        $this->song = $song;
        return $this;
    }

    public function getSong()
    {
        return $this->song;
    }



    public function setCoverPath($coverPath)
    {
        $this->coverPath = $coverPath;
        return $this;
    }

    public function getCoverPath()
    {
        return $this->coverPath;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setSongPath($songPath)
    {
        $this->songPath = $songPath;
        return $this;
    }

    public function getSongPath()
    {
        return $this->songPath;
    }




    public function getSongAbsolutePath()
    {
        return null === $this->songPath ? null : $this->getSongUploadRootDir().'/'.$this->songPath;
    }

    public function getSongWebPath()
    {
        return null === $this->songPath ? null : $this->getSongUploadDir().'/'.$this->songPath;
    }
    protected function getSongUploadRootDir()
    {
        return __DIR__.'/../../../../web/'.$this->getSongUploadDir();
    }
    protected function getSongUploadDir()
    {
        return 'uploads/musics/songs';
    }



    public function getCoverAbsolutePath()
    {
        return null === $this->coverPath ? null : $this->getCoverUploadRootDir().'/'.$this->coverPath;
    }
    public function getCoverWebPath()
    {
        return null === $this->coverPath ? null : $this->getCoverUploadDir().'/'.$this->coverPath;
    }
    protected function getCoverUploadRootDir()
    {
        return __DIR__.'/../../../../web/'.$this->getCoverUploadDir();
    }
    protected function getCoverUploadDir()
    {
        return 'uploads/musics/covers';
    }


    public function upload()
    {
        if (null === $this->song) {
            return;
        }
        $name = uniqid();
        $this->song->move($this->getSongUploadRootDir(), $name);
        $this->cover->move($this->getCoverUploadDir(), $name);
        $this->coverPath = $name;
        $this->songPath = $name;

        $this->song = null;
        $this->cover = null;
    }

} 