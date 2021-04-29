<?php

namespace App\Controller;

use DateTime;
use App\Entity\Todo;
use App\Entity\Category;
use App\Form\TodoFormType;
use App\Repository\TodoRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TodoController extends AbstractController
{
    private $categories;

    function __construct(CategoryRepository $repo)
    {
        $this->categories = $repo->findAll();
    }



    /**
     * @Route("/", name="app_todo")
     */
    public function index(TodoRepository $repo): Response
    {
        $todos = $repo->findAll();
        //dd($todos);
        return $this->render('todo/index.html.twig', [
            'todos' => $todos,
            'categories' => $this->categories 
        ]);
    }



    /**
     * @Route("/detail/{id}", name="app_todo_detail", methods={"GET"})
     *
     * @return response
     */
    public function detail($id, TodoRepository $repo): Response
    { 
        $todo = $repo->find($id);
        //dd($todo);
        return $this->render('todo/detail.html.twig', [
            'todo' => $todo,
            'categories' => $this->categories
        ]);
    }


    /**
     * @Route("/create", priority=1, name="app_todo_create", methods={"GET", "POST"})
     *
     * @return void
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        // étape 1. Affichage (GET)
        $todo = new Todo;
        $form = $this->createForm(TodoFormType::class, $todo);
        // étape 2. Soumission (POST)
        $form-> handleRequest($request);
        if($form->isSubmitted() && $form-> isValid()){
            //$this->getDoctrine()->getManager()-persist($todo);
            $em->persist($todo);
            $em->flush();
            return $this->redirectToRoute('app_todo');
        }


        return $this->render('todo/create.html.twig', [
            'formTodo'=> $form -> createView(),
            'categories' => $this->categories
        ]);
    }

    /**
     * paramconverter => conrrespondance entre un ID dans la route et un objet de type Todo
     * @Route("/edit/{id}", name="app_todo_edit", methods={"GET","POST"})
     *
     * @return response
     */
    public function edit(Todo $todo, Request $request, EntityManagerInterface $em): Response
    { 
        //dd($todo);
        $form = $this->createForm(TodoFormType::class, $todo);
        $form-> handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $todo->setUpdatedAt(new \DateTime('now'));
            $em->flush();
            $this->addFlash(
                'info',
                'Modifictation enregistrée avec succés!'
            );
            return $this->redirectToRoute('app_todo_edit', ['id' => $todo->getId()]);
        }
        return $this->render('todo/edit.html.twig', [
            'formTodo' => $form->createView(),
            'todo' => $todo,
            'categories' => $this->categories
        ]);
    }


    /**
     * @Route("/todo{id}/delete", name="app_todo_delete", methods={"GET"})
     *
     * @param Todo $todo
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function delete(Todo $todo, EntityManagerInterface $em): Response
    {
        $em->remove($todo);
        $em->flush();
        return $this->redirectToRoute('app_todo');
    }


        /**
     * @Route("/todo{id}/deletecsrf", name="app_todo_delete_csrf", methods={"DELETE"})
     *
     * @param Todo $todo
     * @param EntityManagerInterface $em
     * @return void
     * 
     * $request->request->get()    : POST
     * $request->query->get()    : GET
     */
    public function delete2(Todo $todo, EntityManagerInterface $em, Request $request): Response
    {
        $submittedToken = $request->request->get('token');
        //dd($submittedToken);
        if($this->isCsrfTokenValid('delete-item', $submittedToken)){
            $em->remove($todo);
            $em->flush();
        }

        return $this->redirectToRoute('app_todo');
    }

    /**
     * @Route("/todo{id}/category", name="app_todo_category", methods={"GET"})
     *
     * @param Category $cat
     * @return void
     */
    public function todoByCategory(Category $cat) : Response
    {
        return $this->render('todo/index.html.twig', [
            'todos'=>$cat->getTodos(),
            'categories'=>$this->categories
        ]);
    }


}
