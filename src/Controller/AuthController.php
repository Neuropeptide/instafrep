<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authUtils): Response
    {
        if ($this->getUser()) {
            /** @var User $user */
            $user = $this->getUser();
            if ($user->isVerified()) {
                return $this->redirectToRoute('app_homepage');
            } else {
                $this->addFlash('warning', 'Your email has not been validated, please verify your account.');
                return $this->redirectToRoute('app_current_user_profile');
            }
        }

        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('pages/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
