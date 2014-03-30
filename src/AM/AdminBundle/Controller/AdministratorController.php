<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 10/03/14
 * Time: 23:23
 */

namespace AM\AdminBundle\Controller;

use AM\AdminBundle\Entity\Administrator;
use AM\AdminBundle\Form\AdministratorType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdministratorController
 * @package AM\AdminBundle\Controller
 *
 * @Route("/profile")
 */
class AdministratorController extends Controller
{
    /**
     * @Route("/{id}", requirements={"id" = "\d+"}, name = "admin_profile")
     * @Template()
     */
    public function profileAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AMAdminBundle:Administrator')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Administrator entity.');
        }

        return array(
            'entity'    => $entity,
        );
    }


    /**
     * @Route("/{id}/edit", requirements={"id" = "\d+"}, name = "admin_profile_edit")
     * @Template()
     */
    public function editAction($id)
    {
        if(!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            if($this->getUser()->getId() != $id) {
                throw new AccessDeniedException('You are not allowed to access this page');
            }
        }

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AMAdminBundle:Administrator')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Administrator entity.');
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );

    }

    private function createEditForm(Administrator $entity)
    {
        $form = $this->createForm(new AdministratorType(), $entity, array(
            'action' => $this->generateUrl('admin_profile_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        $form->add('submit', 'submit', array('label' => 'Update'));
        if(!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            $form->remove('roles');
        }

        return $form;
    }

    /**
     * @Route("/{id}/update", name = "admin_profile_update")
     * @Template("AMAdminBundle:Administrator:edit.html.twig")
     * @Method("PUT")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AMAdminBundle:Administrator')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Administrator entity.');
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->setEncodedPassword($entity);

            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'notice',
                'Your informations have been changed'
            );

            return $this->redirect($this->generateUrl('admin_profile', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
     * @Route("/create", name="admin_create")
     * @Method("POST")
     * @Template("AMAdminBundle:Administrator:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Administrator();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->setEncodedPassword($entity);

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_profile', array('id' => $entity->getId())));
        }
        return array(
            'entity' => $entity,
            'create_form'   => $form->createView(),
        );
    }

    public function createCreateForm(Administrator $entity)
    {
        $form = $this->createForm(new AdministratorType(), $entity, array(
            'action' => $this->generateUrl('admin_create'),
            'method' => 'POST',
        ));
        $form->add('username', 'text', array('disabled' => false));
        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * @Route("/new", name="admin_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        if(!$this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException('You are not allowed to access this page');
        }
        $entity = new Administrator();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'create_form'   => $form->createView(),
        );
    }

    private function setEncodedPassword($administrator)
    {
        $factory = $this->get('security.encoder_factory');
        $encoder = $factory->getEncoder($administrator);

        $encodedPassword = $encoder->encodePassword($administrator->getPlainPassword(), $administrator->getSalt());
        $administrator->setPassword($encodedPassword);
        $administrator->eraseCredentials();
    }

} 