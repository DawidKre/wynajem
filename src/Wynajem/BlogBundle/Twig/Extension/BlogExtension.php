<?php

namespace Wynajem\BlogBundle\Twig\Extension;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Wynajem\BlogBundle\Entity\Category;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class BlogExtension extends \Twig_Extension implements \Twig_Extension_InitRuntimeInterface
{

    /**
     * @var Registry
     */
    private $doctrine;

    /**
     * @var \Twig_Environment
     */
    private $environment;


    /**
     * @var AuthorizationChecker
     */
    private $authorizationChecker;

    private $categoriesList;



    /**
     * BlogExtension constructor.
     * @param Registry $doctrine
     * @param AuthorizationChecker $authorizationChecker
     */
    public function __construct(Registry $doctrine, AuthorizationChecker $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->doctrine = $doctrine;

    }

    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'wynajem_blog_extension';
    }


    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('print_categories_list', array($this, 'printCategoriesList'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('print_menu_list', array($this, 'printMenuList'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('print_tags_list', array($this, 'tagsCloud'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('print_recent_commented', array($this, 'recentCommented'), array('is_safe' => array('html'))),

        );
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('ab_shorten', array($this, 'shorten'), array('is_safe' => array('html')))
        );
    }



    public function printCategoriesList()
    {
        if(!isset($this->categoriesList)) {
            $categoryRepo = $this->doctrine->getRepository(Category::class);
            $this->categoriesList = $categoryRepo->findAll();
        }

        return $this->environment->render('WynajemBlogBundle:Template:categoriesList.html.twig', array(
            'categoriesList'  =>  $this->categoriesList
        ));
    }



    public function printMenuList()
    {

        $menuList = array(
//            'Strona główna' =>  'blog_index',
            'O firmie'  =>  'blog_about',
            'Aktualności'  =>  'blog_news',
            'Oferta'  =>  'blog_offert',
//            'Wynajem'  =>  'blog_rent',
            'Cennik'  =>  'blog_prices',
            'Regulamin'  =>  'blog_regulations',
            'Kontakt'  =>  'blog_contact',

        );

//        if($this->authorizationChecker->isGranted('ROLE_EDITOR')){
//            $menuList['admin'] = 'admin_dashboard';
//        }

        return $this->environment->render('WynajemBlogBundle:Template:menuList.html.twig', array(
            'mainMenu'  =>  $menuList
        ));
    }

    public function tagsCloud($limit = 40, $minFontSize = 1, $maxFontSize = 3.5)
    {
        $TagRepo  = $this->doctrine->getRepository('WynajemBlogBundle:Tag');
        $tagsList = $TagRepo->getTagsListOcc();
        $tagsList = $this->prepareTagsCloud($tagsList, $limit, $minFontSize, $maxFontSize);

        return $this->environment->render('WynajemBlogBundle:Template:tagsList.html.twig', array(
            'tagsList'  =>  $tagsList
        ));


    }

    protected function prepareTagsCloud($tagsList, $limit, $minFontSize, $maxFontSize){
        $occs = array_map(function($row){
            return (int)$row['occ'];
        }, $tagsList);

        $minOcc = min($occs);
        $maxOcc = max($occs);

        $spread = $maxOcc - $minOcc;
        $spread = ($spread == 0) ? 1 : $spread;

        usort($tagsList, function($a, $b){
            $ao = $a['occ'];
            $bo = $b['occ'];

            if($ao === $bo) return 0;

            return ($ao < $bo) ? 1 : -1;
        });

        $tagsList = array_slice($tagsList, 0, $limit);

        shuffle($tagsList);

        foreach($tagsList as &$row){
            $row['fontSize'] = round(($minFontSize + ($row['occ'] - $minOcc) * ($maxFontSize - $minFontSize) / $spread), 2);
        }

        return $tagsList;
    }

    public function shorten($text, $lenght = 200, $wrapTag = 'p' )
    {
        $text = strip_tags($text);
        $text = substr($text, 0, $lenght).'[...]';
        $openTag = "<{$wrapTag}>";
        $closeTag = "</{$wrapTag}>";

        return $openTag.$text.$closeTag;
    }

    public function recentCommented($limit = 3){

        $PostRepo = $this->doctrine->getRepository('WynajemBlogBundle:Post');
        $postsList = $PostRepo->getRecentCommented($limit);

        return $this->environment->render('WynajemBlogBundle:Template:recentCommented.html.twig', array(
            'postsList' =>  $postsList
        ));
    }


}
