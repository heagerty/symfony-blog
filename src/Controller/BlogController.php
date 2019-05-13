<?php


// src/Controller/BlogController.php
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog_index")
     */

    public function index()
    {
        return $this->render('blog/index.html.twig', [
            'owner' => 'Casey',
        ]);
    }

    /**
     * @Route("/blog/list/{page}", requirements={"page"="\d+"}, defaults={"page"=1}, name="blog_list")
     */

    public function list($page)
    {
        return $this->render('blog/list.html.twig', [
            'page' => $page,
        ]);
    }

    /**
     * @Route("/blog/show/{slug}", requirements={"slug"="[a-zA-Z0-9-_]+"}, defaults={"slug"="article-sans-titre"}, name="blog_show")
     */

    public function show($slug)
    {

        if (preg_match ( '/[A-Z]/', $slug) or preg_match ( '/_/', $slug)) {

            throw $this->createNotFoundException('The page does not exist');

            // the above is just a shortcut for:
            // throw new NotFoundHttpException('The product does not exist');
        }


        return $this->render('blog/show.html.twig', [
            'slug' => $slug,
        ]);
    }
}