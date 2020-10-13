<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Task;
use App\Entity\Category;
use App\Entity\SubTask;

class TaskFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');

        // Create 3 fake categories
        for($i = 0; $i <= 3; $i++){
            $category = new Category();
            $category->setTitle($faker->sentence())
                    ->setDescription($faker->paragraph());

            $manager->persist($category);

            
            // Create 3 to 6 fake tasks
            for($j = 1; $j <= mt_rand(3, 6); $j++){
                $task = new Task();
                $task->setTitle($faker->sentence(3))
                    ->setDescription(join($faker->paragraphs(), ' '))
                    ->setDone(false)
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                    ->setCategory($category);
    
                $manager->persist($task);

                
                // Create 3 to 9 fake subtask
                for($k=1; $k <= mt_rand(3, 9); $k++) {
                    $subTask = new SubTask();
                    
                    $dateLimiter = (new \DateTime())->diff($task->getCreatedAt())->days;

                    $subTask->setTitle($faker->sentence(3))
                        ->setDescription(join($faker->paragraphs(), ' '))
                        ->setCreatedAt(
                            $faker->dateTimeBetween(
                                '-' .$dateLimiter . ' days'
                            )
                        )
                        ->setTask($task);

                    $manager->persist($subTask);
                }
            }
        }

        $manager->flush();
    }
}
