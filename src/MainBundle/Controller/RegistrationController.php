<?php
// src/MainBundle/Controller/RegistrationController.php
namespace MainBundle\Controller;

use MainBundle\Form\UserType;
use MainBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends Controller
{
    /**
     * @Route("/register", name="user_registration")
     */
    public function registerAction(Request $request)
    {
        // 1) build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setRole('ROLE_USER');
            // 4) save the User!
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user
            return $this->render(
                'MainBundle:Security:login.html.twig', array(
                    'register' => 'success'
            ));
            #return $this->redirectToRoute('login');
        }

        $return['uri'] = '';
        $return['register'] = true;

        return $this->render(
            'MainBundle:Security:register.html.twig',
            array(
                'form' => $form->createView(),
                'data' => $return
            )
        );
    }
}