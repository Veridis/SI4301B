<?php

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

/**
 * Music controller.
 */
class MusicController extends Controller
{

    /**
     * Lists all Music entities.
     *
     * @Route("/musics", name="musics")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $musics = $em->getRepository('AMMusicBundle:Music')->findAllByDateWithUser();

        return array(
            'musics' => $musics,
        );
    }


    /**
     * Lists all Music entities for connected user.
     *
     * @Route("/mymusics", name="mymusics")
     * @Method("GET")
     * @Template()
     */
    public function userMusicAction()
    {
        $em = $this->getDoctrine()->getManager();
        $userMusics = $em->getRepository('AMMusicBundle:Music')->findAllUserMusics($this->getUser()->getId());

        return array(
            'userMusics' => $userMusics,
        );
    }


    /**
     * Creates a new Music entity.
     *
     * @Route("mymusic/add", name="music_create")
     * @Method("POST")
     * @Template("AMMusicBundle:Music:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $music = new Music();
        $form = $this->createCreateForm($music);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $user = $this->getUser();
            if(!($user instanceof User)) {
                throw new \Exception('The User is not valid.');
            }
            $music->setUser($user);
            $music->getMusicFiles()->upload();
            $em->persist($music);
            $em->flush();

            return $this->redirect($this->generateUrl('music_show', array('id' => $music->getId())));
        }
        return array(
            'music' => $music,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Music entity.
    *
    * @param Music $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Music $entity)
    {
        $form = $this->createForm(new MusicType(), $entity, array(
            'action' => $this->generateUrl('music_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Upload'));

        return $form;
    }

    /**
     * Displays a form to create a new Music entity.
     *
     * @Route("mymusic/add", name="music_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $music = new Music();
        $form   = $this->createCreateForm($music);

        return array(
            'music' => $music,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Music entity.
     *
     * @Route("/{id}", name="music_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $music = $em->getRepository('AMMusicBundle:Music')->find($id);

        if (!$music) {
            throw $this->createNotFoundException('Unable to find Music entity.');
        }

        $deleteForm = $this->createDeleteForm($music);

        return array(
            'music'      => $music,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Music entity.
     *
     * @Route("/{id}/edit", name="music_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $music = $em->getRepository('AMMusicBundle:Music')->find($id);

        if (!$music) {
            throw $this->createNotFoundException('Unable to find Music entity.');
        }

        $editForm = $this->createEditForm($music);
        $deleteForm = $this->createDeleteForm($music);

        return array(
            'music'      => $music,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Music entity.
    *
    * @param Music $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Music $entity)
    {
        $form = $this->createForm(new MusicType(), $entity, array(
            'action' => $this->generateUrl('music_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Music entity.
     *
     * @Route("/edit/{id}", name="music_update")
     * @Method("POST")
     * @Template("AMMusicBundle:Music:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $music = $em->getRepository('AMMusicBundle:Music')->find($id);

        if (!$music) {
            throw $this->createNotFoundException('Unable to find Music entity.');
        }

        $deleteForm = $this->createDeleteForm($music);
        $editForm = $this->createEditForm($music);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $music->getMusicFiles()->removeFiles();
            $music->getMusicFiles()->upload();
            $em->flush();

            return $this->redirect($this->generateUrl('music_edit', array('id' => $id)));
        }

        return array(
            'music'      => $music,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Music entity.
     *
     * @Route("/delete/{id}", name="music_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $music = $em->getRepository('AMMusicBundle:Music')->find($id);

        $form = $this->createDeleteForm($music);
        $form->handleRequest($request);
        $music->getMusicFiles()->removeFiles();

        $em->remove($music);
        $em->flush();

        return $this->redirect($this->generateUrl('mymusics'));
    }

    private function createDeleteForm(Music $entity)
    {
        $form = $this->createForm(new MusicType(), $entity, array(
            'action' => $this->generateUrl('music_delete', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        return $form;

     /*   return $this->createFormBuilder()
            ->setAction($this->generateUrl('music_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;*/
    }
}
