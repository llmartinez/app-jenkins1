<?php

namespace Adservice\UtilBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

use Adservice\UtilBundle\Controller\UtilController;

class SecurityController extends Controller{

    /**
     * Autologin del taller a través de un token
     * @throws AccessDeniedException
     * @return url
     */
    public function autologinAction(Request $request){

    	$em = $this->getDoctrine()->getEntityManager();

        $token = $request->get("token");

        $headers = $request->headers->all();
var_dump($_POST);
var_dump($_GET);
var_dump($token);
var_dump($headers);

var_dump($request);
die;

        ///////////////////////////////////////////////////////////////////////////////////////
        // Mostrar Token encriptado para test
        ///////////////////////////////////////////////////////////////////////////////////////
        // $user = $em->getRepository('UserBundle:User')->findOneById(3318); //adpruebas
        // $tok = $user->getToken();
        // $enc = $this->encryptADS($tok);
        // $dec = $this->decryptADS($enc);
        // var_dump('Token: '.$tok);
        // var_dump('Encript: '.$enc);
        // var_dump('Decript => ');
        // var_dump($dec);die;
        ///////////////////////////////////////////////////////////////////////////////////////

    	if($token != null)
    	{
            $valid_hashes = $this->decryptADS($token);

            foreach ($valid_hashes as $valid_hash) {
                if($valid_hash != "") $hash = $valid_hash;
            }

            if(isset($hash) and $hash != null and $hash != "")
            {
    			$user = $em->getRepository('UserBundle:User')->findOneByToken($hash);

				$key = new UsernamePasswordToken($user, $user->getPassword(), "public", $user->getRoles());
				$this->get("security.context")->setToken($key);

				// Fire the login event
				$event = new InteractiveLoginEvent($request, $key);
				$this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

            	return $this->redirect($this->generateUrl('user_index'));
            }
            else throw new AccessDeniedException();
    	}
    	else throw new AccessDeniedException();

        return $this->render('UtilBundle:Default:help.html.twig');
    }

    /**
     * Method to encrypt a plain text string
     * initialization vector(IV) has to be the same when encrypting and decrypting
     * PHP 5.4.9
     *
     * https://naveensnayak.wordpress.com/2013/03/12/simple-php-encrypt-and-decrypt/
     *
     * @param string $string: string to encrypt or decrypt (the password)
     * @return string
     */
    public function encryptADS($string){

        $output = false;
        //encryptiom method name
        $encrypt_method = "AES-256-CBC";
        //the current day "YYYYmmdd"
        $secret_key = date_format(date_create('now'), 'Ymd');

        //secret iv
        $secret_iv = $this->container->getParameter('secret_iv');

        // hash
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        //encrypt with openssl
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);

        //encode with base64
        $output = base64_encode($output);

        return $output;
    }

    public function decryptADS($string){

        $output = false;

        $encrypt_method = "AES-256-CBC";
        $secret_iv = $this->container->getParameter('secret_iv');

        // valid keys...
        $keys[0] = hash('sha256', date_format(date_create('now'), 'Ymd'));          //today
        $keys[1] = hash('sha256', date_format(date_create('now -1day'), 'Ymd'));    //yesterday
        $keys[2] = hash('sha256', date_format(date_create('now +1day'), 'Ymd'));    //tomorrow

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        $output = base64_decode($string);
        foreach ($keys as $key) {
            $valid_hashes[] = openssl_decrypt($output, $encrypt_method, $key, 0, $iv);
        }

        return $valid_hashes;
    }
}