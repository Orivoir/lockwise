<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Account;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $em)
    {

        $faker = Factory::create('fr_FR') ;

        for ($i=0,$size= \mt_rand( 13 , 30 ) ; $i < $size ; $i++) { 
            
            $account = new Account( Account::FACTORY );

            $account
                ->setPlateform( $faker->sentence( \mt_rand( 1 , 3 ) ) )
                ->setPassword( $faker->sentence( \mt_rand( 4 , 15 ) ) )
            ;

            if( \mt_rand( 0 , 1 ) ) {
                $account->setLogin( $faker->userName ) ;
            }
            if( \mt_rand( 0 , 9 ) >= 7 ) { // 30% is remove
                $account->setIsRemove( true ) ;
            }
            if( \mt_rand( 0 , 9 ) >= 6 ) { // 40% is favorite

                $account->setIsFavorite( true ) ;
            }

            $em->persist($account);
        }


        $em->flush();
    }
}
