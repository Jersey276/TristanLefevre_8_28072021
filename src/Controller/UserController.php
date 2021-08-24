<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    private UserManager $manager;

    public function __construct(UserManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/users", name="user_list")
     */
    public function listAction()
    {
        return $this->render(
            'user/list.html.twig', 
            [
                'users' => $this->getDoctrine()->getRepository(User::class)->findAll()
            ]
        );
    }

    /**
     * @Route("/users/create", name="user_create")
     */
    public function createAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->manager->save($user)) {
                $this->addFlash('success', "L'utilisateur a bien été ajouté.");
                if ($this->getUser() != null) {
                    return $this->redirectToRoute('user_list');
                }
                return $this->redirectToRoute('app_login');
            } else {
                $this->addFlash("danger", "L'utilisateur n'a pu être ajouté.");
            }
        }

        return $this->render('user/form.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/users/{id}/edit", name="user_edit")
     */
    public function editAction(User $user, Request $request)
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->manager->update($user)) {
                $this->addFlash('success', "L'utilisateur a bien été mis à jour");
            }
            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/form.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
