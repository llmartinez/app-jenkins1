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

        $token = $request->get("token");

        ///////////////////////////////////////////////////////////////////////////////////////
        // Mostrar Token encriptado para test
        ///////////////////////////////////////////////////////////////////////////////////////
        // $em = $this->getDoctrine()->getManager();
        // $user = $em->getRepository('UserBundle:User')->findOneById(48); //ganixtalleres
        // $tok = $user->getToken();
        // $enc = $this->encryptADS($tok);
        // $dec = $this->decryptADS($enc);
        // var_dump('Token: '.$tok);
        // var_dump('Encript: '.$enc);
        // var_dump('Decript => ');var_dump($dec);
        // die;
        ///////////////////////////////////////////////////////////////////////////////////////

        if($token != null)
        {
            $em = $this->getDoctrine()->getManager();
            $valid_hashes = $this->get('security')->decryptADS($token);
            $_SESSION['autologin'] = false;
            $_SESSION['marca'] = null;
            $_SESSION['modelo'] = null;
            $_SESSION['version'] = null;
            $_SESSION['description'] = null;
            $_SESSION['plateNumber'] = null;
            if($request->get('techdocIdVersion') != null){ 
                $_SESSION['autologin'] = true;
                $version = $em->getRepository('CarBundle:Version')->findOneById($request->get('techdocIdVersion'));
                if($version != null){
                    $_SESSION['marca'] = $version->getMarca()->getId();
                    $_SESSION['modelo'] = $version->getModel()->getId();
                    $_SESSION['version'] = $request->get('techdocIdVersion');
                }
                if($request->get('plateNumber') != null){
                    $_SESSION['plateNumber'] = $request->get('plateNumber');
                }
                if($request->get('description') != null){
                    $_SESSION['description'] = $request->get('description');
                }
            }
//            else{
//                $make = $request->get('make');
//                $model = $request->get('model');
//                $version = $request->get('version');
//                if($make != null ){
//                    $_SESSION['error'] = $this->checkValues($make, $model, $version);
//                }
//            }
            
            
            foreach ($valid_hashes as $valid_hash) {
                if($valid_hash != "") $hash = $valid_hash;
            }
            if(isset($hash) and $hash != null and $hash != "")
            {
                $user = $em->getRepository('UserBundle:User')->findOneByToken($hash);

                if($user != null) {
                  
    				$key = new UsernamePasswordToken($user, $user->getPassword(), "public", $user->getRoles());
    				$this->get("security.token_storage")->setToken($key);

    				// Fire the login event
    				$event = new InteractiveLoginEvent($request, $key);
    				$this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

                	return $this->redirect($this->generateUrl('_login'));
                }
                else throw new AccessDeniedException();
            }
            else throw new AccessDeniedException();
    	}
        else{
            throw new AccessDeniedException();

            ///////////////////////////////////////////////////////////////////////////////////////
            // Mostrar Token encriptado para test
            ///////////////////////////////////////////////////////////////////////////////////////
            // $em = $this->getDoctrine()->getManager();
            // $user = $em->getRepository('UserBundle:User')->findOneById(48); //ganixtalleres
            // $tok = $user->getToken();
            // $enc = $this->encryptADS($tok);
            // $dec = $this->decryptADS($enc);
            // var_dump('Token: '.$tok);
            // var_dump('Encript: '.$enc);
            // var_dump('Decript => ');
            // var_dump($dec);

            // echo "
            //     <form action='".$this->generateUrl('autologin')."' method='GET'>
            //         <input id='token' name='token' type='text' value='".$tok."'><input type='submit'>
            //     </form>";
            // die;
            ///////////////////////////////////////////////////////////////////////////////////////
        }
        return $this->render('UserBundle:Default:login.html.twig');
    }
    
//    private function checkValues($idMake, $idModel, $idVersion){
//        $em = $this->getDoctrine()->getManager();
//        $make = $em->getRepository('CarBundle:Brand')->findOneById($idMake);
//        $model = $em->getRepository('CarBundle:Model')->findOneBy(array('brand' => $idMake,
//                'id' => $idModel));
//        $version = $em->getRepository('CarBundle:Version')->findOneBy(array('marca' => $idMake, 'model' => $idModel, 'id' => $idVersion));
//        
//        $error = '';
//        if($idVersion != null && $version == null){
//            $error = $this->get('translator')->trans('error.version_not_found');
//        }
//        $_SESSION['version'] = $idVersion;
//        
//        if($idModel != null && $model == null){
//            $error = $this->get('translator')->trans('error.model_not_found');
//        }
//        $_SESSION['modelo'] = $idModel;
//        
//        if($idMake != null && $make == null){
//            $error = $this->get('translator')->trans('error.make_not_found');
//        }
//        $_SESSION['marca'] = $idMake;
//        return $error;                
//    }
   
    /**
     * Autologin del taller a través de un user y password
     * @throws AccessDeniedException
     * @return url
     */
    public function autologinAccessAction(Request $request){

        $login = $request->get("user");
        $password = $request->get("password");
        $_SESSION['autologin'] = false;
        if($login != null && $password != null)
        {
            $em = $this->getDoctrine()->getManager();
            $valid_hashes_login = $this->get('security')->decryptADS($login);
            $valid_hashes_password = $this->get('security')->decryptADS($password);
            
            $_SESSION['autologin'] = true;
            if($request->get('techdocIdVersion') != null){           
                $version = $em->getRepository('CarBundle:Version')->findOneById($request->get('techdocIdVersion'));
                if($version != null){
                    $_SESSION['marca'] = $version->getMarca()->getId();
                    $_SESSION['modelo'] = $version->getModel()->getId();
                    $_SESSION['version'] = $request->get('techdocIdVersion');
                }
                else {
                    $_SESSION['marca'] = null;
                    $_SESSION['modelo'] = null;
                    $_SESSION['version'] = null;
                }
                
                if($request->get('plateNumber') != null){
                    $_SESSION['plateNumber'] = $request->get('plateNumber');
                }
                else {
                    $_SESSION['plateNumber'] = null;
                }
                if($request->get('description') != null){
                    $_SESSION['description'] = $request->get('description');
                }
                else {
                    $_SESSION['description'] = null;
                }
            }
            foreach ($valid_hashes_login as $valid_hash) {
                if($valid_hash != "") $hash_login = $valid_hash;
            }
            foreach ($valid_hashes_password as $valid_hash) {
                if($valid_hash != "") $hash_password = $valid_hash;
            }
            if((isset($hash_login) && $hash_login != null && $hash_login != "") && (isset($hash_password) && $hash_password != null && $hash_password != ""))
            {           
                $user = $em->getRepository('UserBundle:User')->findOneByUsername($hash_login);
                if($user != null) {
                    $encoder  = $this->container->get('security.encoder_factory')->getEncoder($user);
                    $pass     = $encoder->encodePassword( $hash_password, $user->getSalt());

                    if($user->getPassword() == $pass) {

                        $key = new UsernamePasswordToken($user, $user->getPassword(), "public", $user->getRoles());
                        $this->get("security.token_storage")->setToken($key);

                        // Fire the login event
                        $event = new InteractiveLoginEvent($request, $key);
                        $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

                        return $this->redirect($this->generateUrl('_login'));
                    }
                    else return $this->redirect($this->generateUrl('_login'));
                }
                else return $this->redirect($this->generateUrl('_login'));
            }
            else return $this->redirect($this->generateUrl('_login'));
    	}
        else{
            return $this->redirect($this->generateUrl('_login'));

            ///////////////////////////////////////////////////////////////////////////////////////
            // Mostrar Token encriptado para test
            ///////////////////////////////////////////////////////////////////////////////////////
            // $em = $this->getDoctrine()->getManager();
            // $user = $em->getRepository('UserBundle:User')->findOneById(48); //ganixtalleres
            // $tok = $user->getToken();
            // $enc = $this->encryptADS($tok);
            // $dec = $this->decryptADS($enc);
            // var_dump('Token: '.$tok);
            // var_dump('Encript: '.$enc);
            // var_dump('Decript => ');
            // var_dump($dec);

            // echo "
            //     <form action='".$this->generateUrl('autologin')."' method='GET'>
            //         <input id='token' name='token' type='text' value='".$tok."'><input type='submit'>
            //     </form>";
            // die;
            ///////////////////////////////////////////////////////////////////////////////////////
        }
        return $this->render('UserBundle:Default:login.html.twig');
    }
}