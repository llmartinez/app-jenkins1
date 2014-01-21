<?php

namespace Adservice\TicketingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Adservice\TicketingBundle\Entity\Make;
use Adservice\TicketingBundle\Entity\Model;
use Adservice\TicketingBundle\Entity\Version;
use Adservice\TicketingBundle\Entity\Ticket;
use Adservice\TicketingBundle\Entity\File;
use Adservice\TicketingBundle\Entity\Subsystem;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\HttpFoundation\Response;

class TicketingController extends Controller{

    public function formAction() {
        
         $em = $this->getDoctrine()->getEntityManager();
         $request = $this->getRequest();
         /*
         $ticket = new Ticket();
        
         $form = $this->createForm(new TicketType(), $ticket);
        
         if ($request->getMethod() == 'POST') {
                
            $form->bindRequest($request);
                
            if ($form->isValid()) {
                    
                    $em->persist($usuario);
                    $em->flush();
                    
                    $sesion = $request->getSession();
                    
                    return $this->render('TicketingBundle:Ticketing:form.html.twig', array());
                }
            }
            */
         $tickets = $em->getRepository('TicketingBundle:Ticket')->findAll();
         
         return $this->render('TicketingBundle:Ticket:form.html.twig', array( 'tickets' => $tickets));
         
    }
}
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
/*
    
    public function homeAction(){
        return $this->render('TicketingBundle:Default:index.html.twig', array());
    }
    
    public function formAction($id = null){
        // Recojo el manejador de entidades
        $em = $this->getDoctrine()->getEntityManager();
        
        // Recojo el objeto request
        $request = $this->getRequest();
        
        // Miro si ha sido enviado el formulario y tengo que procesar los datos, o bien tengo que mostrarlo
        if ($request->request->all()){
            // Creo una instancia del ticket
            $ticket = new Ticket();
            
            // Recojo los campos y los archivos
            $formFields = $request->request->all();
            
            // Guardo el ticket en la BBDD
            $ticket->setVehicle($em->getRepository('TicketingBundle:Version')->findOneById($formFields['version']));
            $ticket->setSystem($em->getRepository('TicketingBundle:Subsystem')->findOneById($formFields['subsystem']));
            $ticket->setTime(time());
            $ticket->setDescription($formFields['description']);
            $ticket->setUser($em->getRepository('TicketingBundle:User')->findOneById($this->get('security.context')->getToken()->getUser()->getId()));
            if (isset($formFields['call'])){
                $ticket->setCallme(1);
            } else {
                $ticket->setCallme(0);
            }
            $ticket->setStatus(0);
            
            $em->persist($ticket);
            $em->flush();
            
            for($f=1; $f<=3; $f++){
                $currentFile = $this->getRequest()->files->get('file'.$f);
                if ($currentFile != null){
                    // He encontrado el archivo, voy a renombrarlo y a moverlo al directorio temporal
                    $fileName = time().".";
                    $fileOriginalName = $currentFile->getClientOriginalName();
                    $currentFile->move('bundles/ticketing/temp', $fileName.$fileOriginalName);
                    $ext = explode('.', $fileOriginalName);
                    $ext = $ext[count($ext)-1];
                    // Ahora lo abro y lo meto dentro de la BBDD
                    $fileContents = file_get_contents('bundles/ticketing/temp/'.$fileName.$fileOriginalName);
                    
                    $file = new File();
                    $file->setName(str_replace('.'.$ext, '', $fileOriginalName));
                    $file->setExtension($ext);
                    $file->setTimestamp(time());
                    $file->setTicket($em->getRepository('TicketingBundle:Ticket')->findOneById($ticket->getId()));
                    $file->setFile(base64_encode($fileContents));
                    
                    $em->persist($file);
                    $em->flush();
                }
            }
        }
        
        // Recojo de entrada las marcas y los sistemas
        $makes    = $em->getRepository('TicketingBundle:Make')->findBy(array(), array('name' => 'ASC'));
        $systems  = $em->getRepository('TicketingBundle:System')->findBy(array(), array('name' => 'ASC'));

        // Devuelvo el formulario
        return $this->render('TicketingBundle:Ticket:Form.html.twig', 
            array(
                'makes'=>$makes,
                'systems'=>$systems
            )
        );
    }
    
    public function getModelsAction($make){
        // Recojo el manejador de entidades.
        $em = $this->getDoctrine()->getEntityManager();
        
        // Recojo id y nombre de todos los modelos a partir de una marca, ordenado alfabéticamente.
        $qb = $em->createQueryBuilder();
        $rsModels = $qb
            ->select('m.id', 'm.name')
            ->from('TicketingBundle:Model', 'm')
            ->where('m.make = '.$make)
            ->orderBy('m.name', 'desc')
            ->getQuery()->execute();
        
        // Paso a JSON el resultado para leerlo por AJAX en la vista.
        $jsonEncoder = new JsonEncoder();
        $response = $jsonEncoder->encode($rsModels, 'json');
        
        // Retorno JSON
        return new Response($response);
    }
    
    public function getVersionsAction($model){
        // Recojo el manejador de entidades.
        $em = $this->getDoctrine()->getEntityManager();
        
        // Recojo id y nombre de todas las versiones a partir de un modelo, ordenado alfabéticamente.
        $qb = $em->createQueryBuilder();
        $rsVersion = $qb
            ->select('v.id', 'v.name')
            ->from('TicketingBundle:Version', 'v')
            ->where('v.model = '.$model)
            ->orderBy('v.name', 'desc')
            ->getQuery()->execute();
        
        // Paso a JSON el resultado para leerlo por AJAX en la vista.
        $jsonEncoder = new JsonEncoder();
        $response = $jsonEncoder->encode($rsVersion, 'json');
        
        // Retorno JSON
        return new Response($response);
    }
    
    public function getSubsysAction($sys){
        // Recojo el manejador de entidades.
        $em = $this->getDoctrine()->getEntityManager();
        
        // Recojo id y nombre de todas las versiones a partir de un modelo, ordenado alfabéticamente.
        $qb = $em->createQueryBuilder();
        $rsSubsys = $qb
            ->select('s.id', 's.name')
            ->from('TicketingBundle:Subsystem', 's')
            ->where('s.system = '.$sys)
            ->orderBy('s.name', 'desc')
            ->getQuery()->execute();
        
        // Paso a JSON el resultado para leerlo por AJAX en la vista.
        $jsonEncoder = new JsonEncoder();
        $response = $jsonEncoder->encode($rsSubsys, 'json');
        
        // Retorno JSON
        return new Response($response);
    }
    
    public function uploadImageAction(){
        $request = $this->getRequest();
        $mypost = $_POST;
        $file = $request->request->get('file');
        $E = 0;
        return new Response('OK!');
    }
 */
