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
use Wynajem\BlogBundle\Entity\Product;

class ProductsFixtures extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {



        $postsList = array(
            array(
                'name' => 'Młot udarowo-obrotowy Bosch GSH5',
                'description'   =>  'What’s the secret to aromatic and aged chickpeas? Always use quartered vegemite.Grill mild tofus in a bottle with champaign for about an hour to cut their creaminess.',
                'category' => 'sprzet-budowlany',
                'add_date' => '2012-01-01 12:12:12',
                'price_for_hour' => 200.00,
                'price_for_day'  => 300.23
            ),
            array(
                'name' => 'Skoczek CNCJ 72 FW',
                'description'   =>  'Turkey stew has to have a cored, muddy margerine component.To the raw zucchini add noodles, shrimps, salad cream and squeezed okra.',
                'category' => 'podnosnik',
                'author' => 'kowal',
                'add_date' => '2012-01-01 12:12:12',
                'price_for_hour' => 200.00,
                'price_for_day'  => 300.23
            ),
            array(
                'name' => 'Husqvarna FS-300',
                'description'   =>  'Cook aromatic spinach in a frying pan with coffee for about an hour to cut their asperity..Grill mild tofus in a bottle with champaign for about an hour to cut their creaminess.',
                'category' => 'sprzet-budowlany',
                'add_date' => '2012-01-01 12:12:12',
                'price_for_hour' => 200.00,
                'price_for_day'  => 300.23
            ),
            array(
                'name' => 'Piła do asfaltu',
                'description'   =>  'To the fresh chickpeas add lentils, tuna, BBQ sauce and instant peanuts. .Grill mild tofus in a bottle with champaign for about an hour to cut their creaminess.',
                'category' => 'rusztowania',
                'add_date' => '2012-01-01 12:12:12',
                'price_for_hour' => 200.00,
                'price_for_day'  => 300.23
            ),
            array(
                'name' => 'LCAT 302.5 Cm',
                'description'   =>  'Per guest prepare fifteen teaspoons of red wine with cut chicory for dessert. .Grill mild tofus in a bottle with champaign for about an hour to cut their creaminess.',
                'category' => 'podesty-dzwigi',
                'add_date' => '2012-01-01 12:12:12',
                'price_for_hour' => 200.00,
                'price_for_day'  => 300.23
            ),
            array(
                'name' => 'Silver-Line SL-7',
                'description'   =>  'Per guest prepare eight tablespoons of ricotta with sliced chicken breasts for dessert.Grill mild tofus in a bottle with champaign for about an hour to cut their creaminess.',
                'category' => 'kontenery-ogrodzenia',
                'add_date' => '2012-01-01 12:12:12',
                'price_for_hour' => 200.00,
                'price_for_day'  => 300.23
            ),
            array(
                'name' => 'Agregat prądotwórczy 5 kW',
                'description'   =>  'What’s the secret to tasty and dried butter? Always use yellow basil..Grill mild tofus in a bottle with champaign for about an hour to cut their creaminess.',
                'category' => 'kontenery-ogrodzenia',
                'add_date' => '2012-01-01 12:12:12',
                'price_for_hour' => 200.21,
                'price_for_day'  => 300.23
            ),
        );

        foreach ($postsList as $idx => $details) {
            $Product = new Product();

            $Product
                ->setName($details['name'])
                ->setDescription($details['description'])
                ->setPriceForDay($details['price_for_day'])
                ->setPriceForHour($details['price_for_hour'])
                ->setAddDate(new \DateTime());

//            $details['add_date']

//            if(null !== $details['add_date']){
//                $Product->setAddDate(new \DateTime($details['publishedDate']));


//            }

            $Product->setCategory($this->getReference('category_'.$details['category']));


            $this->addReference('product-'.$idx, $Product);

            $manager->persist($Product);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}