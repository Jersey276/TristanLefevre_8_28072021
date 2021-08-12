<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Manager\TaskManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class TaskController extends AbstractController
{
    private TaskManager $manager;

    public function __construct(TaskManager $manager)
    {
        $this->manager = $manager;
    }
    /**
     * @Route("/tasks", name="task_list")
     */
    public function listAction()
    {
        return $this->render(
            'task/list.html.twig', 
            [
                'tasks' => $this->getDoctrine()->getRepository(Task::class)->findBy([
                    'isDone' => false
                ]),
                'done' => false

            ]
        );
    }

    /**
     * @Route("/task/done", name="task_done_list")
     */
    public function listDoneAction()
    {
        return $this->render(
            'task/list.html.twig',
            [
                'tasks' => $this->getDoctrine()->getRepository(Task::class)->findBy([
                    'isDone' => true
                ]),
                'done' => true
            ]
        );
    }

    /**
     * @Route("/tasks/create", name="task_create")
     */
    public function createAction(Request $request)
    {
        $task = new Task();
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->manager->save($task, $user)) {
                $this->addFlash('success', 'La tâche a été bien été ajoutée.');
            } else {
                $this->addFlash('danger', 'la tâche n\'a pu être ajoutée');
            }
            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/form.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     */
    public function editAction(Task $task, Request $request)
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->manager->update($task)) {
                $this->addFlash('success', 'La tâche a bien été mise à jour.');
            } else {
                $this->addFlash('danger', 'La tâche n\'a pu être mise à jour.');
            }
            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/form.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     */
    public function toggleTaskAction(Task $task)
    {
        $task->toggle(!$task->isDone());
        if ($this->manager->update($task)) {
            if ($task->isDone()) {
                $result = 'terminé';
                $redirect = "task_list";
            } else {
                $result = 'non terminé';
                $redirect = "task_done_list";
            }
            $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme %s', $task->getTitle(), $result));
            return $this->redirectToRoute($redirect);
        } else {
            $this->addFlash('danger', 'La tâche n\'a pu être mise à jour.');
        }
        return $this->redirectToRoute('task_list');
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     */
    public function deleteTaskAction(Task $task)
    {
        if ($this->manager->remove($task)) {
            $this->addFlash('success', 'La tâche a bien été supprimée.');
        } else {
            $this->addFlash('danger', 'La tâche n\'a pu être supprimée.');
        }
        return $this->redirectToRoute('task_list');
    }
}
