<?php


namespace App\Controller;

use App\Entity\Notification;
use App\Entity\Post;
use App\Entity\PostNotification;
use App\Entity\User;
use App\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class PageController extends AbstractController
{
    /**
     * Display the website home page
     *
     * @Route("/", name="app_homepage")
     * @param $authUtils
     * @return Response
     *
     */
    public function home(AuthenticationUtils $authUtils) {
        $postRepository = $this->getDoctrine()->getRepository(Post::class);
        // Aller chercher le post le plus populaire
        $mostLikedPost = $postRepository->findMostLiked();
        // Aller chercher les 5 posts "racine" les plus récents
        $recentPosts = $postRepository->findBy(['parent' => null], ['published_at' => 'DESC'], 5);

        // On crée un formulaire pour ajouter des Posts directement depuis la homepage
        $form = $this->createForm(PostType::class);

        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        // L'utilisateur connecté
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($currentUser) {
            if (!$currentUser->isVerified()) {
                $this->addFlash('warning', 'Your email has not been validated, please verify your account.');
                return $this->redirectToRoute('app_current_user_profile');
            }
        }

        $notifsRepository = $this->getDoctrine()->getRepository(Notification::class);


        // On stock toutes les notifs du user connecté
        /** @var User $currentUserId */
        $userNotifs = $notifsRepository->findBy([
            'author' => $currentUser
        ], ['created_at' => 'DESC']);



        // Les injecter pour les afficher
        return $this->render('pages/home.html.twig', [
            'posts' => $recentPosts,
            'mostLikedPost' => $mostLikedPost,
            'postForm' => $form->createView(),
            'error' => $error,
            'last_username' => $lastUsername,
            'notifs' => $userNotifs
        ]);
    }

    /**
     * @Route("/notifs", name="app_notifs_list")
     */
    public function notifications(){
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $notifsRepository = $this->getDoctrine()->getRepository(Notification::class);

        // Récupérer les notifs de commentaires non lues par le current User
        $userPostNotifs = $notifsRepository->findBy([
            'receiver' => $currentUser,
            'isRead'=> 0,
        ], ['created_at' => 'DESC']);




        return $this->render('pages/notifications.html.twig',[
            'notifs' => $userPostNotifs,
        ]);
    }

    /**
     * @Route("/close-notification/{id<^\d+$>}", name="app_notifs_close")
     * @param $id
     * @param Request $httpRequest
     * @return RedirectResponse
     */
    public function closeNotifications($id, Request $httpRequest){

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        $notifsRepository = $this->getDoctrine()->getRepository(Notification::class);

        /** @var Notification $userNotif */
        $userNotif = $notifsRepository->find($id);

        if ($currentUser === $userNotif->getReceiver()){

            $userNotif->setIsRead(1);

            $this->getDoctrine()->getManager()->flush();
        } else{
            throw $this->createAccessDeniedException("Cette notif ne vous appartient pas");
        }


        return $this->redirectToRoute('app_notifs_list');
    }
}