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
            'admin_format_date' => new \Twig_Filter_Method($this, 'adminFormatDate')
        );
    }

    public function adminFormatDate (\DateTime $dateTime)
    {
        return $dateTime->format('d.m.Y, H:i:s');
    }

}
