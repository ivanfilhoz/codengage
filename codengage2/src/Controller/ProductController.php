<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;

class ProductController extends Controller
{
    /**
     * @Route("/products", name="products")
     */
    public function index()
    {
        $actions = [
            'create' => '/products/new'
        ];

        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->list();

        return $this->render('products.html.twig', [
            'actions' => $actions,
            'products' => array_map(function ($product) {
                $actions = [
                    'edit' => '/products/' . $product['id'],
                    'delete' => '/products/' . $product['id'] . '/delete'
                ];

                return array_merge($product, [
                    'actions' => $actions
                ]);
            }, $products)
        ]);
    }

    /**
     * @Route("/products/{id}", name="products_single")
     */
    public function single($id)
    {
        $actions = [
            'back' => '/products',
            'save' => '/products/' . $id  . '/save'
        ];

        $repository = $this->getDoctrine()
            ->getRepository(Product::class);

        if ($id === 'new') {
            $product = $repository->create();
        } else {
            $product = $repository->read($id);
        }

        return $this->render('products_single.html.twig', [
            'actions' => $actions,
            'product' => $product
        ]);
    }

    /**
     * @Route("/products/{id}/save", name="products_save")
     */
    public function save($id)
    {
        $em = $this->getDoctrine()
            ->getManager();

        if ($id === 'new') {
            $product = new Product();
        } else {
            $product = $this->getDoctrine()
                ->getRepository(Product::class)
                ->find($id);
        }

        $request = Request::createFromGlobals();

        $product->setCode($request->request->get('code'));
        $product->setName($request->request->get('name'));
        $product->setPrice($request->request->get('price'));

        $validator = $this->get('validator');
        $errors = $validator->validate($product);

        if (count($errors) > 0) {
            return $this->render('message.html.twig', [
                'action' => $request->headers->get('referer')
            ]);
        } else {
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('products');
        }

    }

    /**
     * @Route("/products/{id}/delete", name="products_delete")
     */
    public function delete($id)
    {
        $em = $this->getDoctrine()
            ->getManager();

        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);

        $em->remove($product);
        $em->flush();

        return $this->redirectToRoute('products');
    }
}
