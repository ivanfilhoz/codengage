<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Person;
use App\Entity\Product;
use App\Entity\Order;
use App\Entity\OrderItem;

class OrderController extends Controller
{
    /**
     * @Route("/orders", name="orders")
     */
    public function index()
    {
        $actions = [
            'create' => '/orders/new'
        ];

        $orders = $this->getDoctrine()
            ->getRepository(Order::class)
            ->list();

        return $this->render('orders.html.twig', [
            'actions' => $actions,
            'orders' => array_map(function ($order) {
                $customer = $this->getDoctrine()
                    ->getRepository(Person::class)
                    ->read($order['customer']->getId());

                $actions = [
                    'delete' => '/orders/' . $order['id'] . '/delete'
                ];

                return array_merge($order, [
                    'customer' => $customer,
                    'actions' => $actions
                ]);
            }, $orders)
        ]);
    }

    /**
     * @Route("/orders/new", name="orders_single")
     */
    public function single()
    {
        $actions = [
            'back' => '/orders',
            'save' => '/orders/save'
        ];

        $persons = $repository = $this->getDoctrine()
            ->getRepository(Person::class)
            ->list();

        $products = $repository = $this->getDoctrine()
            ->getRepository(Product::class)
            ->list();

        $repository = $this->getDoctrine()
            ->getRepository(Order::class);
        $order = $repository->create();

        return $this->render('orders_single.html.twig', [
            'actions' => $actions,
            'persons' => $persons,
            'products' => $products,
            'order' => $order
        ]);
    }

    /**
     * @Route("/orders/save", name="orders_save")
     */
    public function save()
    {
        $em = $this->getDoctrine()
            ->getManager();

        $order = new Order();

        $request = Request::createFromGlobals();

        try {
            $validator = $this->get('validator');

            // Customer
            $customerId = $request->request->get('customer');
            if (!$customerId) {
                throw new \Exception('customer-not-specified');
            }
            $customer = $this->getDoctrine()
                ->getRepository(Person::class)
                ->find($request->request->get('customer'));
            if (!$customer) {
                throw new \Exception('customer-invalid');
            }
            $order->setCustomer($customer);

            // Items
            $itemsInput = json_decode($request->request->get('items'));
            $items = [];
            foreach ($itemsInput as $itemInput) {
                $product = $this->getDoctrine()
                    ->getRepository(Product::class)
                    ->find($itemInput->id);
                if (!$product) {
                    throw new \Exception('product-invalid');
                }

                $item = new OrderItem();
                $item->setOrder($order);
                $item->setProduct($product);
                $item->setQuantity($itemInput->quantity);
                $item->setPercentDiscount($itemInput->percentDiscount);

                $errors = $validator->validate($item);
                if (count($errors) > 0) {
                    throw new \Exception('validation-error-items');
                }

                $item->setUnitPrice($product->getPrice());
                $item->setTotalPrice($itemInput->quantity * $product->getPrice());

                $items[] = $item;
            }

            // Order
            $repository = $this->getDoctrine()
                ->getRepository(Order::class);
            $order->setNumber($repository->generateNumber());
            $order->setTotalPrice($repository->generateTotalPrice($items));

            $errors = $validator->validate($order);
            if (count($errors) > 0) {
                throw new \Exception('validation-error');
            } else {
                $em->persist($order);
                $em->flush();

                return $this->redirectToRoute('orders');
            }
        } catch (\Exception $e) {
            return $this->render('message.html.twig', [
                'message' => $e->getMessage(),
                'action' => $request->headers->get('referer')
            ]);
        }
    }

    /**
     * @Route("/orders/{id}/delete", name="orders_delete")
     */
    public function delete($id)
    {
        $em = $this->getDoctrine()
            ->getManager();

        $order = $this->getDoctrine()
            ->getRepository(Order::class)
            ->find($id);

        $em->remove($order);
        $em->flush();

        return $this->redirectToRoute('orders');
    }
}
