<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Tag;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Slugify;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/article")
 */
class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="article_index", methods={"GET"})
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $articleRepository->findAllWithCategoriesAndTags(),

        ]);
    }

    /**
     * @Route("/new", name="article_new", methods={"GET","POST"})
     */
    public function new(Request $request, Slugify $slugify, \Swift_Mailer $mailer): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        $author = $this->getUser();
        $article->setAuthor($author);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $title = $article->getTitle();


            $slug = $slugify->generate($article->getTitle());
            $article->setSlug($slug);

            $entityManager->persist($article);
            $entityManager->flush();

            $id = $article->getId();

            $message = (new \Swift_Message('Un nouvel article vient d\'être publié !'))
                ->setFrom('cheagerty@gmail.com')
                //->setTo('cheagerty@gmail.com')   -- swiftmailer.yaml
                ->setBody(
                    $this->renderView(
                    // templates/emails/registration.html.twig
                        'article/email.html.twig',
                        ['slug' => $slug,
                         'title' => $title,
                            'id' => $id]
                    ),
                    'text/html'
                )

            ;
            $mailer->send($message);

            return $this->redirectToRoute('article_index');
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="article_show", methods={"GET"})
     */
    public function show(Article $article, Slugify $slugify): Response
    {


        $slug = $slugify->generate($article->getTitle());
        $article->setSlug($slug);


        return $this->render('article/show.html.twig', [
            'article' => $article,
            'slug' => $slug,
            'author' => $article->getAuthor(),
            'tags' => $article->getTags()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="article_edit", methods={"GET","POST"})
     *
     */
    public function edit(Request $request, Article $article, Slugify $slugify): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('article_index', [
                'id' => $article->getId(),
            ]);
        }


        $slug = $slugify->generate($article->getTitle());
        $article->setSlug($slug);

        $user = $this->getUser();
        $userRoles = $user->getRoles();
        $author = $article->getAuthor();

        if ($user == $author or in_array('ROLE_ADMIN', $userRoles)) {
            return $this->render('article/edit.html.twig', [
                'article' => $article,
                'form' => $form->createView(),
                'slug' => $slug,
            ]);
        } else {
            $isNotAuthor = true;

            return $this->render('article/show.html.twig', [
                'article' => $article,
                'slug' => $slug,
                'author' => $article->getAuthor(),
                'isNotAuthor' => $isNotAuthor
            ]);
        }
    }

    /**
     * @Route("/{id}", name="article_delete", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Article $article): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('article_index');
    }
}
