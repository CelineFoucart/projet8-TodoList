<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Security\Voter\TaskVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("ROLE_USER")]
class TaskController extends AbstractController
{
    #[Route(path: '/tasks', name: 'task_list', methods:['GET'])]
    public function listAction(TaskRepository $taskRepository): Response
    {
        return $this->render('task/list.html.twig', ['tasks' => $taskRepository->findAll()]);
    }
    
    #[Route(path: '/tasks/create', name: 'task_create', methods:['GET', 'POST'])]
    public function createAction(Request $request, EntityManagerInterface $em): Response
    {
        $task = (new Task())->setAuthor($this->getUser());
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }
    
    #[Route(path: '/tasks/{id}/edit', name: 'task_edit', methods:['GET', 'POST'])]
    public function editAction(Task $task, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted(TaskVoter::EDIT, $task);
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }
    
    #[Route(path: '/tasks/{id}/toggle', name: 'task_toggle', methods:['POST'])]
    public function toggleTaskAction(Task $task, EntityManagerInterface $em, Request $request): Response
    {
        $this->denyAccessUnlessGranted(TaskVoter::EDIT, $task);
        
        if ($this->isCsrfTokenValid('toggle'.$task->getId(), $request->request->get('_token'))) {
            $task->toggle(!$task->isDone());
            $em->persist($task);
            $em->flush();
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }
    
    #[Route(path: '/tasks//{id}/delete', name: 'task_delete', methods:['POST'])]
    public function deleteTaskAction(Task $task, EntityManagerInterface $em, Request $request): Response
    {
        $this->denyAccessUnlessGranted(TaskVoter::DELETE, $task);
        
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $em->remove($task);
            $em->flush();
            $this->addFlash('success', 'La tâche a bien été supprimée.');
        } else {
            $this->addFlash('error', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('task_list');
    }
}
