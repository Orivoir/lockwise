<?php

namespace App\Controller;

use App\Entity\Account;
use App\Entity\CodeRecup;
use App\Form\CodeRecupType;
use App\Form\AccountFormType;
use App\Repository\AccountRepository;
use App\Repository\CodeRecupRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/account" , name="account." )
 */
class AccountController extends AbstractController
{

    private $token = NULL;

    public function __construct() {

        if( !$this->token ) {
            $this->token = \md5("token") ;
        }
    }

    /**
     * @Route("/", name="index")
     */
    public function index(AccountRepository $accountRepo)
    {

        $accounts = $accountRepo->findAllVisible() ;

        return $this->render(
            'account/index.html.twig' ,
            [
                'accounts' => $accounts,
                'route' => '/account',
            ]
        );
    }

    /**
     * @Route("/toggle-favorite/{slug}/{id<\d{1,9}>}" , name="toggle_favorite" )
     */
    public function toggleFavorite(
        string $slug , Account $account
    ) {

        if( $account->getIsRemove() ) {    
            return $this->json( [
                "success" => false
                ,"code" => 404
            ] ) ;
        }

        $em = $this->getDoctrine()->getManager() ;

        $account->setIsFavorite( 
            !$account->getIsFavorite()
        ) ;

        $em->flush() ;

        return $this->json( [
            "success" => true
            ,"status" => $account->getIsFavorite()
            ,"id" => $account->getId()
            ,"code" => 200
        ] ) ;
    }

    /**
     * @Route("/details/{slug}/{id<\d{1,9}>}", name="details")
     */
    public function details( string $slug , Account $account )
    {

        $realSlug = $account->getSlug() ;

        if( $slug != $realSlug ) {
            // redirect to the canonique link
            return $this->redirectToRoute('account.details' , [
                'id' => $account->getId()
                ,'slug' => $realSlug
            ] , 301 ) ;
        }

        return $this->render(
            'account/details.html.twig'
            , [
                'account' => $account
                , "route" => "details"
            ]
        );
    }

    /**
     * @Route("/create", name="create")
     */
    public function create( Request $rq )
    {

        $account = new Account() ;

        $form = $this->createForm( AccountFormType::class , $account ) ;

        $form->handleRequest( $rq ) ;

        if( $form->isSubmitted() && $form->isValid() ) {

            $em = $this->getDoctrine()->getManager() ;

            $this->addFlash('success' , 'account create with success .' ) ;

            $em->persist( $account ) ;

            $em->flush() ;

            return $this->redirectToRoute('account.index');
        }

        return $this->render('account/create.html.twig' , [
            'form' => $form->createView()
        ] ) ;
    }

    /**
     * @Route("/update/{slug}/{id<\d{1,9}>}", name="update")
     */
    public function update(
        string $slug
        ,Account $account 
        ,Request $rq
    ) {
        $realSlug = $account->getSlug() ;

        if( $account->getIsRemove() ) {

            return $this->redirectToRoute('account.index' , [] , 301 ) ;
        }

        if( $slug != $realSlug ) {
            // redirect to the canonique link
            return $this->redirectToRoute('account.update' , [
                'id' => $account->getId()
                ,'slug' => $realSlug
            ] , 301 ) ;
        }

        $form = $this->createForm( AccountFormType::class , $account ) ;

        $form
            ->add('login',TextType::class )
            ->add('password',TextType::class )
            ->add('codeRecups' , CollectionType::class , [
                'entry_type' => CodeRecupType::class
                ,'allow_add' => true
                ,'allow_delete' => true
            ] )
        ;

        $form->handleRequest( $rq ) ;

        if( $form->isSubmitted() && $form->isValid() ) {

            $em = $this->getDoctrine()->getManager() ;

            $codeRecups = $form->getData()->getCodeRecups()->toArray() ;

            foreach( $codeRecups as  $codeRecup ) {

                $codeRecup->setAccount( $account ) ;
            }

            $account->setUpdateAt( new \DateTime() );

            $this->addFlash('success' , 'account create with success .' ) ;

            $em->persist( $account ) ;
            $em->flush() ;

            return $this->redirectToRoute('account.index');
        }

        return $this->render(
            'account/update.html.twig'
            ,[
                'form' => $form->createView()
                ,'account' => $account
                ,'route' => 'details'
            ]
        ) ;
    }

    /**
     * @Route("/remove/{slug}/{id<\d{1,9}>}", name="remove")
     */
    public function remove( 
        string $slug , 
        Account $account ,
        Request $rq
    ) {

        $tokenParam = $rq->get('token') ;

        if( $account->getIsRemove() ) {

            $this->addFlash("error" , "account not found" ) ;

            return $this->redirectToRoute(
                'account.index' ,
                [] ,
                301
            ) ;
        }

        $realSlug = $account->getSlug() ;

        if( $slug != $realSlug ) {
            // redirect to the canonique link
            return $this->redirectToRoute('account.remove' , [
                'id' => $account->getId()
                ,'slug' => $realSlug
            ] , 301 ) ;
        }

        if( $tokenParam === $this->token ) {
            // remove accept
            $em = $this->getDoctrine()->getManager() ;

            $account->setIsRemove( true );

            $em->persist( $account ) ;
            $em->flush();

            $this->addFlash('success' , 'account remove with success' ) ;

            return $this->redirectToRoute('account.index' , [] , 301 );

        } else if( $tokenParam != NULL ) {
            // token error
            dump('token error');
        }

        return $this->render(
            'account/remove.html.twig' ,
            [
                'real slug' => $realSlug,
                'account' => $account,
                "token" => $this->token
            ]
        );
    }

}
