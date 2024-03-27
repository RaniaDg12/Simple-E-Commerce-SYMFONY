<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Comment;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('en_US');

        for($i=1; $i<=3; $i++){
            $category= new Category();

            $terms = ['tech', 'cosmetic', 'accessories'];
            $category->setTitle($faker->randomElement($terms));

            $manager->persist($category);

            for($j=1; $j<=4; $j++){
                $product = new Product();
                
                $description = '<p>' . implode('</p><p>', $faker->paragraphs(3)) . '</p>';

                $product->setTitle($faker->sentence())
                        ->setDescription($description)
                        ->setPrice($price)
                        ->setImage($faker->imageUrl())
                        ->setCreatedAt(new \DateTimeImmutable())
                        ->setCategory($category);
            
                $manager->persist($product);
                
                for($k=1; $k<=2; $k++){
                    $comment= new Comment();

                    $content= '<p>' . implode('</p><p>', $faker->paragraphs(2)) . '</p>';
                
                    $comment->setAuthor($faker->name)
                            ->setContent($content)
                            ->setCreatedAt(new \DateTimeImmutable())
                            ->setProduct($product);
                    
                    $manager->persist($comment);
                }
            }

        }
        
        $manager->flush();
    }
}
