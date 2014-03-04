<?php //
//
//namespace Adservice\UtilBundle\Controller;
//
//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Adservice\UtilBundle\Entity\Document as Document;
//use Adservice\UtilBundle\Form\DocumentType;
//
//class DocumentController extends Controller {
//
//    public function uploadAction() {
//        $em = $this->getDoctrine()->getEntityManager();
//        $request = $this->getRequest();
//
//        $document = new Document();
//        
//        echo "oaloalalallalalalalall";die;
//
//        $form = $this->createForm(new DocumentType(), $document);
//
//        if ($this->getRequest()->getMethod() === 'POST') {
//
//            if ($form->isValid()) {
//
//                $form->bindRequest($this->getRequest());
////                $this->upload();
//                $em->persist($document);
//                $em->flush();
//
//                $this->redirect($this->generateUrl('upload'));
//            }
//        }
//
//        return $this->render("UtilBundle:Document:upload.html.twig", array('form' => $form->createView()));
//    }

//    private function upload() {
//
//        // the file property can be empty if the field is not required
//        if (null === $this->getFile()) {
//            return;
//        }
//
//        // use the original file name here but you should
//        // sanitize it at least to avoid any security issues
//        // move takes the target directory and then the
//        // target filename to move to
//        $this->getFile()->move(
//                $this->getUploadRootDir(), $this->getFile()->getClientOriginalName()
//        );
//
//        // set the path property to the filename where you've saved the file
//        $this->path = $this->getFile()->getClientOriginalName();
//
//        // clean up the file property as you won't need it anymore
//        $this->file = null;
//    }

//}
