<?php

namespace App\Repository;

use App\Entity\Person;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use App\Tool\IFormatterRepository;
use App\Tool\Formatter;
use App\Tool\Search;

class PersonRepository extends ServiceEntityRepository implements IFormatterRepository
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

    public function search($term)
    {
        $persons = $this->list();
        $search = new Search($persons);

        return $search->byKey($term, [
            'name',
            'formattedBirthdate'
        ]);
    }

    public function format($person)
    {
        $formatter = new Formatter();

        return [
            'id' => $person->getId(),
            'name' => $person->getName(),
            'birthdate' => $person->getBirthdate()->format('Y-m-d'),
            'formattedBirthdate' => $formatter->formatDate($person->getBirthdate()),
        ];
    }
}
