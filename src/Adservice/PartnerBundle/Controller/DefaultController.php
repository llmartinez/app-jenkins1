<?php

namespace Adservice\PartnerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Adservice\PartnerBundle\Form\PartnerType;
use Adservice\PartnerBundle\Entity\Partner;
use Adservice\UtilBundle\Entity\Region;
use Adservice\UtilBundle\Entity\Province;
use Adservice\UtilBundle\Entity\Pagination;
use Adservice\UtilBundle\Controller\DefaultController as UtilController;
use Adservice\TicketBundle\Controller\DefaultController as DefaultC;

class DefaultController extends Controller {

    /**
     * Listado de todos los socios de la bbdd
     * @throws AccessDeniedException
     */
    public function listAction($page=1) {

        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false) {
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();

        $params[] = array();

        $pagination = new Pagination($page);

        $partners = $pagination->getRows($em, 'PartnerBundle', 'Partner', $params, $pagination);

        $length = $pagination->getRowsLength($em, 'PartnerBundle', 'Partner', $params);

        $pagination->setTotalPagByLength($length);

        return $this->render('PartnerBundle:Default:list.html.twig', array( 'all_partners' => $partners,
                                                                            'pagination'   => $pagination,));
    }
    /**
     * Crea un socio en la bbdd
     * @throws AccessDeniedException
     */
    public function newPartnerAction() {
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();
        $partner = new Partner();
        $request = $this->getRequest();
        $form = $this->createForm(new PartnerType(), $partner);
        $form->bindRequest($request);

        if ($request->getMethod() == 'POST') {
            if ($form->isValid()) {
                /*CHECK CODE PARTNER NO SE REPITA*/
                $code = UtilController::getCodePartnerUnused($em, $partner->getCodePartner());
                if($code != $partner->getCodePartner())
                {
                    $flash = 'El codigo de Socio ya esta en uso, el primer numero disponible es: '.$code;
                    $this->get('session')->setFlash('error', $flash);
                }
                else{
                    $partner = DefaultC::newEntity($partner, $this->get('security.context')->getToken()->getUser());
                    $this->savePartner($em, $partner);

                    return $this->redirect($this->generateUrl('partner_list'));
                }
            }
            else{
                $partner->setCodePartner(UtilController::getCodePartnerUnused($em));
                $flash = 'El primer numero disponible es: '.$partner->getCodePartner();
                $this->get('session')->setFlash('info', $flash);
            }
        }
        else{
            $partner->setCodePartner(UtilController::getCodePartnerUnused($em));
            $flash = 'El primer numero disponible es: '.$partner->getCodePartner();
            $this->get('session')->setFlash('info', $flash);
        }

        return $this->render('PartnerBundle:Default:newPartner.html.twig', array('partner'    => $partner,
                                                                                 'form_name'  => $form->getName(),
                                                                                 'form'       => $form->createView()));
    }

     /**
     * Obtener los datos del partner a partir de us ID para poder editarlo (solo lo puede hacer el ROLE_ADMIN)
     * Si la petición es GET  --> mostrar el formulario
     * Si la petición es POST --> save del formulario
     */
    public function editPartnerAction($id){
        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $partner = $em->getRepository("PartnerBundle:Partner")->find($id);

        if (!$partner) throw $this->createNotFoundException('Partner no encontrado en la BBDD');

        $petition = $this->getRequest();
        $form = $this->createForm(new PartnerType(), $partner);


        if ($petition->getMethod() == 'POST') {

            $last_code = $partner->getCodePartner();
            $form->bindRequest($petition);

            if ($form->isValid())
            {
                /*CHECK CODE PARTNER NO SE REPITA*/
                $code = UtilController::getCodePartnerUnused($em, $partner->getCodePartner());
                if($code != $partner->getCodePartner() and $last_code != $partner->getCodePartner())
                {
                    $flash = 'El codigo de Socio ya esta en uso, el primer numero disponible es: '.$code.' (valor actual '.$last_code.').';
                    $this->get('session')->setFlash('error', $flash);
                }
                else{
                    $this->savePartner($em, $partner);
                    return $this->redirect($this->generateUrl('partner_list'));
                }
            }
        }

        return $this->render('PartnerBundle:Default:editPartner.html.twig', array('partner'    => $partner,
                                                                                  'form_name'  => $form->getName(),
                                                                                  'form'       => $form->createView()));
    }

    /**
     * Elimina el socio con $id de la bbdd
     * @param Int $id
     * @throws AccessDeniedException
     * @throws CreateNotFoundException
     */
    public function deletePartnerAction($id){

        if ($this->get('security.context')->isGranted('ROLE_ADMIN') === false){
            throw new AccessDeniedException();
        }
        $em = $this->getDoctrine()->getEntityManager();
        $partner = $em->getRepository("PartnerBundle:Partner")->find($id);
        if (!$partner) throw $this->createNotFoundException('Partner no encontrado en la BBDD');

        $em->remove($partner);
        $em->flush();

        return $this->redirect($this->generateUrl('partner_list'));
    }

     /**
     * Hace el save de un partner
     * @param EntityManager $em
     * @param Partner $partner
     */
    private function savePartner($em, $partner){
        $partner->setModifiedAt(new \DateTime(\date("Y-m-d H:i:s")));
        $partner->setModifiedBy($this->get('security.context')->getToken()->getUser());
        $em->persist($partner);
        $em->flush();
    }

}
