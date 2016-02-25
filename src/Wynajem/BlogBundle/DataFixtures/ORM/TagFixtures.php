<?php
/**
 * Created by PhpStorm.
 * User: Bliksem
 * Date: 2016-01-20
 * Time: 12:16
 */

namespace Wynajem\BlogBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\AbstractFixture;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Wynajem\BlogBundle\Entity\Tag;

class TagFixtures extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        $tagsList = array(
            'dolor',
            'ullamcorper',
            'suspendisse',
            'pellentesque',
            'maecenas',
            'malesuada',
            'ultricies',
            'etiam',
            'quisque',
            'fringilla',
            'eleifend',
            'bibendum',
            'faucibus',
            'luctus',
            'vestibulum'
        );


        foreach ($tagsList as $name){
            $Tag = new Tag();
            $Tag
                ->setName($name);
            $manager->persist($Tag);

            $this->addReference('tag_'. $name, $Tag);
        }


        $manager->flush();


    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 0;
    }
}