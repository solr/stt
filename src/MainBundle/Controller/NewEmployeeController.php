<?php

namespace MainBundle\Controller;

use MainBundle\Entity\Employee;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class NewEmployeeController extends Controller
{
    /**
     * @Route("/new-employee")
     */
    public function indexAction(Request $request)
    {
        $return = [];
        $return['uri'] = '/';

        $isAdmin = $this->get('security.context')->isGranted('ROLE_ADMIN');
        $return["isAdmin"] = $isAdmin;
        
        $employee = new Employee();
        $form = $this->createFormBuilder($employee)
            ->add('username', TextType::class, array('label' => 'Benutzername'))
            ->add('firstname', TextType::class, array('label' => 'Vorname'))
            ->add('lastname', TextType::class, array('label' => 'Nachname'))
            ->add('submit', SubmitType::class, array('label' => 'Mitarbeiter hinzufügen'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($employee);
                $em->flush();
            } catch (\Doctrine\DBAL\DBALException $e) {
                return $this->render(
                    'MainBundle:NewEmployee:new-employee.html.twig', [
                    'newEmployeeAdded' => 'fail',
                    'errorMessage' => $e->getMessage(),
                    'form' => $form->createView(),
                    'data' => $return
                ]);
            }
            return $this->render(
                'MainBundle:NewEmployee:new-employee.html.twig', [
                'newEmployeeAdded' => 'success',
                'form' => $form->createView(),
                'data' => $return
            ]);

        }
        return $this->render('MainBundle:NewEmployee:new-employee.html.twig', array(
            'form' => $form->createView(),
            'data' => $return
        ));
        
    }

}
