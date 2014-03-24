<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 15/02/14
 * Time: 17:06
 */

namespace AM\SiteBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SiteController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function siteAction()
    {
        return array();
    }
} 