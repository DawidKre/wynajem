<?php

namespace Wynajem\BlogBundle\Entity;

use Common\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Slug;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Post
 *
 * @ORM\Table(name="blog_post")
 * @ORM\Entity(repositoryClass="Wynajem\BlogBundle\Repository\PostRepository")
 *
 * @UniqueEntity(fields={"title"})
 * @UniqueEntity(fields={"slug"})
 *
 * @ORM\HasLifecycleCallbacks()
 */
class Post
{
    const DEFAULT_AVATAR = 'default-thumbnail.jpg';
    const UPLOAD_DIR = 'uploads/thumbnails/';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=120, unique=true)
     * @Assert\NotBlank
     *
     * @Assert\Length(max = 120)
     */
    private $title;

    /**
     * @Slug(fields={"title"})
     *
     * @ORM\Column(name="slug", type="string", length=120, unique=true)
     * @Assert\Length(max="120")
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text")
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private $thumbnail = null;

    /**
     * @Assert\Image(
     *      minWidth = 600,
     *      minHeight = 480,
     *      maxWidth = 1920,
     *      maxHeight = 1080,
     *      maxSize = "2M"
     * )
     */
    private $thumbnailFile;

    private $thumbnailTemp;


    /**
     * @ORM\ManyToOne(
     *     targetEntity="Wynajem\BlogBundle\Entity\Category",
     *     inversedBy="posts"
     *
     * )
     * @ORM\JoinColumn(
     *     name="category_id",
     *     referencedColumnName="id",
     *     onDelete="SET NULL"
     * )
     */
    private $category;

    /**
     * @ORM\ManyToMany(
     *     targetEntity="Wynajem\BlogBundle\Entity\Tag",
     *     inversedBy="posts"
     * )
     *
     * @ORM\JoinTable(
     *     name="blog_posts_tags"
     * )
     * @Assert\Count(
     *      min=2
     * )
     */
    private $tags;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Common\UserBundle\Entity\User"
     * )
     *
     * @ORM\JoinColumn(
     *     name="author_id",
     *     referencedColumnName="id"
     *
     * )
     */
    private $author;

    /**
     * @ORM\Column(name="update_date", type="datetime", nullable=true)
     */
    private $updateDate = null;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetime")
     */
    private $createDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="published_date", type="datetime", nullable=true)
     *
     * @Assert\DateTime
     */
    private $publishedDate;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Wynajem\BlogBundle\Entity\Comment",
     *     mappedBy="post"
     * )
     *
     * @ORM\OrderBy({"createDate" = "DESC"})
     *
     */
    private $comments;

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
     * Set title
     *
     * @param string $title
     * @return Post
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }



    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;
    }




    /**
     * Set content
     *
     * @param string $content
     * @return Post
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set thumbnail
     *
     * @param string $thumbnail
     * @return Post
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }


    /**
     * Set author
     *
     * @param \Common\UserBundle\Entity\User $author
     * @return Post
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

    /**
     * Set createDate
     *
     * @param \DateTime $createDate
     * @return Post
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
     * Set publishedDate
     *
     * @param \DateTime $publishedDate
     * @return Post
     */
    public function setPublishedDate($publishedDate)
    {
        $this->publishedDate = $publishedDate;

        return $this;
    }

    /**
     * Get publishedDate
     *
     * @return \DateTime 
     */
    public function getPublishedDate()
    {
        return $this->publishedDate;
    }

    /**
     * Set category
     *
     * @param \Wynajem\BlogBundle\Entity\Category $category
     * @return Post
     */
    public function setCategory(\Wynajem\BlogBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Wynajem\BlogBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection();


    }

    /**
     * Add tags
     *
     * @param \Wynajem\BlogBundle\Entity\Tag $tags
     * @return Post
     */
    public function addTag(\Wynajem\BlogBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;

        return $this;
    }

    /**
     * Remove tags
     *
     * @param \Wynajem\BlogBundle\Entity\Tag $tags
     */
    public function removeTag(\Wynajem\BlogBundle\Entity\Tag $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Add comments
     *
     * @param \Wynajem\BlogBundle\Entity\Comment $comments
     * @return Post
     */
    public function addComment(\Wynajem\BlogBundle\Entity\Comment $comments)
    {
        $this->comments[] = $comments;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \Wynajem\BlogBundle\Entity\Comment $comments
     */
    public function removeComment(\Wynajem\BlogBundle\Entity\Comment $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }

    public function getThumbnailFile() {
        return $this->thumbnailFile;
    }

    public function setThumbnailFile(UploadedFile $thumbnailFile) {
        $this->thumbnailFile = $thumbnailFile;
        $this->updateDate = new \DateTime();
        return $this;
    }

    /**
     * Get thumbnail
     *
     * @return string
     */
    public function getThumbnail()
    {
        if(null == $this->thumbnail){
            return Post::UPLOAD_DIR.Post::DEFAULT_AVATAR;
        }

        return Post::UPLOAD_DIR.$this->thumbnail;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preSave(){
        if(null === $this->slug){
            $this->setSlug($this->getTitle());
        }

        if(null !== $this->getThumbnailFile()){

            if(null !== $this->thumbnail){
                $this->thumbnailTemp = $this->thumbnail;
            }

            $fileName = sha1(uniqid(null, true));
            $this->thumbnail = $fileName.'.'.$this->getThumbnailFile()->guessExtension();
        }

        if(null == $this->createDate){
            $this->createDate = new \DateTime();
        }
        if(null == $this->publishedDate){
            $this->publishedDate = new \DateTime();
        }
    }
    /**
     * @ORM\PostPersist
     * @ORM\PostUpdate
     */
    public function postSave(){
        if(NULL !== $this->getThumbnailFile()){
            $this->getThumbnailFile()->move($this->getUploadRootDir(), $this->thumbnail);
            unset($this->thumbnailFile);

            if(isset($this->thumbnailTemp)){
                unlink($this->getUploadRootDir().'/'.$this->thumbnailTemp);
                unset($this->thumbnailTemp);
            }
        }
    }

    /**
     * @ORM\PostRemove
     */
    public function postRemove() {
        if(null !== $this->thumbnail){
            unlink($this->getUploadRootDir().'/'.$this->thumbnail);
        }
    }
    public function getUploadRootDir(){
        return __DIR__.'/../../../../web/'.self::UPLOAD_DIR;
    }
}
