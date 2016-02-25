<?php
/**
 * Created by PhpStorm.
 * User: Bliksem
 * Date: 2016-01-25
 * Time: 13:26
 */

namespace Wynajem\BlogBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Builder implements ContainerAwareInterface
{

    use ContainerAwareTrait;

    /**
     * Sets the container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     */
    public function setContainer(ContainerInterface $container = null)
    {

    }


    public function mainMenu(FactoryInterface $factory)
    {
        $menu = $factory->createItem('root');

        $menu->addChild('Home', array('route' => 'blog_index'));


        $menu->addChild('Latest Blog Post', array(
            'route' => 'blog_news'
        ));

        // create another menu item
        $menu->addChild('About Me', array('route' => 'blog_about'));
        // you can also add sub level's to your menu's as follows
        $menu['About Me']->addChild('Edit profile', array('route' => 'blog_contact'));

        // ... add more children

        return $menu;
    }
}