<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class MovieFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $movie = new Movie();
        $movie->setTitle('qwe');
        $movie->setReleaseYear(2000);
        $movie->setDescription('asd');
        $movie->setImagePath('zxc');
        $movie->addActor($this->getReference('actor_1'));
        $movie->addActor($this->getReference('actor_2'));
        $manager->persist($movie);

        $movie2 = new Movie();
        $movie2->setTitle('rty');
        $movie2->setReleaseYear(2001);
        $movie2->setDescription('fgh');
        $movie2->setImagePath('vbn');
        $movie2->addActor($this->getReference('actor_2'));
        $movie2->addActor($this->getReference('actor_3'));
        $manager->persist($movie2);

        $manager->flush();
    }
}
