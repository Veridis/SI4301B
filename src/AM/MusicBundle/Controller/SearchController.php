<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 03/04/14
 * Time: 20:54
 */

namespace AM\MusicBundle\Controller;

use AM\MusicBundle\Entity\MusicFiles;
use AM\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AM\MusicBundle\Entity\Music;
use AM\MusicBundle\Form\MusicType;

class SearchController extends Controller
{
    /**
     * @Route("/search", name="music_search")
     * @Method("POST")
     * @Template()
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $musics = $em->getRepository('AMMusicBundle:Music')->searchMusic($request->get('research'));

        return array('musics' => $musics);
    }

    /**
     * Rajouter des requierement sur order
     * @Route("/musics/orderby/{order}", name="music_orderby")
     * @Template("AMMusicBundle:Music:index.html.twig")
     */
    public function orderByAction($order)
    {
        $em = $this->getDoctrine()->getManager();
        $musics = $em->getRepository('AMMusicBundle:Music')->orderMusicBy($order);

        return array('musics' => $musics);

    }
} 