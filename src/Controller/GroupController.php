<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Post;
use App\Entity\User;
use App\Form\GroupType;
use App\Form\PostType;
use App\Repository\GroupRepository;
use App\Repository\PostRepository;
use App\Services\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class GroupController extends AbstractController
{
    /**
     * create a group
     * @Route("/group/create", name="app_group_create")
     * @param Request $httpRequest
     * @return Response
     */
    public function createGroup(Request $httpRequest)
    {

        /** @var User $currentUser */

        $currentUser= $this->getUser();

        $newGroup = new Group();


        $newGroup
            ->setCreator($currentUser)
            ->addMember($currentUser)
            ->setCreatedAt(new \DateTime());

        $form= $this->createForm(GroupType::class, $newGroup);

        $form->handleRequest($httpRequest);


        // Si on a recu des données
        if ($form->isSubmitted()) {

            // Si les données sont valides
            if ($form->isValid()) {
                // on met à jour l'entité avec les données du formulaire
                $newGroup = $form->getData();

                // on l'enregistre en BDD
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($newGroup); // on informe Doctrine qu'il y a un nouveau Post dont il faudra s'occuper
                $entityManager->flush();


                return $this->redirectToRoute('app_group_single',[
                    'id' => $newGroup->getId()
                ]);
            }
        }

        return $this->render('group/group_create.html.twig', [
            'groupForm' => $form->createView()
        ]);

    }

    /**
     * @Route("group/{id<^\d+$>}", name="app_group_single")
     * @param $id
     * @param $httpRequest
     * @return Response
     */
    public function singleGroup($id, Request $httpRequest)

    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        // on selectionne le groupe dont on veux voir la single page
        /** @var Group $group */
        $group = $this->getDoctrine()->getRepository(Group::class)->find($id);
        if (empty($group)) {
            throw $this->createNotFoundException("Le groupe $id n'existe pas :(");
        }

        //on cherche les posts de ce group
        /** @var PostRepository $postRepository */
        $postRepository = $this->getDoctrine()->getRepository(Post::class);

        $postInGroup = $postRepository->findBy([
            'papaGroup' => $group
        ],[
            'created_at' => 'DESC'
        ]);

        if ($currentUser->isMemberOf($group)
            || $currentUser === $group->getCreator() ) {
            $newPostInGroup = new Post();
            $newPostInGroup->setAuthor($currentUser)->setPapaGroup($group);

            $form = $this->createForm(PostType::class, $newPostInGroup);
            $form->handleRequest($httpRequest);

                    if ($form->isSubmitted()) {

                        if ($form->isValid()) {
                            // on met à jour l'entité avec les données du formulaire
                            $newPostInGroup = $form->getData();

                            // on l'enregistre en BDD
                            $entityManager = $this->getDoctrine()->getManager();
                            $entityManager->persist($newPostInGroup); // on informe Doctrine qu'il y a un nouveau Post dont il faudra s'occuper
                            $entityManager->flush();

                            return $this->redirectToRoute('app_group_single', ['id' => $id]);

                        }
                    }

                    return $this->render('group/single_group.html.twig', [
                        "group" => $group,
                        'currentUser' => $currentUser,
                        "id" => $id,
                        'postInGroup' => $postInGroup,
                        "postGroupForm" => $form->createView()
                    ]);

                }
        return $this->render('group/single_group.html.twig', [
            "group" => $group,
            'currentUser' => $currentUser,
            "id" => $id,
            'postInGroup' => $postInGroup

        ]);
    }

    /**
     * Display the list of groups
     *
     * @Route("/group/list", name="app_group_list")
     * @param Request $request
     * @return Response
     */


    public function listGroup(Request $request) {
        // On obtient une instance du repository qui gère les groupes
        /** @var GroupRepository $groupRepository */
        $groupRepository = $this->getDoctrine()->getRepository(Group::class);

        $currentUser = $this->getUser();
        // $group = $groupRepository->findAll();
        $group = $groupRepository->findBy([],
        [
            'createdAt' => 'DESC'
        ]);

        // ... on les "injecte" dans la vue pour les afficher
        return $this->render('group/all_groups.html.twig', [
            'groups' => $group,
            'currentUser' => $currentUser,



        ]);
    }


    /**
     * Leave a group or join it
     *
     * @Route("/{id<^\d+$>}/leave", name="app_group_leave")
     * @param $id
     * @return RedirectResponse
     */
            public function leaveGroup($id){

                    /** @var User $currentUser */
                    $currentUser = $this->getUser();

                    /** @var Group $group */
                    // On va chercher l'utilisateur correspondant en BDD
                    $group = $this
                        ->getDoctrine()
                        ->getRepository(Group::class)
                        ->find($id);

                    // validation (post existant, ...)
                    if (empty($group)) {
                        throw $this->createNotFoundException("Le groupe n'existe pas :(");
                    }

                if($currentUser->isMemberOf($group)){
                    $currentUser->removeGroupMember($group);

                    $this->getDoctrine()->getManager()->flush();
                    return $this->redirectToRoute('app_group_list', [
                        'currentUser' => $currentUser
                    ]);
                }
                else{
                    $currentUser->addGroupMember($group);
                    $this->getDoctrine()->getManager()->flush();
                    return $this->redirectToRoute('app_group_single', [
                        'id' => $id,
                        'currentUser' => $currentUser
                    ]);
                }

            }

}
