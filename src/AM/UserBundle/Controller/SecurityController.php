<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 27/03/14
 * Time: 11:56
 */

namespace AM\UserBundle\Controller;

use AM\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends Controller
{
    /**
     * LoginAction
     *
     * @Route("/login", name = "user_login")
     * @Template()
     */
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        return array(
            // last username entered by the user
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        );
    }

    /**
     * LoginCheck
     *
     * @Route("/login_check", name = "user_login_check")
     */
    public function loginCheckAction()
    {
    }

    /**
     * Logout
     *
     * @Route("/logout", name = "user_logout")
     */
    public function logoutAction()
    {
    }

} 