<?php

namespace App\Controller;

use App\Entity\Account;
use App\Repository\CodeRecupRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/code-recup" , name="code_recup." )
 */
class CodeRecupController extends AbstractController
{

    /**
     * @Route("/remove/{slug}/{id}"  , name="codeRecup" , methods={"GET"} )
     */
    public function removeCodeRecup(
        string $slug ,
        Account $account,
        Request $rq ,
        CodeRecupRepository $codeRecupRepo
    ) {

        $backNotFound = [
            "success" => false
            ,"code" => 404
        ] ;

        if( $account->getIsRemove() ) {

            $this->addFlash('error','account not found') ;
            return $this->json( $backNotFound ) ;
        }

        $idCode = $rq->get('code_recup') ;

        $code2remove = $codeRecupRepo->find( (int) $idCode ) ;

        if( $code2remove ) {

            $em = $this->getDoctrine()->getManager() ;

            $backNotFound['codeRecup'] = $code2remove->getContent() ;
            $code2remove->setisRemove( true ) ;

            $em->persist( $code2remove ) ;
            $em->flush() ;

            return $this->json([
                'codeRecup' => $code2remove->getContent() ,
                'codeId' => $code2remove->getId() , 
                'success' => true ,
                'account' => $account->getId()
            ]) ;
    
        } else {
            // 404
            $this->addFlash('error','code not found') ;
            return $this->json( $backNotFound ) ;
        }

    }
}
