<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 16/02/14
 * Time: 15:01
 */

namespace AM\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comment
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AM\SiteBundle\Entity\CommentRepository")
 */
class Comment
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="author", type="string", length=255)
     */
    private $author;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text")
     */
    private $comment;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="publishedDate", type="datetime")
     */
    private $publishedDate;


    public function __construct()
    {
        $this->publishedDate = new \DateTime();
    }

    /**
     * @param string $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \DateTime $publishedDate
     */
    public function setPublishedDate($publishedDate)
    {
        $this->publishedDate = $publishedDate;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPublishedDate()
    {
        return $this->publishedDate;
    }






} 