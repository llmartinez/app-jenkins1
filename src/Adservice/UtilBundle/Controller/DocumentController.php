<?php

namespace Adservice\UtilBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Adservice\UtilBundle\Entity\Document as Document;
use Adservice\UtilBundle\Form\DocumentType;

class DocumentController extends Controller
{
    public function uploadAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        
        $document = new Document();
        
        $form   = $this->createForm(new DocumentType(), $document);

        if ($this->getRequest()->getMethod() === 'POST') {
            
            $form->bindRequest($this->getRequest());

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($document);
                $em->flush();

                $this->redirect($this->generateUrl('upload'));
            }

    return  $this->render("UtilBundle:Document:upload.html.twig",array('form' =>   $form->createView()));
    }
}