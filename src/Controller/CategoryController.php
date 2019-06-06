<?php


// src/Controller/BlogController.php
namespace App\Controller;


use App\Entity\Article;
use App\Entity\Category;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use App\Form\ArticleSearchType;
use App\Form\CategoryType;
use Symfony\Component\HttpFoundation\Request;


use App\Repository\CategoryRepository;





class CategoryController extends AbstractController
{

    /**
     * Show all rows from article entity
     *
     * @param Request $request Request
     * @Route("/category/", name="category_index")
     * @return Response A response instance
     */
    public function index(Request $request): Response
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        if (!$categories) {
            throw $this->createNotFoundException(
                'No article found in article\'s table.'
            );
        }

        $form = $this->createForm(CategoryType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            // $data contient les données du $_POST
            // Faire une recherche dans la BDD avec les infos de $data...
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($data);
            $entityManager->flush();
        }





        //create search field
//        $form = $this->createForm(
//            ArticleSearchType::class,
//            null,
//            ['method' => Request::METHOD_GET]
//        );


        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);



        //list of categories
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        if (!$categories) {
            throw $this->createNotFoundException(
                'No categories found.'
            );
        }







        return $this->render(
            'category/index.html.twig',
            ['form' => $form->createView(),
             'categories' => $categories]
        );
    }



}