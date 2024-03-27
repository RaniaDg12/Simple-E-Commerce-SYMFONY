<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Form\ProductType;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use App\Form\ContactType;

use App\Entity\Comment;
use App\Form\CommentType;



class SiteController extends AbstractController
{
    
    /**
     * @Route("/site", name="site")
     */
    public function index(ProductRepository $repo): Response
    {
        $products= $repo->findAll();

        return $this->render('site/index.html.twig', [
            'controller_name' => 'SiteController',
            'products' => $products
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        return $this->render('site/home.html.twig');
    }

    /**
     * @Route("/site/contact", name="site_contact") *-
     */
    public function contact(Contact $contact = null, Request $request, ObjectManager $manager)
    {
        if(!$contact){
            $contact =new Contact();
        }
        
        $form= $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if(!$contact->getId()){
                $contact->setSentAt(new \DateTimeImmutable());
            }

            $manager->persist($contact);
            $manager->flush();

            $this->addFlash(
                'notice',
                'Your message is sent!'
            );
            return $this->redirectToRoute ('site_contact');

        } 

        return $this->render('site/contact.html.twig', [
            'formContact' =>$form->createView()
        ]);
    }


    /**
     * @Route("/site/new", name="site_create")
     * @Route("/site/{id}/edit", name="site_edit")
     */
    public function form(Product $product = null, Request $request, ObjectManager $manager)
    {
        if(!$product){
            $product =new Product();
        }
        
        $form= $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if(!$product->getId()){
                $product->setCreatedAt(new \DateTimeImmutable());
            }

            $manager->persist($product);
            $manager->flush();

            return $this->redirectToRoute ('site_show' , ['id'=> $product->getId()]);

        } 
        return $this->render('site/create.html.twig', [
            'formProduct' =>$form->createView(),
            'editMode'=> $product->getId() !== null

        ]);
    }

    /**
     * @Route("/site/{id}", name="site_show") *-
     */
    public function show(Product $product, Request $request, ObjectManager $manager)
    {
        $comment= new Comment();
        $form= $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $comment->setCreatedAt(new \DateTimeImmutable())
                    ->setProduct($product);

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute ('site_show', [ 'id' => $product->getId()]);

        }

        return $this->render('site/show.html.twig', [
            'product' => $product,
            'commentForm'=> $form->createView()
        ]);
    }
    


}
