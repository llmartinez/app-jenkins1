<?php
namespace Adservice\TicketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

use Adservice\UtilBundle\Controller\UtilController as UtilController;
use Adservice\UtilBundle\Entity\Pagination;
use Adservice\UserBundle\Entity\User;
use Adservice\TicketBundle\Entity\Sentence;
use Adservice\TicketBundle\Form\SentenceType;

class SentenceController extends Controller
{

    /**
     * Devuelve la lista de sentencias
     */
    public function listSentenceAction(Request $request, $page=1, $country='none') {
        $em = $this->getDoctrine()->getManager();


        if (! $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
             throw new AccessDeniedException();
        }
        $params[] = array();
//        if($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
//            if ($country != 'none') $params[] = array('country', ' = '.$country);
//            else                    
//        }
//        else $params[] = array('country', ' = '.$this->getUser()->getCountry()->getId());

        $pagination = new Pagination($page);

        $sentences = $pagination->getRows($em, 'TicketBundle', 'Sentence', $params, $pagination);

        $length = $pagination->getRowsLength($em, 'TicketBundle', 'Sentence', $params);

        $pagination->setTotalPagByLength($length);

        if($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) $countries = $em->getRepository('UtilBundle:Country')->findAll();
        else $countries = array();

        return $this->render('TicketBundle:Sentence:list_sentence.html.twig', array( 'sentences'  => $sentences,
                                                                                     'pagination' => $pagination,
                                                                                     'countries'  => $countries,
                                                                                     'country'    => $country,));
    }

    /**
     * Obtener los datos de la tipologia a partir de su ID para poder editarlo (solo lo puede hacer el ROLE_ADMIN)
     * Si la petición es GET  --> mostrar el formulario
     * Si la petición es POST --> save del formulario
     */
    public function editSentenceAction(Request $request, $id) {

        if (! $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $sentence = new Sentence();

        if ($id != null) {
                          $sentence = $em->getRepository("TicketBundle:Sentence")->find($id);
                          if (!$sentence) throw $this->createNotFoundException('Sentencia no encontrado en la BBDD');
        }
        // Creamos variables de sesion para fitlrar los resultados del formulario
        if ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {

            $_SESSION['id_partner'] = ' != 0 ';

        }elseif ($this->get('security.authorization_checker')->isGranted('ROLE_SUPER_AD')) {
            $_SESSION['id_country'] = ' = '.$this->getUser()->getCountry()->getId();

        }else {
            $_SESSION['id_country'] = ' = '.$partner->getCountry()->getId();
        }
        $form = $this->createForm(new SentenceType(), $sentence);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $this->saveSentence($em, $sentence);
                return $this->redirect($this->generateUrl('sentence_list'));
            }
        }

        return $this->render('TicketBundle:Sentence:edit_sentence.html.twig', array('sentence'   => $sentence,
                                                                                    'form_name'  => $form->getName(),
                                                                                    'form'       => $form->createView()));
    }

    /**
     * Hace el save de un sentence
     * @param EntityManager $em
     * @param Sentence $sentence
     */
    private function saveSentence($em, $sentence){
        $em->persist($sentence);
        $em->flush();
    }

}
