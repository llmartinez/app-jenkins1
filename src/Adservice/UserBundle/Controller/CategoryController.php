<?php
namespace Adservice\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;

use Adservice\UtilBundle\Entity\Pagination;
use Adservice\UserBundle\Entity\CategoryService;
use Adservice\UserBundle\Form\CategoryServiceType;

class CategoryController extends Controller
{

    /**
     * Devuelve la lista de categorias de servicio
     */
    public function listCategoriesAction(Request $request, $page=1) {
        $em = $this->getDoctrine()->getManager();

        if (! $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
             throw new AccessDeniedException();
        }
        $params[] = array();

        $pagination = new Pagination($page);
        $categories = $pagination->getRows($em, 'UserBundle', 'CategoryService', $params, $pagination);

        $length = $pagination->getRowsLength($em, 'UserBundle', 'CategoryService', $params);
        
        $pagination->setTotalPagByLength($length);

        return $this->render('UserBundle:CategoryService:list_category_service.html.twig', array( 'categories'  => $categories,
                                                                                     'pagination' => $pagination,
                                                                                     'country'    => 0));
    }

    /**
     * Crear una nueva categoria de servicio
     */
    public function newCategoryServiceAction(Request $request) {

        if (! $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')){
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $category = new CategoryService();

        // Creamos variables de sesion para fitlrar los resultados del formulario
        $form = $this->createForm(new CategoryServiceType(), $category);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $category->setSlug(str_replace(" ", "-", strtolower($category->getSlug())));
                
                $em->persist($category);
                $em->flush();
                return $this->redirect($this->generateUrl('category_service_list'));
            }
        }

        return $this->render('UserBundle:CategoryService:new_category_service.html.twig', array('category'   => $category,
                                                                                    'form_name'  => $form->getName(),
                                                                                    'form'       => $form->createView()));
    }

    /**
     * Crear una nueva categoria de servicio
     */
    public function editCategoryServiceAction(Request $request, $id) {

        if (! $this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')){
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository("UserBundle:CategoryService")->find($id);
        if (!$category) throw $this->createNotFoundException('Categoría de servicio no encontrado en la BBDD');

        // Creamos variables de sesion para fitlrar los resultados del formulario
        $form = $this->createForm(new CategoryServiceType(), $category);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $category->setSlug(str_replace(" ", "-", strtolower($category->getSlug())));
                
                $em->persist($category);
                $em->flush();
                return $this->redirect($this->generateUrl('category_service_list'));
            }
        }

        return $this->render('UserBundle:CategoryService:edit_category_service.html.twig', array('category'   => $category,
                                                                                    'form_name'  => $form->getName(),
                                                                                    'form'       => $form->createView()));
    }
    /**
     * Hace el save de un sentence
     * @param EntityManager $em
     * @param Sentence $sentence
     */
//    private function saveSentence($em, $sentence){
//        $em->persist($sentence);
//        $em->flush();
//    }

}
