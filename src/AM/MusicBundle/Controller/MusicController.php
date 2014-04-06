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
use AM\MusicBundle\Form\CommentType;
use AM\MusicBundle\Entity\Comment;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
            $music->getMusicFiles()->upload(); // essayer de le faire avec les event prepersist
            $em->persist($music);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                'The music has been added !'
            );

            return $this->redirect($this->generateUrl('music_show', array('id' => $music->getId())));
        }
        return array(
            'music' => $music,
            'form'   => $form->createView(),
        );
    }



    /**
     * Displays a form to create a new Music entity.
     *
     * @Route("mymusics/add", name="music_new")
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
     * @Route("/{id}", requirements={"id" = "\d+"}, name="music_show")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function showAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $music = $em->getRepository('AMMusicBundle:Music')->findWithCommentsAndUser($id);
        if (!$music) {
            throw $this->createNotFoundException('Unable to find Music entity.');
        }

        $deleteForm = $this->createDeleteForm($music);

        $comment = new Comment();

        $commentForm = $this->createCreateCommentForm($comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isValid()) {
            $user = $this->getUser();
            if(!($user instanceof User)) {
                throw new \Exception('The User is not valid.');
            }
            $comment->setUser($user);
            $comment->setMusic($music);

            $this->get('session')->getFlashBag()->add(
                'success',
                'Comment added !'
            );

            $em->persist($comment);
            $em->flush();

            return $this->redirect($this->generateUrl('music_show', array('id' => $music->getId())));
        }

        return array(
            'music'      => $music,
            'delete_form' => $deleteForm->createView(),
            'comment_form' => $commentForm->createView(),
        );
    }

    /**
     * @Route("/comment/delete/{id}", name="comment_delete")
     * @Template("AMMusicBundle:Music:show.html.twig")
     * @Method("POST")
     */
    public function deleteCommentAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository('AMMusicBundle:Comment')->find($id);

        if(!$comment)
        {
            throw $this->createNotFoundException('Unable to find Comment entity.');
        }

        $music = $comment->getMusic();
        if(!$comment)
        {
            throw $this->createNotFoundException('Unable to find Music entity.');
        }
           if ($this->getUser()->getId() != $comment->getUser()->getId())
           {
               throw new AccessDeniedException();
           }

        $commentDeleteForm = $this->createDeleteCommentForm($comment);
        $commentDeleteForm->handleRequest($request);


        $em->remove($comment);
        $em->flush();

        $this->get('session')->getFlashBag()->add(
            'warning',
            'The comment has been removed.'
        );


        return $this->redirect($this->generateUrl('music_show', array('id' => $music->getId())));

        return array(
            'music' => $music,
        );
    }



    /**
     * Displays a form to edit an existing Music entity.
     *
     * @Route("/mymusics/edit/{id}", name="music_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $music = $em->getRepository('AMMusicBundle:Music')->find($id);

        if(!$this->getUser()){
            throw $this->createNotFoundException('No user connected.');
        }
        if (!$music) {
            throw $this->createNotFoundException('Unable to find Music entity.');
        }
        if($this->getUser()->getId() != $music->getUser()->getId()) {
            throw new AccessDeniedException('You are not allowed to access this page');
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
     * Edits an existing Music entity.
     *
     * @Route("/mymusics/edit/{id}", name="music_update")
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

            $this->get('session')->getFlashBag()->add(
                'success',
                'The informations have been changed.'
            );

            return $this->redirect($this->generateUrl('mymusics'));
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
     * @Route("/mymusics/delete/{id}", name="music_delete")
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

        $this->get('session')->getFlashBag()->add(
            'warning',
            'The music has been removed.'
        );

        return $this->redirect($this->generateUrl('mymusics'));
    }

    private function createDeleteForm(Music $entity)
    {
        $form = $this->createForm(new MusicType(), $entity, array(
            'action' => $this->generateUrl('music_delete', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        return $form;
    }

    private function createCreateForm(Music $entity)
    {
        $form = $this->createForm(new MusicType(), $entity, array(
            'action' => $this->generateUrl('music_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    private function createEditForm(Music $entity)
    {
        $form = $this->createForm(new MusicType(), $entity, array(
            'action' => $this->generateUrl('music_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        return $form;
    }
    private function createCreateCommentForm(Comment $comment)
    {
        $commentForm = $this->createForm(new CommentType(), $comment);

        return $commentForm;
    }

    private function createDeleteCommentForm(Comment $comment)
    {
        $deleteForm = $this->createForm(new CommentType(), $comment);

        return $deleteForm;
    }
}
