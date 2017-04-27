<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Shop;
use AppBundle\Form\ShopType;

class ShopController extends Controller
{
    public function shopsAction($page=1)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->getRepository("AppBundle:Shop")
                    ->createQueryBuilder("s")
                    ->select("s")
                    ->where("s.id != 0")
                    ->orderBy("s.name", "ASC");

        $pagination = $this->get('knp_paginator')->paginate($query->getQuery(), $page, 10);

        return $this->render('w_shop/shops.html.twig', array('pagination' => $pagination));
    }

    /*
     * @ParamConverter("shop", class="AppBundle:Shop")
     */
    public function shopAction(Request $request, Shop $shop=null)
    {
        if($shop == null) $shop = new Shop();

        $em = $this->getDoctrine()->getManager();
        $tokenUser = $this->get('security.token_storage')->getToken()->getUser();
        $tokenService = $this->get('utils')->getTokenServices($tokenUser);


        $form = $this->createForm(new ShopType(), $shop, array('attr' => array('em' => $em, 'ids' => $tokenUser->getService(), 'tokenService' => $tokenService )));

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $em->persist($shop);
            $em->flush();

            // Show confirmation
            $this->get('session')->getFlashBag()->add('ok' , $this->get('translator')->trans('shop_update'));

            return $this->redirect($this->generateUrl('shops'));
        }

        return $this->render('w_shop/shop.html.twig', array('_locale' => $this->get('locale'), 'form' => $form->createView()));
    }

    /*
     * @ParamConverter("shop", class="AppBundle:Shop")
     */
    public function shopDeleteAction(Shop $shop)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($shop);
        $em->flush();

        // Show confirmation
        $this->get('session')->getFlashBag()->add('ok' , $this->get('translator')->trans('shop_deleted'));

        return $this->redirect($this->generateUrl('shops'));
    }
}