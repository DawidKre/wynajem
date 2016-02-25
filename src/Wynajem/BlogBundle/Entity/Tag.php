<?php
/**
 * Created by PhpStorm.
 * User: Bliksem
 * Date: 2016-01-20
 * Time: 22:10
 */

namespace Wynajem\BlogBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table(name="blog_tags")
 * @ORM\Entity(repositoryClass="Wynajem\BlogBundle\Repository\TagRepository")
 *
 */
class Tag extends AbstractTaxonomy
{

    /**
     * @ORM\ManyToMany(
     *     targetEntity="Wynajem\BlogBundle\Entity\Post",
     *     mappedBy="tags"
     * )
     */
    protected $posts;

    /**
     * @ORM\ManyToMany(
     *     targetEntity="Wynajem\BlogBundle\Entity\Product",
     *     mappedBy="tags"
     * )
     */
    protected $products;

}
