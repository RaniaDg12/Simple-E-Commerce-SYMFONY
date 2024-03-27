<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Form\ProductType;

class CartController extends AbstractController
{
    /**
     * @Route("/panier", name="app_panier")
     */
    public function index(SessionInterface $session, ProductRepository $prodRepo): Response
    {
        $panier= $session->get('panier', []);

        $panierWithData= [];

        foreach($panier as $id =>$quantity){
            $panierWithData[]=[
                'product'=> $prodRepo->find($id),
                'quantity'=>$quantity
            ];
        }

        $total=0;
        foreach($panierWithData as $item){
            $totalItem= $item['product']->getPrice()*$item['quantity'];
            $total+= $totalItem;
        }

        return $this->render('cart/index.html.twig', [
            'items'=> $panierWithData,
            'total'=>$total
        ]);
    }

    /**
     * @Route("/panier/add/{id}", name="panier_add")
     */
    public function add($id, SessionInterface $session)
    {
        $panier= $session->get('panier', []);

        if(!empty($panier[$id])) {
            $panier[$id]++;
        }else {
            $panier[$id]= 1;
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute("app_panier");

    }

    /**
     * @Route("/panier/remove/{id}", name="panier_remove")
     */
    public function remover($id, SessionInterface $session)
    {
        $panier= $session->get('panier', []);

        if(!empty($panier[$id])) {
            unset($panier[$id]);
        }

        $session->set('panier', $panier);
        return $this->redirectToRoute("app_panier");

    }
}
