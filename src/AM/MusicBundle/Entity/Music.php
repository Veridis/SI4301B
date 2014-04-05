<?php

namespace AM\MusicBundle\Entity;

use AM\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AM\MusicBundle\Repository\MusicRepository")
 */
class Music
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AM\UserBundle\Entity\User", inversedBy="musics")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    protected $album;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(max="20")
     * @Assert\NotBlank()
     */
    protected $style;

    /**
     * @ORM\OneToOne(targetEntity="AM\MusicBundle\Entity\MusicFiles", cascade={"persist"})
     * @ORM\JoinColumn(name="musicfile_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $musicFiles;

    /**
     * time in seconds
     * @ORM\Column(type="integer")
     * @Assert\Type(type="integer", message="The value {{ value }} is not an {{ type }}.")
     */
    protected $duration;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $uploadedAt;


    public function __construct()
    {
        $this->uploadedAt = new \DateTime('now');
        $this->musicFiles = new MusicFiles();
    }
    public function setAlbum($album)
    {
        $this->album = $album;

        return $this;
    }

    public function getAlbum()
    {
        return $this->album;
    }

    public function setDuration($duration)
    {
        $this->duration = $duration;
        return $this;
    }

    public function getDuration()
    {
        return $this->duration;
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

    public function setMusicFiles(MusicFiles $musicFiles)
    {
        $this->musicFiles = $musicFiles;

        return $this;
    }

    public function getMusicFiles()
    {
        return $this->musicFiles;
    }

    public function setStyle($style)
    {
        $this->style = $style;

        return $this;
    }

    public function getStyle()
    {
        return $this->style;
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
        $user->addMusic($this);

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUploadedAt($uploadedAt)
    {
        $this->uploadedAt = $uploadedAt;

        return $this;
    }

    public function getUploadedAt()
    {
        return $this->uploadedAt;
    }

    public function convertDuration()
    {
        return gmdate('i:s', $this->getDuration());
    }
}
