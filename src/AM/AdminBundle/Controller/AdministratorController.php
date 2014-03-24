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
        if($this->getUser()->getId() != $id) {
            throw new AccessDeniedException('You are not allowed to access this page');
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

    public function createEditForm(Administrator $entity)
    {
        $form = $this->createForm(new AdministratorType(), $entity, array(
            'action' => $this->generateUrl('admin_profile_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

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
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($entity);
            $password = $encoder->encodePassword($entity->getPlainPassword(), $entity->getSalt());
            $entity->setPassword($password);

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

} 