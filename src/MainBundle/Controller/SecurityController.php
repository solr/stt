<?php

// src/MainBundle/Controller/SecurityController.php

namespace MainBundle\Controller;

use MainBundle\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use \Doctrine\Common\Util\Debug as MyDebug;

use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {   
        $authenticationUtils = $this->get('security.authentication_utils');
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        #$lastUsername = $authenticationUtils->getLastUsername();
        #MyDebug::Dump($lastUsername);die;
        
        $return['uri'] = '/stt';
        $return['login'] = true;
        return $this->render('MainBundle:Security:login.html.twig', array(
           # 'last_username' => $lastUsername,
            'error'         => $error,
            'data'          => $return
        ));
    }

}