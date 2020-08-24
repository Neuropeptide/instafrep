<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\PasswordResetType;
use App\Form\UploadProfilePictureType;
use App\Services\PictureGenerator\RandomPicGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @method current_password()
 */
class UserController extends AbstractController
{

    private $pictureGenerator;

    /**
     * UserController constructor.
     * @param $pictureGenerator
     */
    public function __construct(RandomPicGenerator $pictureGenerator)
    {
        $this->pictureGenerator = $pictureGenerator;
    }



    /**
     * @Route("/users", name="app_all_users_list")
     */
    public function all() {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render("pages/all_users.html.twig", [
            "users" => $users
        ]);
    }


    /**
     * @Route("/profile/{id<^[0-9]+$>}", name="app_user_profile")
     * @param $id
     * @return RedirectResponse|Response
     */
    public function profile($id) {

        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if ($currentUser->getId() == $id) {
            return $this->redirectToRoute('app_current_user_profile');
        }


        // On va chercher l'utilisateur correspondant en BDD
        $user = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

        if (empty($user)) {
            throw $this->createNotFoundException(
                "L'utilisateur n'existe pas. (id : $id)");
        }

        return $this->render('pages/profile.html.twig', [
            'currentUser' => $currentUser,
            'user' => $user,
            'totalLiked' => $this->getDoctrine()->getRepository(Post::class)->countLikedPostBy($user)
        ]);
    }

    /**
     * @Route("/profile/{slug}", name="app_user_profile_by_slug")
     * @param $slug
     * @return RedirectResponse|Response
     */
    public function profileBySlug($slug) {

        // On redirige l'utilisateur actuel vers la route /me
        /** @var User $currentUser */
        $currentUser = $this->getUser();
        if ($currentUser->getSlug() == $slug) {
            return $this->redirectToRoute('app_current_user_profile');
        }

        // On va chercher l'utilisateur correspondant en BDD

        /** @var User $user */
        $user = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy([
                'slug' => $slug
            ]);

        // Si aucun utilisateur ne correspond à ce qu'on a demandé (ici le slug)
        // On fait une erreur 404
        if (empty($user)) {
            throw $this->createNotFoundException(
                "L'utilisateur n'existe pas. (slug : $slug)");
        }

        // Si l'utilisateur existe, on affiche son profil
        return $this->render('pages/profile.html.twig', [
            'currentUser' => $currentUser,
            'user' => $user,
            'totalLiked' => $this->getDoctrine()->getRepository(Post::class)->countLikedPostBy($user)
        ]);
    }

    /**
     * @Route("/me", name="app_current_user_profile")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function currentUserProfile(Request $request, SluggerInterface $slugger)
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();


        $form = $this->createForm(UploadProfilePictureType::class, $currentUser);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            if ($form->isValid()){
                $profilPicture = $form->get('uploadImage')->getData();


                /** @var UploadedFile  $profilPicture */
                if ($profilPicture) {
                    $originalFilename = pathinfo($profilPicture->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$profilPicture->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {

                        $folder = $this->getParameter('uploadImage_directory');
                        $profilPicture->move(
                            $folder,
                            $newFilename
                        );
                        if (!empty($currentUser->getUploadImage())) { # TODO
                            $fileSystem = new Filesystem();
                            $fileSystem->remove([$folder . '/' . $currentUser->getUploadImage()]);

                        }

                    } catch (FileException $e) {
                        // TODO  handle exception if something happens during file upload
                        dd($e);
                    }

                    $currentUser->setUploadImage($newFilename);
                    // On enregistre dans la BDD
                    $this->getDoctrine()->getManager()->flush();
                }

            }
        }

        return $this->render('pages/profile.html.twig', [
            'currentUser' => $currentUser,
            'user' => $currentUser,
            'totalLiked' => $this->getDoctrine()->getRepository(Post::class)->countLikedPostBy($currentUser),
            'form' => $form->createView(),
            ]);
    }



    /**
     * @Route("/deletepicprofile", name="app_delete_profile_picture")
     * @param $currentUser
     * @param $folder
     */

    public function deletePictureProfile() {
        /** @var User $currentUser */

        // On recupere l'utilisateur actuel
        $currentUser = $this->getUser();
        // On defini le dossier de sauvegarde de l'image
        $folder = $this->getParameter('uploadImage_directory');


        if (!empty($currentUser->getUploadImage())) { // Si l'utilisateur a deja une image de profil
            // Necessaire pour acceder au dossier de l'image
            $fileSystem = new Filesystem();
            //On supprime son image
            $fileSystem->remove([$folder . '/' . $currentUser->getUploadImage()]);
            // On defini a "null" le fait que l'utilisateeur ai une image
            $currentUser->setUploadImage(null);
            // On donne un nom d'image unique à la nouvelle image
            $fileName = uniqid().".png";
            // On genere une nouvelle image et on l'enregistre dans le dossier de sauvegarde
            $this->pictureGenerator->generateRandomPic()
                ->makeMiniature(30, 30)
                ->savePic("$folder/".$fileName);
            //On associe l'image crée à l'utilisateur
            $currentUser->setUploadImage($fileName);
            }

        // On envoi le nom de unique de l'image en BDD
        $this->getDoctrine()->getManager()->flush();
        // On redirige vers la page de profil
        return $this->redirectToRoute('app_current_user_profile');
        // lets go baby !!!
    }



    /**
     * Follow or unfollow
     * @Route("/{id<^\d+$>}/follow", name="app_user_follow")
     * @param $id
     */


    public function follow ($id){

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        /** @var User $user */
        // On va chercher l'utilisateur correspondant en BDD
        $user = $this
            ->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

        // validation (post existant, ...)
        if (empty($user)) {
            throw $this->createNotFoundException("Le user n'existe pas :(");
        }

        // mettre à jour la relation entre les deux entités
        if ($currentUser->doesFollow($user)){

            $currentUser->removeFollowing($user);

        } else {
            $currentUser->addFollowing($user);
        }

        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('app_user_profile', [
            'id' => $id
        ]);
    }

    /**
     * @Route("/me/change_password", name="app_modify_password")
     * @param $httpRequest
     * @return Response
     */
    public function modifyPassword(Request $httpRequest, UserPasswordEncoderInterface $passwordEncoder)
    {

        $form = $this->createForm(PasswordResetType::class);
        $form->handleRequest($httpRequest);

        /** @var User $user */
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            // TODO: Verification mot de passe (ancien) = à celui de la BDD
            // TODO: Verification nouveau mot de passe != ancien
            // TODO: Verification nouveau mot de passe == confirmation
            $user->setPassword($passwordEncoder->encodePassword(
                $user,
                $form->get('password')->get('new_password')->getData()
            ));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre password a bien été enregistré :)'
            );

            return $this->redirectToRoute('app_modify_password');
        }


        return $this->render('registration/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
