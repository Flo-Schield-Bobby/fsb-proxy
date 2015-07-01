<?php

namespace Fsb\Proxy\AppBundle\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AuthenticationController extends Controller
{
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        return $this->render('FsbProxyAppBundle:Security:login.html.twig', array(
            'last_username' => $authenticationUtils->getLastUsername(),
            'last_password' => '',
            'error'         => $authenticationUtils->getLastAuthenticationError(),
        ));
    }
}
