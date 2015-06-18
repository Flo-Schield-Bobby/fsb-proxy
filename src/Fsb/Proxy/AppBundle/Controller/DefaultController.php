<?php

namespace Fsb\Proxy\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('FsbProxyAppBundle:Default:index.html.twig', array('name' => $name));
    }
}
