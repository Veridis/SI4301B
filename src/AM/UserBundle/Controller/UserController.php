<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 27/03/14
 * Time: 12:06
 */

namespace AM\UserBundle\Controller;

use AM\UserBundle\Entity\User;
use AM\UserBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserController extends Controller
{

    /**
     * @Route("/edit/{id}", requirements={"id" = "\d+"}, name = "user_edit")
     * @Template()
     */
    public function editAction($id)
    {
        if($this->getUser()->getId() != $id) {
            throw new AccessDeniedException('You are not allowed to access this page');
        }

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AMUserBundle:User')->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User');
        }

        $editForm = $this->createEditForm($user);

        return array(
            'user'      => $user,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * @Route("/update/{id}", requirements={"id" = "\d+"}, name="user_update")
     * @Template("AMUserBundle:User:edit.html.twig")
     * @Method("POST")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
       //$user = $em->getRepository('AMUserBundle:User')->find($id);
        $user = $this->getUser();
        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createEditForm($user);
        $editForm->handleRequest($request);


        if ($editForm->isValid()) {
            $this->setEncodedPassword($user);

            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                'Your informations have been changed.'
            );

            return $this->redirect($this->generateUrl('user_show', array('id' => $user->getId())));
        }


        return array(
            'user'      => $user,
            'edit_form'   => $editForm->createView(),
        );
    }


    /**
     * @Route("/create", name="user_create")
     * @Method("POST")
     * @Template("AMUserBundle:User:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $user = new User();
        $form = $this->createCreateForm($user);
        $form->handleRequest($request);

        if($form->isValid()) {
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);
            $encodedPassword = $encoder->encodePassword($user->getPlainPassword(), $user->getSalt());
            $user->setPassword($encodedPassword);
            $user->eraseCredentials();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->authenticateUser($user);

            $this->get('session')->getFlashBag()->add(
                'success',
                sprintf('Welcome on AtoMusic %s, have fun with us and share your musics!', $user->getUsername())
            );

            return $this->redirect($this->generateUrl('site_home'));
        }
        return array(
            'entity' => $user,
            'create_form'   => $form->createView(),
        );
    }

    public function createCreateForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('user_create'),
            'method' => 'POST',
        ));
        //$form->add('submit', 'submit', array('label' => 'Register'));

        return $form;
    }

    /**
     * @Route("/new", name="user_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new User();
        $createForm = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'create_form'   => $createForm->createView(),
        );
    }

    /**
     * @Route("/{id}", requirements={"id" = "\d+"}, name="user_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository("AMUserBundle:User")->findWithAllMusics($id);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        return array(
            'user' => $user,
        );
    }

    private function setEncodedPassword($user)
    {
        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($user);

        $encodedPassword = $encoder->encodePassword($user->getPlainPassword(), $user->getSalt());
        $user->setPassword($encodedPassword);
        $user->eraseCredentials();
    }

    /**
     * @Route("/addfav/{id}", requirements={"id" = "\d+"}, name="user_addfav")
     * @Template("AMMusicBundle:Music:show.html.twig")
     * @Method("POST")
     */
    public function addFavAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $music = $em->getRepository("AMMusicBundle:Music")->find($id);

        if(!$music){
            throw $this->createNotFoundException('Unable to find Music entity.');
        }
        $user = $this->getUser();
        $user->addFavMusic($music);
        $em->persist($user);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'success',
            'Music added to Favs'
        );

        return $this->redirect($this->generateUrl('music_show', array('id' => $music->getId())));
    }

    /**
     * @Route("/removefav/{id}", requirements={"id" = "\d+"}, name="user_removefav")
     * @Template("AMMusicBundle:Music:show.html.twig")
     * @Method("POST")
     */
    public function removeFavAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $music = $em->getRepository('AMMusicBundle:Music')->find($id);

        if(!$music){
            throw $this->createNotFoundException('Unable to find Music entity.');
        }
        $user = $this->getUser();
        $user->removeFavMusic($music);
        $em->persist($user);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'warning',
            'Music removed from Favs.'
        );

        // faire un return this redirect d'une route generée
        return $this->redirect($this->generateUrl('user_favs'));
    }

    /**
     * @Route("/favs", name="user_favs")
     * @Template()
     */
    public function indexFavAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $user = $em->getRepository('AMUserBundle:User')->findFavsWithMusicFiles($user->getId());

        if(!$user) {
            throw $this->createNotFoundException('Unable to find User');
        }
        return array('user' => $user);
    }

    /**
     * @param $user
     */
    private function authenticateUser($user)
    {
        $token = new UsernamePasswordToken(
            $user,
            $user->getPassword(),
            'user_area',
            $user->getRoles()
        );
        $securityContext = $this->container->get('security.context');
        $securityContext->setToken($token);
    }

    private function createEditForm($user)
    {
        $form = $this->createForm(new UserType(), $user, array(
            'action' => $this->generateUrl('user_update', array('id' => $user->getId())),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
} 