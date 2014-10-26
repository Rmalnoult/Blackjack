<?php

namespace LasVenturas\BlackjackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use LasVenturas\BlackjackBundle\Entity\User;

class DefaultController extends Controller
{


    public function isLoggedIn(Request $request)
    {
        // try if user is logged in :
        // if a session is found with the blackJackPlayer parameter => return true
        // if no session parameter is found => return false

        // symfony's way to get session parameter
        $session = $request->getSession();
         if ($session->get('blackJackPlayer')) {
            return true;               

        } else {
            // si aucun parametre de session n'a été trouvé
            return false;
        }              
    }



    public function login()
    {

        // when no session parameter is found, this function is played

        $request = $this->getRequest();
        $session = $request->getSession();

        var_dump('session found');
        var_dump('dat session: '.$session->get('blackJackPlayer'));

        //get the name associated with the session
        $sessionName = $session->get('blackJackPlayer');

        // doctrine's syntax to load the User
        $repository = $this->getDoctrine()
            ->getRepository('LasVenturasBlackjackBundle:User');
        // get the username found in the session from the user database
        $user = $repository->findOneByName($sessionName);
        // store the username in a variable
        $userName = $user->getName();
        var_dump('userName: '.$userName);

        if (!$user) {
            throw $this->createNotFoundException(
                'Aucun User trouvé pour cet id : '
            );
        }


        return $this->render('LasVenturasBlackjackBundle:Default:index.html.twig', array('name' => $userName )); 
    }




    public function signup($request)
    {   
        // initialize a new user entity    
        $user = new User();  
        // create a signup form       
        $form = $this->createFormBuilder($user)
            ->add('Name', 'text')
            ->add('save', 'submit', array('label' => ' Register '))
            ->getForm();
        // get the validated form and handle it
        $form->handleRequest($request);

        // if form is valid
        if ($form->isValid()) {
            var_dump('signing up');
            // store the username from the form in a variable
            $userName = $user->getName();
            // store the username in database
            $user->setName($userName); 
            // session
            $session = $request->getSession();
            // store an attribute in session for reuse during a later user request
            $session->set('blackJackPlayer', $userName);

            var_dump('user: '.$user->getName());
            var_dump('userinsession: '.$session->get('blackJackPlayer'));
            // initialize the user's wallet
            $user->setWallet(10000);

            // validate database operations
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            // redirect to home
            return $this->redirect('/');
        }

        // if form is not yet complete or invalid => render the signup page + passing the form to it
        return $this->render('LasVenturasBlackjackBundle:Default:signup.html.twig', array(
            'form' => $form->createView(),
        ));
         
    }
}
