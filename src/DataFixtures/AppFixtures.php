<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Account;
use App\Entity\CodeRecup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * build Account Factory
 * 
 * 50% have an login
 * 30% is remove
 * 40% is favorite
 * 33% have one or many CodeRecup
 */
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
            if( \mt_rand( 0 , 9 ) >= 7 ) {
                $account->setIsRemove( true ) ;
            }
            if( \mt_rand( 0 , 9 ) >= 6 ) {

                $account->setIsFavorite( true ) ;
            }

            if( \mt_rand( 0 , 2 ) == 2 ) {

                for( $i = 0 , $size = \mt_rand( 1 , 15 ) ; $i < $size; $i++ ) {

                    $codeRecup = new CodeRecup() ;

                    $codeRecup->setContent( $faker->sentence( 1 ) ) ;

                    $account->addCodeRecup( $codeRecup ) ;
                }
            }

            $em->persist($account);
        }

        $em->flush();
    }
}
