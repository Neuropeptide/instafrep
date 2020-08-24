<?php

namespace App\Controller;

use App\Entity\LikeNotification;
use App\Entity\Notification;
use App\Entity\Post;
use App\Entity\PostNotification;
use App\Entity\User;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Services\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /**
     * @Route("/posts/create", name="app_post_create")
     * @param Request $httpRequest
     * @return RedirectResponse|Response
     */
    public function createPost(Request $httpRequest)
    {
        $newPost = new Post();

        /** @var User $currentUser */
        $currentUser = $this->getUser();
        // On associe le nouveau post a son auteur
        $newPost->setAuthor($currentUser);
        // Ou bien $currentUser->addPost($newPost);

        $form = $this->createForm(PostType::class, $newPost);

        $form->handleRequest($httpRequest);

        // Si on a recu des données
        if ($form->isSubmitted()) {

            // Si les données sont valides
            if ($form->isValid()) {
                // on met à jour l'entité avec les données du formulaire
                $newPost = $form->getData();

                // on l'enregistre en BDD
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($newPost); // on informe Doctrine qu'il y a un nouveau Post dont il faudra s'occuper
                $entityManager->flush();

                // On crée une notification
                $this->addFlash(
                    'success',
                    'Votre post a bien été enregistré :)'
                );


                return $this->redirectToRoute('app_homepage');
            }
        }

        return $this->render('pages/new_post.html.twig', [
            'postForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/posts/{id<^\d+$>}", name="app_post_single")
     * @param $id
     * @param Request $httpRequest
     * @return RedirectResponse|Response
     */
    public function single($id, Request $httpRequest) {

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        // récupérer le Post demandé
        $post = $this
            ->getDoctrine()
            ->getRepository(Post::class)
            ->find($id);

        // validation (post existant, ...)
        if (empty($post)) {
            throw $this->createNotFoundException("Le post $id n'existe pas :(");
        }

        $author = $post->getAuthor();
        /** @var Post $post */
        if (($currentUser->getFollowing()->contains($author)) || $currentUser === $author ) {
            $comment = new Post();
            $comment->setAuthor($currentUser);
            $comment->setParent($post);

            $form = $this->createForm(PostType::class, $comment);
            $form->handleRequest($httpRequest);

            // Si on a recu des données
            if ($form->isSubmitted()) {

                // Si les données sont valides
                if ($form->isValid()) {
                    // on met à jour l'entité avec les données du formulaire
                    $comment = $form->getData();

                    // on l'enregistre en BDD
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($comment); // on informe Doctrine qu'il y a un nouveau Post dont il faudra s'occuper
                    $entityManager->flush();

                    // On crée une notification
                    $this->addFlash(
                        'success',
                        'Merci d\'avoir commenté !'
                    );

                    // Envoyer une notif
                    // SI JE LIKE LE POST
                    // if($newPost->isLikedBy($currentUser)){}
    
                    $receivers = $post->getLikers()->toArray();
                    if (!in_array($author, $receivers)) {
                        array_push($receivers, $author);
                    }
    
                    foreach ($receivers as $user){
    
                        $newNotif = new PostNotification();
                        $newNotif->setLinkedPost($post);
                        $newNotif->setAuthor($currentUser);
                        $newNotif->setCreatedAt(new \DateTime());
                        $newNotif->setContent($currentUser->getUsername() . " a commenté le post #" . $post->getId());
                        /** @var User $user */
                        $newNotif->setReceiver($user);
    
                        $entityManager->persist($newNotif);
                    }
    
                    $entityManager->flush();
                    
                    return $this->redirectToRoute('app_post_single', ['id' => $id]);
                }
            }

            return $this->render('pages/single_post.html.twig', [
                "post" => $post,
                "commentForm" => $form->createView()
            ]);

        } else {
            $this->addFlash(
                'danger',
                'Vous devez follow pour voir ses postes ! !'
            );
            return $this->redirectToRoute('app_user_profile', [
                'id' => $author->getId()
            ]);
        }
    }

    /**
     * Allows the current user to like/unlike the given Post
     *
     * @Route("/posts/{id<^\d+$>}/like", name="app_like_post")
     */
    public function likePost($id, Request $request) {

        // récupérer le User qui like
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        // récupérer le Post demandé
        /** @var Post $post */
        $post = $this
            ->getDoctrine()
            ->getRepository(Post::class)
            ->find($id);

        // validation (post existant, ...)
        if (empty($post)) {
            throw $this->createNotFoundException("Le post $id n'existe pas :(");
        }

        $author = $post->getAuthor();

        if ($currentUser->doesFollow($author)){

            // mettre à jour la relation entre les deux entités
            if ($currentUser->doesLike($post)) {
                $currentUser->unlike($post);
            } else {

                $currentUser->like($post);

                $newNotif = new LikeNotification();

                $newNotif->setLinkedPost($post);
                $newNotif->setAuthor($currentUser);
                $newNotif->setCreatedAt(new \DateTime());
                $newNotif->setContent(" a liké le post #" . $post->getId());
                /** @var User $user */
                $newNotif->setReceiver($post->getAuthor());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($newNotif);
            }




            // enregistrer en base de données
            $this->getDoctrine()->getManager()->flush();
        }

        // retourner une réponse
        if ($request->isXmlHttpRequest()) { // si c'est une requete AJAX
            return $this->render('post/post_card_footer.html.twig', ['post' => $post]);
        }

        $redirectRoute = $request->query->get('redirect');
        if (!empty($redirectRoute)) {
            return $this->redirectToRoute($redirectRoute);
        }

        return $this->redirectToRoute('app_homepage');

    }

    /**
     * Display the list of the posts
     *
     * @Route("/posts", name="app_posts_list")
     * @param Request $request
     * @return Response
     */
    public function list(Request $request) {
        // On obtient une instance du repository qui gère les Posts
        /** @var PostRepository $postRepository */
        $postRepository = $this->getDoctrine()->getRepository(Post::class);

        $page = $request->query->get('page');
        $limit = $request->query->get('limit');

        //if value of 'page' is not valid, default value is 1
        if(empty($page) || !is_numeric($page))
        {
            $page = Paginator::DEFAULT_PAGE;
        }

        if (empty($limit) || !is_numeric($limit)) {
            $limit = Paginator::DEFAULT_PER_PAGE;
        }

        // On va chercher tous les Posts de la BDD ...
        $posts = $postRepository->findPage($page, $limit);
        $totalPosts = $postRepository->countAllPost();
        $maxPage = $postRepository->pageNumber($limit,$totalPosts);


        // Default value of page is maxPage value if is higher
        if($page > $maxPage)
        {
            $page = $maxPage;
        }

        if ($request->isXmlHttpRequest()) { // if it's a Ajax request

            if(rand(0, 100) < 5) {
                throw new \Exception('bug'); // 5% de chance d'avoir un bug :P
            }

            $html = "";
            foreach ($posts as $post) {
                $html .= "<li>" . $this->renderView('post/post_card.html.twig', ['post' => $post]). "</li>";
            }
            return new Response($html);
        }

        // ... on les "injecte" dans la vue pour les afficher
        return $this->render('pages/all_posts.html.twig', [
            // key => value
            // twig => php
            'posts' => $posts,
            'max_page' => $maxPage
        ]);
    }

}
