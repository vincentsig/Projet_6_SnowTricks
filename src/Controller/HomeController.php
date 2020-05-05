<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TrickRepository;

class HomeController extends AbstractController
{
    /**
     * @Route("/",
     *      name="home")
     * @return Response
     */
    public function index(TrickRepository $trickRepository): Response
    {
        return $this->render('pages/home.html.twig', [
            'tricks' => $trickRepository->findAll(),
        ]);
    }
}
