<?php

namespace App\Controller\wallets;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ListController extends AbstractController
{
    #[Route('/wallets/list', name: 'app_wallets_list')]
    public function index(): Response
    {
        return $this->render('wallets/list/index.html.twig', [
            'controller_name' => 'ListController',
        ]);
    }
}
