<?php
/**
 * Created by PhpStorm.
 * User: Bliksem
 * Date: 2016-01-20
 * Time: 12:16
 */

namespace Wynajem\BlogBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\AbstractFixture;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Wynajem\BlogBundle\Entity\Category;

class CategoryFixtures extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {

        $categoriesList = array(
            'sprzet-budowlany' =>  'Sprzet budowlany',
            'podnosnik'     =>      'Podnośniki',
            'rusztowania'      =>      'Rusztowania',
            'podesty-dzwigi'     =>      'Podesty i dźwigi',
            'kontenery-ogrodzenia'         =>      'Kontenery i ogrodzenia'
        );


        foreach ($categoriesList as $key => $name){
            $Category = new Category();
            $Category
                ->setName($name);
//                ->setSlug($key);
            $manager->persist($Category);
            $this->addReference('category_'.$key, $Category);

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