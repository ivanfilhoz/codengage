<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class OrderRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function create()
    {
        return [
            'customer' => '',
            'items' => []
        ];
    }

    public function list()
    {
        $orders = $this->findAll();

        return array_map([$this, 'format'], $orders);
    }

    public function format($order)
    {
        return [
            'id' => $order->getId(),
            'number' => $order->getNumber(),
            'customer' => $order->getCustomer(),
            'issuedAt' => $order->getIssuedAt(),
            'formattedIssuedAt' => $order->getIssuedAt()->format('d/m/Y'),
            'totalPrice' => $order->getTotalPrice(),
            'formattedTotalPrice' => 'R$ ' . str_replace('.', ',', number_format($order->getTotalPrice(), 2))
        ];
    }

    public function generateNumber()
    {
        $query = $this->createQueryBuilder('o')
            ->select('MAX(o.number)')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
        ;
        $max = $query[0][1] ?? 0;

        return $max + 1;
    }

    public function generateTotalPrice($items)
    {
        $total = 0;

        foreach ($items as $item) {
            $total +=
                $item->getUnitPrice() *
                $item->getQuantity() *
                (1 - ($item->getPercentDiscount() / 100));
        }

        return $total;
    }
}
