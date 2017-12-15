<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Tool\IFormatterRepository;
use App\Tool\Formatter;
use App\Tool\Search;

class OrderRepository extends ServiceEntityRepository implements IFormatterRepository
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

    public function read($id)
    {
        $order = $this->find($id);

        return $this->format($order);
    }

    public function search($term)
    {
        $orders = $this->list();
        $search = new Search($orders);

        return $search->byKey($term, [
            'number',
            'customerName',
            'formattedIssuedAt',
            'formattedTotalPrice'
        ]);
    }

    public function format($order)
    {
        $formatter = new Formatter();

        return [
            'id' => $order->getId(),
            'number' => $order->getNumber(),
            'customer' => $order->getCustomer(),
            'customerName' => $order->getCustomer()->getName(),
            'issuedAt' => $order->getIssuedAt()->format('Y-m-d'),
            'formattedIssuedAt' => $formatter->formatDate($order->getIssuedAt()),
            'totalPrice' => $order->getTotalPrice(),
            'formattedTotalPrice' => $formatter->formatCurrency($order->getTotalPrice(), 2)
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
