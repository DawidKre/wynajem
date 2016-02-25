<?php
/**
 * Created by PhpStorm.
 * User: Bliksem
 * Date: 2016-01-20
 * Time: 20:22
 */

namespace Wynajem\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation\Slug;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Post
 *
 * @ORM\Table(name="blog_products")
 * @ORM\Entity(repositoryClass="Wynajem\BlogBundle\Repository\ProductRepository")
 *
 * @UniqueEntity(fields={"name"})
 * @UniqueEntity(fields={"slug"})
 *
 * @ORM\HasLifecycleCallbacks()
 */
class Product
{

    const DEFAULT_AVATAR = 'default-thumbnail2.jpg';
    const UPLOAD_DIR = 'uploads/products/';
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
     * @ORM\Column(name="name", type="string", length=120, unique=true)
     *
     * @Assert\NotBlank
     *
     * @Assert\Length(max = 120)
     */
    private $name;

    /**
     * @Slug(fields={"name"})
     *
     * @ORM\Column(name="slug", type="string", length=120, unique=true)
     */
    private $slug;


    /**
     * @ORM\ManyToOne(
     *     targetEntity="Wynajem\BlogBundle\Entity\Category",
     *     inversedBy="products"
     * )
     * @ORM\JoinColumn(
     *     name="category_id",
     *     referencedColumnName="id",
     *     onDelete="SET NULL"
     * )
     */
    private $category;

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

    /**
     * @ORM\ManyToMany(
     *     targetEntity="Wynajem\BlogBundle\Entity\Tag",
     *     inversedBy="products"
     * )
     *
     * @ORM\JoinTable(
     *     name="blog_products_tags"
     * )
     */
    private $tags;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;


    /**
     *
     * @ORM\Column(name="thumbnail", type="string", length=80, nullable=true)
     */
    private $thumbnail = null;

    /**
     * @ORM\Column(name="update_date", type="datetime", nullable=true)
     */
    private $updateDate = null;

    /**
     * @Assert\Image(
     *      minWidth = 200,
     *      minHeight = 200,
     *      maxWidth = 1920,
     *      maxHeight = 1080,
     *      maxSize = "2M"
     * )
     */
    private $thumbnailFile;

    private $thumbnailTemp;
     /**
     * @ORM\Column(name="price_for_hour", type="decimal", precision=5, scale=2)
      *
      * @Assert\NotBlank()
     */
    private $priceForHour;

    /**
     * @ORM\Column(name="price_for_day", type="decimal", precision=5, scale=2)
     * @Assert\NotBlank()
     */
    private $priceForDay;

    /**
     * @ORM\Column(name="available", type="boolean", options={"default" = true})
     */
    private $available = true;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="add_date", type="datetime")
     */
    private $addDate;

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
     * Set name
     *
     * @param string $name
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
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
     * Set slug
     *
     * @param string $slug
     * @return Product
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
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



    /**
     * Set description
     *
     * @param string $description
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set thumbnail
     *
     * @param string $thumbnail
     * @return Product
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;

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
            return Product::UPLOAD_DIR.Product::DEFAULT_AVATAR;
        }

        return Product::UPLOAD_DIR.$this->thumbnail;
    }

    /**
     * Set priceForHour
     *
     * @param string $priceForHour
     * @return Product
     */
    public function setPriceForHour($priceForHour)
    {
        $this->priceForHour = $priceForHour;

        return $this;
    }

    /**
     * Get priceForHour
     *
     * @return string 
     */
    public function getPriceForHour()
    {
        return $this->priceForHour;
    }

    /**
     * Set priceForDay
     *
     * @param string $priceForDay
     * @return Product
     */
    public function setPriceForDay($priceForDay)
    {
        $this->priceForDay = $priceForDay;

        return $this;
    }

    /**
     * Get priceForDay
     *
     * @return string 
     */
    public function getPriceForDay()
    {
        return $this->priceForDay;
    }

    /**
     * Set addDate
     *
     * @param \DateTime $addDate
     * @return Product
     */
    public function setAddDate($addDate)
    {
        $this->addDate = $addDate;

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->addDate = new \DateTime();
    }

    /**
     * Get addDate
     *
     * @return \DateTime 
     */
    public function getAddDate()
    {
        return $this->addDate;
    }

    /**
     * Set category
     *
     * @param \Wynajem\BlogBundle\Entity\Category $category
     * @return Product
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
    }

    /**
     * Add tags
     *
     * @param \Wynajem\BlogBundle\Entity\Tag $tags
     * @return Product
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
     * Set available
     *
     * @param boolean $available
     * @return Product
     */
    public function setAvailable($available)
    {
        $this->available = $available;

        return $this;
    }

    /**
     * Get available
     *
     * @return boolean 
     */
    public function getAvailable()
    {
        return $this->available;
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
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preSave(){
        if(null === $this->slug){
            $this->setSlug($this->getName());
        }

        if(null !== $this->getThumbnailFile()){

            if(null !== $this->thumbnail){
                $this->thumbnailTemp = $this->thumbnail;
            }

            $fileName = sha1(uniqid(null, true));
            $this->thumbnail = $fileName.'.'.$this->getThumbnailFile()->guessExtension();
        }

        if(null == $this->addDate){
            $this->createDate = new \DateTime();
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
