<?php

namespace App\ApiController;

use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Entity\Product;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/product", host="api.upload.fr")
 */
class ProductController extends AbstractFOSRestController
{
    /**
     * Retrieves a collection of Task resource
     * @Rest\Get("/", name="productlist_api")
     * @Rest\View()
     */
    public function index(ProductRepository $productRepository): View
    {
        $products = $productRepository->findAll();
        return View::create($products, Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/{id}", name="productshow_api")
     */
    public function show(Product $product): View
    {
        return View::create($product, Response::HTTP_OK);
    }

    /**
     * @Rest\Post("/new", name="productcreate_api")
     */
    public function create(Request $request): View
    {
        $product = new Product();
        $product->setName($request->get('name'));
        $product->setDescription($request->get('description'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();
        return View::create($product, Response::HTTP_CREATED);
    }

    /**
     * @Rest\Delete("/{id}", name="productdelete_api")
     */
    public function delete(Product $product): View
    {
        if($product)
        {
            $em=$this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
        }
        return View::create([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @Rest\Put("/{id}", name="productedit_api")
     */
    public function edit(Request $request, Product $product)
    {
        if($product){
            $product->setName($request->get('name'));
            $product->setDescription($request->get('description'));
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
        }
        return View::create($product, Response::HTTP_OK);
    }

    /**
     * @Rest\Patch("/{id}", name="productpatch_api")
     */
    public function patch(Request $request, Product $product)
    {
        if($product){
            $form = $this->createForm(ProductType::class, $product);
            $form->submit($request->request->all(), false);
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
        }
        return View::create($product, Response::HTTP_OK);
    }

}