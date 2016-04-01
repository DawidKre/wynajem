<?php

namespace Wynajem\AdminBundle\Twig\Extension;

class AdminExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin';
    }

    public function getFilters()
    {
        return array(
            'admin_format_date' => new \Twig_SimpleFilter('admin_format_date', array($this, 'adminFormatDate'),array('is_safe' => array('html')))
        );
    }

    public function adminFormatDate (\DateTime $dateTime)
    {
        return $dateTime->format('d.m.Y, H:i:s');
    }

}
