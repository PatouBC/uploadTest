<?php

namespace App\ApiController;

use App\Repository\TaskRepository;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/task", host="api.upload.fr")
 */
class TaskController extends AbstractFOSRestController
{
    /**
     * Retrieves a collection of Task resource
     * @Route("/", name="tasklist_api", methods={ "GET" })
     * @Rest\View()
     */
    public function index(TaskRepository $taskRepository): View
    {
        $tasks = $taskRepository->findAll();
        return View::create($tasks, Response::HTTP_OK);
    }
}