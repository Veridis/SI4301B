<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 01/04/14
 * Time: 17:45
 */

namespace AM\MusicBundle\Entity;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Table()
 * @ORM\Entity
 */
class MusicFiles
{

    const DEFAULT_COVER = 'default_cover.png';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Assert\File(maxSize="500k")
     * @Assert\Image(mimeTypes={"image/gif", "image/jpeg", "image/png"})
     */
    protected $cover;

    /**
     * @Assert\File(maxSize="50M", mimeTypes={"audio/mpeg"})
     * @Assert\NotBlank()
     */
    protected $song;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $songPath;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $coverPath;

    public function __construct()
    {
        $this->coverPath = self::DEFAULT_COVER;
    }

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
        $id = uniqid();
        $songExt = $this->song->guessExtension();
        $songName = $id . '.' . $songExt;

        if(!$songExt)
        {
            throw new \Exception('The extension is not valid.');
        }

        $this->song->move($this->getSongUploadRootDir(), $songName);
        $this->songPath = $songName;

        if($this->cover != null)
        {
            $coverExt = $this->cover->guessExtension();
            $coverName = $id . '.' . $coverExt;
            if(!$coverExt)
            {
                throw new \Exception('The extension is not valid.');
            }
            $this->cover->move($this->getCoverUploadDir(), $coverName);
            $this->coverPath = $coverName;
        }
        else {
            $this->coverPath = self::DEFAULT_COVER;
        }

        $this->song = null;
        $this->cover = null;
    }

    public function removeFiles()
    {
        $fs = new Filesystem();
        try {
            $fs->remove($this->getSongWebPath());
            if($this->coverPath != self::DEFAULT_COVER)
                $fs->remove($this->getCoverWebPath());
        } catch (IOException $e) {
            var_dump($e->getMessage());
            die();
        }
    }

} 