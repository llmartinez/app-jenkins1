<?php
namespace Adservice\TicketBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
    public function listSentenceAction($page=1, $country='none') {
        $em = $this->getDoctrine()->getEntityManager();
        $security = $this->get('security.context');

        if (! $security->isGranted('ROLE_ADMIN')) {
             throw new AccessDeniedException();
        }

        if($security->isGranted('ROLE_SUPER_ADMIN')) {
            if ($country != 'none') $params[] = array('country', ' = '.$country);
            else                    $params[] = array();
        }
        else $params[] = array('country', ' = '.$security->getToken()->getUser()->getCountry()->getId());

        $pagination = new Pagination($page);

        $sentences = $pagination->getRows($em, 'TicketBundle', 'Sentence', $params, $pagination);

        $length = $pagination->getRowsLength($em, 'TicketBundle', 'Sentence', $params);

        $pagination->setTotalPagByLength($length);

        if($security->isGranted('ROLE_SUPER_ADMIN')) $countries = $em->getRepository('UtilBundle:Country')->findAll();
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
    public function editSentenceAction($id) {
        $security = $this->get('security.context');
        if (! $security->isGranted('ROLE_ADMIN')){
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $sentence = new Sentence();

        $petition = $this->getRequest();
        if ($id != null) {
                          $sentence = $em->getRepository("TicketBundle:Sentence")->find($id);
                          if (!$sentence) throw $this->createNotFoundException('Sentencia no encontrado en la BBDD');
        }
        // Creamos variables de sesion para fitlrar los resultados del formulario
        if ($security->isGranted('ROLE_SUPER_ADMIN')) {

            $_SESSION['id_partner'] = ' != 0 ';

        }elseif ($security->isGranted('ROLE_SUPER_AD')) {
            $_SESSION['id_country'] = ' = '.$security->getToken()->getUser()->getCountry()->getId();

        }else {
            $_SESSION['id_country'] = ' = '.$partner->getCountry()->getId();
        }
        $form = $this->createForm(new SentenceType(), $sentence);

        if ($petition->getMethod() == 'POST') {
            $form->bindRequest($petition);

            //La segunda comparacion ($form->getErrors()...) se hizo porque el request que reciber $form puede ser demasiado largo y hace que la funcion isValid() devuelva false
            $form_errors = $form->getErrors();
                if(isset($form_errors[0])) {
                    $form_errors = $form_errors[0];
                    $form_errors = $form_errors->getMessageTemplate();
                }else{
                    $form_errors = 'none';
                }
            if ($form->isValid() or $form_errors == 'The uploaded file was too large. Please try to upload a smaller file') {

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
