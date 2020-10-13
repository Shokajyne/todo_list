<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Task;
use App\Entity\SubTask;
use App\Repository\TaskRepository;
use App\Form\TaskType;
use App\Form\SubTaskType;

class TodoTaskController extends AbstractController
{
    /**
     * @Route("/todo/task", name="todo_task")
     */
    public function index(TaskRepository $repo)
    {
    	$tasks = $repo->findAll();

        return $this->render('todo_task/index.html.twig', [
            'controller_name' => 'TodoTaskController',
            'tasks' => $tasks
        ]);
    }

    /**
    * @Route("/", name="home")
    */
    public function home() {
    	return $this->render('todo_task/home.html.twig', [
    		'person' => "justine",
    		'title' => "Hey !"
    	]);
    }

    /**
	* @Route("/todo/task/new", name="todo_task_add")
	* @Route("/todo/task/{id}/edit", name="todo_task_edit")
    */
    public function form(Task $task = null, Request $request, EntityManagerInterface $manager){
    	if(!$task) {
    		$task = New Task();
    	}

    	$form = $this->createForm(TaskType::class, $task);

    	$form->handleRequest($request);

    	if($form->isSubmitted() && $form->isValid()) {
    		if(!$task->getId()) {
	    		$task->setDone(false);
	    		$task->setCreatedAt(new \DateTime());
    		}

	    	$manager->persist($task);
	    	$manager->flush($task);

	    	return $this->redirectToRoute('todo_task_show', ['id' => $task->getId()]);
    	}

    	return $this->render('todo_task/addTask.html.twig', [
    		'formTask' => $form->createView(),
    		'editMode' => $task->getId() !== null
    	]);
    }

    /**
    * @Route("/todo/task/{id}", name="todo_task_show")
    */
    public function show(Task $task, Request $request, EntityManagerInterface $manager){

		$subtask = new SubTask;

		$form = $this->createForm(SubTaskType::class, $subtask);

    	$form->handleRequest($request);

    	if($form->isSubmitted() && $form->isValid()) {
    		if(!$subtask->getId()) {
				$subtask->setTask($task)
	    				->setCreatedAt(new \DateTime());
    		}

	    	$manager->persist($subtask);
	    	$manager->flush($subtask);
		}

    	return $this->render('todo_task/show.html.twig', [
			'task' => $task,
			'formSubTask' => $form->createView()
    	]);
    }
}
