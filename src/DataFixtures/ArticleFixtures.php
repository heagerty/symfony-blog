<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;
use App\Service\Slugify;
use App\Repository\TagRepository;


class ArticleFixtures extends Fixture implements DependentFixtureInterface
{


    public function getDependencies()
    {
        return [CategoryFixtures::class];
    }

    public function load(ObjectManager $manager)
    {
        for ($i=0; $i<50; $i++) {
            $faker = Faker\Factory::create('en_US');
            $article = new Article();
            $article->setTitle(mb_strtolower($faker->sentence($nbWords = 6, $variableNbWords = true)));
            $article->setContent(mb_strtolower($faker->text));

            $tag = new Tag();
            $article->addTag($tag->findOneByName($faker->randomElement(['PHP', 'Ruby', 'Java'])));
            $tag->addArticle($article);

            $category = new Category();
            $article->setCategory($category->findOneByName($faker->randomElement(CategoryFixtures::CATEGORIES)));

            $slugify = new Slugify();
            $slug = $slugify->generate($article->getTitle());
            $article->setSlug($slug);



            $manager->persist($article);
            $manager->persist($category);

            $manager->flush();

        }

    }
}