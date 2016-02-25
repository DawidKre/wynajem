<?php
/**
 * Created by PhpStorm.
 * User: Bliksem
 * Date: 2016-01-20
 * Time: 11:10
 */

namespace Wynajem\BlogBundle\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 *
 * @ORM\Table(name="blog_category")
 * @ORM\Entity(repositoryClass="Wynajem\BlogBundle\Repository\CategoryRepository")
 *
 */
class Category extends AbstractTaxonomy
{

    /**
     * @ORM\OneToMany(
     *     targetEntity="Wynajem\BlogBundle\Entity\Post",
     *     mappedBy="category"
     * )
     */
    protected $posts;

    /**
     * @ORM\OneToMany(
     *     targetEntity="Wynajem\BlogBundle\Entity\Product",
     *     mappedBy="category"
     * )
     */
    protected $products;

}
