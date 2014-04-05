<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 05/04/14
 * Time: 14:10
 */

namespace AM\MusicBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table()
 * @ORM\Entity()
 */
class Comment
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="content", type="text")
     * @Assert\Length(min = "5", max = "500")
     */
    protected $content;

    /**
     * @ORM\ManyToOne(targetEntity="AM\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $user;

    /**
     * @ORM\Column(name="date", type="datetime")
     */
    protected $postedAt;

    /**
     * @ORM\ManyToOne(targetEntity="AM\MusicBundle\Entity\Music", inversedBy="comments")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $music;

    public function __construct()
    {
        $this->postedAt = new \Datetime();
    }


    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
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

    public function setMusic($music)
    {
        $this->music = $music;

        return $this;
    }

    public function getMusic()
    {
        return $this->music;
    }

    public function setPostedAt($postedAt)
    {
        $this->postedAt = $postedAt;

        return $this;
    }

    public function getPostedAt()
    {
        return $this->postedAt;
    }

    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }
}