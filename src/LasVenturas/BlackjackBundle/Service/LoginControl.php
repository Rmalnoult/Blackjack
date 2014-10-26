<?php
namespace LasVenturas\BlackjackBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use LasVenturas\BlackjackBundle\Entity\User;


class LoginControl {

    public function isLoggedIn(Request $request)
    {

    	var_dump('service works');
        // try if user is logged in :
        // if a session is found with the blackJackPlayer parameter => return true
        // if no session parameter is found => return false

        
         if ($request->cookies->get('blackJackPlayer')) {
            return true;               

        } else {
            // si aucun parametre de session n'a été trouvé
            return false;
        }              
    }

    public function whoIsThisUser($request) {
        //get the name associated with the cookie
        $cookieName = $request->cookies->get('blackJackPlayer');
        return $cookieName;    	
    }

    public function redirectToHome() {
        $response = new RedirectResponse('/');
        return $response;
    }    


}