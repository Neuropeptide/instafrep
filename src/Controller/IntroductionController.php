<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class IntroductionController extends AbstractController
{
    /**
     * @Route("/intro", name="page_intro")
     */
    public function index()
    {

        $random = rand(0, 10);

        return $this->render('introduction/index.html.twig', [
            'name' => 'Pierre',
            'page_title' => "Super titre",
            'random_number' => $random
        ]);
    }

    /**
     * @Route("/lucky")
     */
    public function testMethod() {
        $random = rand(0, 10);
        return new Response($random);
    }

    /**
     * @Route("/gitlab")
     */
    public function goToGitLab() {
        return new RedirectResponse('https://gitlab.com');
    }

    /**
     * @Route("/miracle", name="app_miracle")
     */
    public function solutionMiracle() {
        throw $this->createNotFoundException("Désolé, mais ca n'existe pas !");
    }

    /**
     * @Route("/balrog")
     */
    public function gandalf() {
        throw new AccessDeniedHttpException("Vous ne passerez pas !");
    }
}
