<?php

namespace MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use \Doctrine\Common\Util\Debug as Debug;


class IndexController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $return = [];
        $return['uri'] = '';

        $isAdmin = $this->get('security.context')->isGranted('ROLE_ADMIN');
        $return["isAdmin"] = $isAdmin;
        
        $return["currentYear"] = (int)date("Y");
        $return["currentWeek"] = (int)date("W");

        
        $user = $this->get('security.context')->getToken()->getUser();
        $return['user'] = $user;
        $em = $this->getDoctrine()->getManager();
        $return["employees"] = $em->getRepository('MainBundle:Employee')->findBy(array(), array('firstname' => 'asc'));

        
        return $this->render('MainBundle:Index:index.html.twig', array(
            'data' => $return
        ));
        
    }

}
