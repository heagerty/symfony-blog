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





class BlogController extends AbstractController
{

    /**
     * Show all rows from article entity
     *
     * @Route("/", name="index")
     * @return Response A response instance
     */
    public function index(): Response
    {
        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findAll();

        if (!$articles) {
            throw $this->createNotFoundException(
                'No article found in article\'s table.'
            );
        }


        //create search field
//        $form = $this->createForm(
//            ArticleSearchType::class,
//            null,
//            ['method' => Request::METHOD_GET]
//        );


        $category = new Category();
        $form = $this->createForm(CategoryTypeOld::class, $category);



        return $this->render(
            'blog/index.html.twig',
            ['articles' => $articles,
            'form' => $form->createView(),
            ]
        );
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
     * Getting a article with a formatted slug for title
     *
     * @param string $slug The slugger
     *
     * @Route("/blog/{slug<^[a-z0-9-]+$>}",
     *     defaults={"slug" = null},
     *     name="blog_show")
     *  @return Response A response instance
     */
    public function show(?string $slug) : Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find an article in article\'s table.');
        }

        $slug = preg_replace(
            '/-/',
            ' ', ucwords(trim(strip_tags($slug)), "-")
        );

        $article = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);

        if (!$article) {
            throw $this->createNotFoundException(
                'No article with '.$slug.' title, found in article\'s table.'
            );
        }

        return $this->render(
            'blog/show.html.twig',
            [
                'article' => $article,
                'slug' => $slug,
            ]
        );
    }


    /**
     * Show a selection based on category name
     *
     * @param Category $category Category
     *
     * @Route("blog/category/{name}", name="show_category")
     *
     * @return Response A response instance
     *
     * @ParamConverter("category", class="App\Entity\Category")
     *
     */
    public function showByCategory(Category $category) : Response
    {


/*        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => mb_strtolower($category)]);

        $articles = $this->getDoctrine()
            ->getRepository(Article::class)
            ->findBy(['category' => ($category)], array('id' => 'DESC'), 3);

        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => mb_strtolower($category)]);       */


        $articles = $category->getArticles();




        return $this->render('blog/category.html.twig', [
            'category' => $category,
            'articles' => $articles,
        ]);
    }
}