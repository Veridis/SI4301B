<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 02/03/14
 * Time: 18:47
 */

namespace AM\AdminBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;



class AdminController extends Controller
{
    /**
     * @Route("/", name = "admin_index")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository("AMAdminBundle:Administrator")->findAll();

        return array(
            'entities' => $entities,
        );
    }

}