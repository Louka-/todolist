<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Todo;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        # 1 - tableau de catégories

        $categories = ['professionnel', 'personnel', 'important'];
        # je tstocke tous les objets créés dans la boucle dans le tableau $tabObjCategory
        $tabObjCategory = [];

        foreach ($categories as $c) {
            $cat = new Category;
            $cat->setName($c);
            $manager->persist($cat);
            $tabObjCategory[] = $cat;
        }

        # 2 - créer autant d'objet de type Category qu'il y a dans le tableau



        # 3 - Créer une ou plusieurs Todo

        $todo = new Todo;
        $todo
            ->setTitle('Initialiser le projet')
            ->setContent('Un tas de trucs à dire la dessus')
            ->setDateFor(new \DateTime('Europe/Paris'))
            ->setCategory($tabObjCategory[0]);
        
        $manager->persist($todo);

        $manager->flush();
    }

}
