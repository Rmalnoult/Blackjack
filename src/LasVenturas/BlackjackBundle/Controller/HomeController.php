<?php

namespace LasVenturas\BlackjackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Cookie;


use LasVenturas\BlackjackBundle\Entity\User;

class HomeController extends Controller
{

    public function isLoggedIn()
    {
         if ($session->get('blackJackPlayer')) {

            $sessionName = $session->get('blackJackPlayer');

            $repository = $this->getDoctrine()
                ->getRepository('LasVenturasBlackjackBundle:User');

            $user = $repository->findOneByName($sessionName);
            $userName = $user->getName();

            if ($sessionName = $userName){
                return true;               
            }

        } else {
            return false;
        }              
    }


    public function indexAction(Request $request)
    {
    	// if user is auth with a cookie, get info from cookie, say hello, offer link to play a game
    	// else if no cookie is available, welcome new player, present a form to auth (just a username)and let's play a game.
        // var_dump($Response);

        if ($this->isLoggedIn()) {
            var_dump('loggedIn');
            return $this->login($request);
        } else {
            var_dump('not loggedIn');
            return $this->signup($request);
        }
    }



    public function login()
    {
        $request = $this->getRequest();
        $session = $request->getSession();
        var_dump('session'.$session);

        var_dump('cookie found');
        var_dump('dat cookie: '.$_COOKIE['blackJackPlayer']);
        $cookie = $_COOKIE['blackJackPlayer'];

        $repository = $this->getDoctrine()
            ->getRepository('LasVenturasBlackjackBundle:User');

        $user = $repository->findOneByName($cookie);

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
        $user = new User();         
        $form = $this->createFormBuilder($user)
            ->add('Name', 'text')
            ->add('save', 'submit', array('label' => ' Register '))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            var_dump('signing up');
            $userName = $user->getName();
            $user->setName($userName); 

            $session = $request->getSession();
            // store an attribute for reuse during a later user request
            $session->set('blackJackPlayer', $userName);

            var_dump('user: '.$user->getName());
            var_dump('userinsession: '.$session->get('blackJackPlayer'));
            $user->setWallet(10000);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirect('/');
        }

        return $this->render('LasVenturasBlackjackBundle:Default:signup.html.twig', array(
            'form' => $form->createView(),
        ));
         
    }
}
