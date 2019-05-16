<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/product", host="upload.fr")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/", name="product_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="product_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $form->get('image')->get('file')->getData();
            if($file){
                $image = new Image();
                $fileName=$this->generateUniqueFileName().'.'.$file->guessExtension();

                try{
                    $file->move(
                        $this->getParameter('image_abs_path'),
                        $fileName
                    );
                }catch(FileException $e){

                }

                $image->setPath($this->getParameter('image_abs_path').'/'.$fileName);
                $image->setImgPath($this->getParameter('image_path').'/'.$fileName);
                $entityManager->persist($image);
                $product->setImage($image);
            }else{
                $product->setImage(null);
            }

            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }

    /**
     * @Route("/{id}", name="product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $image = $product->getImage();
            $file = $form->get('image')->get('file')->getData();

            if($file)
            {

                $fileName=$this->generateUniqueFileName().'.'.$file->guessExtension();

                try{
                    $file->move(
                        $this->getParameter('image_abs_path'),
                        $fileName
                    );
                }catch(FileException $e){

                }
                $this->removeFile($image->getPath());

                $image->setPath($this->getParameter('image_abs_path').'/'.$fileName);
                $image->setImgPath($this->getParameter('image_path').'/'.$fileName);
                $entityManager->persist($image);
                $product->setImage($image);
            }
            if(empty($image->getId())&& !$file)
            {
                $product->setImage(null);
            }
            $entityManager->flush();

            return $this->redirectToRoute('product_index', [
                'id' => $product->getId(),
            ]);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $image = $product->getImage();
            if($image){
                $this->removeFile($image->getPath());
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index');
    }
    /**
     * @Route("/{id}", name="product_image_delete", methods={"POST"})
     */
    public function deleteImg(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $image = $product->getImage();
            $this->removeFile($image->getPath());
            $product->setImage(null);

            $entityManager->remove($image);
            $entityManager->persist($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_edit', array('id'=>$product->getId()));
    }
    private function removeFile($path)
    {
        if(file_exists($path))
        {
            unlink($path);
        }
    }
}
