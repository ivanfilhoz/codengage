<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Tool\IFormatterRepository;
use App\Tool\Formatter;
use App\Tool\Search;

class ProductRepository extends ServiceEntityRepository implements IFormatterRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function create()
    {
        return [
            'code' => '',
            'name' => '',
            'price' => 1
        ];
    }

    public function read($id)
    {
        $product = $this->find($id);

        return $this->format($product);
    }

    public function list()
    {
        $products = $this->findAll();

        return array_map([$this, 'format'], $products);
    }

    public function search($term)
    {
        $products = $this->list();
        $search = new Search($products);

        return $search->byKey($term, [
            'code',
            'name',
            'formattedPrice'
        ]);
    }

    public function format($product)
    {
        $formatter = new Formatter();

        return [
            'id' => $product->getId(),
            'code' => $product->getCode(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'formattedPrice' => $formatter->formatCurrency($product->getPrice(), 2)
        ];
    }
}
