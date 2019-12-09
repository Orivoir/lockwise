<?php

namespace App\Controller;

use App\Repository\AccountRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main.index")
     */
    public function index( AccountRepository $accountRepo )
    {

        $accountFavorites = $accountRepo->findAllFavorite( AccountRepository::PUBLIC_ACCESS ) ;

        return $this->render(
            'main/index.html.twig'
            ,[
                'accounts' => $accountFavorites
                , 'route' => '/'
            ]
        ) ;
    }
}
