<?php

namespace App\Repository;

use App\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class PersonRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Person::class);
    }

    public function create()
    {
        return [
            'name' => '',
            'birthdate' => date('Y-m-d')
        ];
    }

    public function read($id)
    {
        $person = $this->find($id);

        return $this->format($person);
    }

    public function list()
    {
        $persons = $this->findAll();

        return array_map([$this, 'format'], $persons);
    }

    public function format($person)
    {
        return [
            'id' => $person->getId(),
            'name' => $person->getName(),
            'birthdate' => $person->getBirthdate()->format('Y-m-d'),
            'formattedBirthdate' => $person->getBirthdate()->format('d/m/Y')
        ];
    }
}
