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

    public function createEditForm($user)
    {
        $form = $this->createForm(new UserType(), $user, array(
            'action' => $this->generateUrl('user_update', array('id' => $user->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * @Route("/update/{id}", name = "user_update")
     * @Template("AMAdminBundle:User:edit.html.twig")
     * @Method("PUT")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AMUserBundle:User')->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createEditForm($user);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->setEncodedPassword($user);

            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Your informations have been changed'
            );

            return $this->redirect($this->generateUrl('user_show', array('id' => $user->getId())));
        }

        return array(
            'entity'      => $user,
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
        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if($form->isValid()) {
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($entity);
            $encodedPassword = $encoder->encodePassword($entity->getPlainPassword(), $entity->getSalt());
            $entity->setPassword($encodedPassword);
            $entity->eraseCredentials();

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('site_home'));
        }
        else{
            var_dump($form->getErrors());
            die();
        }
        return array(
            'entity' => $entity,
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
        $user = $em->getRepository("AMUserBundle:User")->find($id);

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
} 