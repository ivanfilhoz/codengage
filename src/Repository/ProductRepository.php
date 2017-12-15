<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ProductRepository extends ServiceEntityRepository
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

    public function format($product)
    {
        return [
            'id' => $product->getId(),
            'code' => $product->getCode(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'formattedPrice' => 'R$ ' . str_replace('.', ',', number_format($product->getPrice(), 2))
        ];
    }
}
