<?php

namespace LasVenturas\BlackjackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Cookie;

use LasVenturas\BlackjackBundle\Entity\User;

class HomeController extends Controller
{

    public function indexAction(Request $request)
    {
        // play isLoggedIn function to see if a cookie is found
        // isLoggedIn is in a service called Login control
        $LoginControlService = $this->get('las_venturas_blackjack.loginControl');

        if ($LoginControlService->isLoggedIn($request)) {
            // var_dump('loggedIn');
            return $this->login($request);
        } else {
            // var_dump('not loggedIn');
            return $this->signup($request);
        }
    }

    public function login()
    {

        // when no cookie is found, this function is played
        $request = $this->getRequest();
        // var_dump('cookie found');
        // var_dump('dat cookie: '.$request->cookies->get('blackJackPlayer'));

        //get the name associated with the cookie
        $cookieName = $request->cookies->get('blackJackPlayer');

        // doctrine's syntax to load the User
        $repository = $this->getDoctrine()
            ->getRepository('LasVenturasBlackjackBundle:User');
        // get the username found in the cookie from the user database
        $user = $repository->findOneByName($cookieName);
        // store the username in a variable
        $userName = $user->getName();
        // var_dump('userName: '.$userName);

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

            // var_dump('user: '.$user->getName());

            // initialize the user's wallet
            $user->setWallet(10000);

            // validate database operations
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // We create a new cookie that lasts a day
            $cookie = new Cookie('blackJackPlayer', $userName, time() + 3600 * 24);
            // we implement a new response object (it's actually a redirect response, merci le symfony component qui permet cela)
            $response = new RedirectResponse('/');
            // and we pass it the cookie we have created
            // var_dump('userincookie: '.$request->cookies->get('blackJackPlayer'));
            $response->headers->setCookie($cookie);
            // Then we send by returning the response (to implement the cookie in the browser) and also redirect to home at the same time
            return $response;
        }

        // if form is not yet complete or invalid => render the signup page + passing the form to it
        return $this->render('LasVenturasBlackjackBundle:Default:signup.html.twig', array(
            'form' => $form->createView(),
        ));
         
    }   

}
