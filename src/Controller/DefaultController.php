<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="accueilClass_")
 */
class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        return $this ->render('default/index.html.twig',
            ['base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR]);
    }
    /**
     * @Route("/admin", name="homeAdmin")
     * @IsGranted("ROLE_ADMIN")
     */
    public function indexAdmin()
    {
        return $this ->render('default/index.html.twig',
            ['base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR]);
    }

}
