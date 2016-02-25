<?php
/**
 * Created by PhpStorm.
 * User: Bliksem
 * Date: 2016-02-03
 * Time: 13:46
 */

namespace Wynajem\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="blog_comments")
 */
class Comment
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Wynajem\BlogBundle\Entity\Post",
     *     inversedBy="comments"
     * )
     *
     * @ORM\JoinColumn(
     *     name="post_id",
     *     referencedColumnName="id",
     *     nullable= false,
     *     onDelete="CASCADE"
     * )
     */
    private $post;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Common\UserBundle\Entity\User"
     * )
     *
     * @ORM\JoinColumn(
     *     name="author_id",
     *     referencedColumnName="id",
     *     nullable= false
     * )
     */
    private $author;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createDate;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     *
     * @Assert\Length(max="1000")
     */
    private $comment;

    /**
     * Comment constructor.
     */
    public function __construct()
    {
        $this->createDate = new \DateTime();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return Comment
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return \DateTime 
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return Comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set post
     *
     * @param \Wynajem\BlogBundle\Entity\Post $post
     * @return Comment
     */
    public function setPost(\Wynajem\BlogBundle\Entity\Post $post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * Get post
     *
     * @return \Wynajem\BlogBundle\Entity\Post 
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Set author
     *
     * @param \Common\UserBundle\Entity\User $author
     * @return Comment
     */
    public function setAuthor(\Common\UserBundle\Entity\User $author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return \Common\UserBundle\Entity\User 
     */
    public function getAuthor()
    {
        return $this->author;
    }
}
