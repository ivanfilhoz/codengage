<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderItemRepository")
 */
class OrderItem
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="items")
     * @ORM\JoinColumn(name="order", referencedColumnName="id")
     */
    private $order;

    /**
     * @ORM\OneToOne(targetEntity="Product")
     * @Assert\NotBlank()
     */
    private $product;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThan(0)
     */
    private $quantity;

    /**
     * @ORM\Column(type="float")
     * @Assert\GreaterThan(0)
     */
    private $unitPrice;

    /**
     * @ORM\Column(type="float")
     * @Assert\Range(min = 0,max = 100)
     */
    private $percentDiscount;

    /**
     * @ORM\Column(type="float")
     * @Assert\GreaterThan(0)
     */
    private $totalPrice;

    public function getId()
    {
        return $this->id;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder($order)
    {
        $this->order = $order;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function setProduct($product)
    {
        $this->product = $product;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;
    }

    public function getPercentDiscount()
    {
        return $this->percentDiscount;
    }

    public function setPercentDiscount($percentDiscount)
    {
        $this->percentDiscount = $percentDiscount;
    }

    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;
    }
}
