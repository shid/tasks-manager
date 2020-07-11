<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/task")
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/", name="task_index", methods={"GET"})
     * @param UserInterface $userLogged
     * @param Request $request
     * @return Response
     */
    public function index(UserInterface $userLogged, Request $request): Response
    {
        $tasks = $userLogged->getTasks();

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks
        ]);
    }

    /**
     * @Route("/new", name="task_new", methods={"GET","POST"})
     * @param Request $request
     * @param UserInterface $userLogged
     * @return Response
     */
    public function new(Request $request, UserInterface $userLogged): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $task_form = $request->request->get('task');
        if($task_form) {
            $task_form['user'] = (string)$userLogged->getId();
        }
        $request->request->set('task', $task_form);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('task_index');
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="task_show", methods={"GET"})
     * @param Task $task
     * @param UserInterface $userLogged
     * @return Response
     */
    public function show(Task $task, UserInterface $userLogged): Response
    {
        if($userLogged->getId() !== $task->getUser()->getId()) return $this->redirectToRoute('task_index');

        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="task_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Task $task
     * @param UserInterface $userLogged
     * @return Response
     */
    public function edit(Request $request, Task $task, UserInterface $userLogged): Response
    {
        if($userLogged->getId() !== $task->getUser()->getId()) return $this->redirectToRoute('task_index');

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('task_index');
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="task_delete", methods={"DELETE"})
     * @param Request $request
     * @param Task $task
     * @param UserInterface $userLogged
     * @return Response
     */
    public function delete(Request $request, Task $task, UserInterface $userLogged): Response
    {
        if($userLogged->getId() !== $task->getUser()->getId()) return $this->redirectToRoute('task_index');

        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($task);
            $entityManager->flush();
        }

        return $this->redirectToRoute('task_index');
    }
}
