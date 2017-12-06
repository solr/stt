<?php

namespace MainBundle\Controller;

use MainBundle\Entity\Project;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use \Doctrine\Common\Util\Debug as MyDebug;

class NewProjectController extends Controller
{
    /**
     * @Route("/new-project")
     */
    public function indexAction(Request $request)
    {
        $return = [];
        $return['uri'] = '/';

        $isAdmin = $this->get('security.context')->isGranted('ROLE_ADMIN');
        $return["isAdmin"] = $isAdmin;
        
        $project = new Project();
        $form = $this->createFormBuilder($project)
            ->add('name', TextType::class, array('label' => 'Projektname','attr'=> array('class'=>'project_name')))
            ->add('sort', TextType::class,array('label' => 'Sortierung'))
            ->add('submit', SubmitType::class, array('label' => 'Projekt erstellen'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($project);
                $em->flush();
            } catch (\Doctrine\DBAL\DBALException $e) {
                return $this->render(
                    'MainBundle:NewProject:new-project.html.twig', [
                    'newProjectAdded' => 'fail',
                    'errorMessage' => $e->getMessage(),
                    'form' => $form->createView(),
                    'data' => $return
                ]);
            }

            return $this->render(
                'MainBundle:NewProject:new-project.html.twig', [
                'newProjectAdded' => 'success',
                'form' => $form->createView(),
                'data' => $return
            ]);
        }
        return $this->render('MainBundle:NewProject:new-project.html.twig', array(
            'form' => $form->createView(),
            'data' => $return
        ));
        
    }
}
