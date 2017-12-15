<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Person;

class PersonController extends Controller
{
    /**
     * @Route("/persons", name="persons")
     */
    public function index()
    {
        $actions = [
            'create' => '/persons/new'
        ];

        $request = Request::createFromGlobals();
        $repository = $this->getDoctrine()
            ->getRepository(Person::class);
        $search = $request->get('search');

        if ($search) {
            $persons = $repository->search($search);
        } else {
            $persons = $repository->list();
        }

        return $this->render('persons.html.twig', [
            'actions' => $actions,
            'persons' => array_map(function ($person) {
                $actions = [
                    'edit' => '/persons/' . $person['id'],
                    'delete' => '/persons/' . $person['id'] . '/delete'
                ];

                return array_merge($person, [
                    'actions' => $actions
                ]);
            }, $persons)
        ]);
    }

    /**
     * @Route("/persons/{id}", name="persons_single")
     */
    public function single($id)
    {
        $actions = [
            'back' => '/persons',
            'save' => '/persons/' . $id  . '/save'
        ];

        $repository = $this->getDoctrine()
            ->getRepository(Person::class);

        if ($id === 'new') {
            $person = $repository->create();
        } else {
            $person = $repository->read($id);
        }

        return $this->render('persons_single.html.twig', [
            'actions' => $actions,
            'person' => $person
        ]);
    }

    /**
     * @Route("/persons/{id}/save", name="persons_save")
     */
    public function save($id)
    {
        $em = $this->getDoctrine()
            ->getManager();

        if ($id === 'new') {
            $person = new Person();
        } else {
            $person = $this->getDoctrine()
                ->getRepository(Person::class)
                ->find($id);
        }

        $request = Request::createFromGlobals();

        $person->setName($request->request->get('name'));
        $birthdate = \DateTime::createFromFormat('Y-m-d', $request->request->get('birthdate'));
        $person->setBirthdate($birthdate);

        $validator = $this->get('validator');
        $errors = $validator->validate($person);

        if (count($errors) > 0) {
            return $this->render('message.html.twig', [
                'action' => $request->headers->get('referer')
            ]);
        } else {
            $em->persist($person);
            $em->flush();

            return $this->redirectToRoute('persons');
        }

    }

    /**
     * @Route("/persons/{id}/delete", name="persons_delete")
     */
    public function delete($id)
    {
        $em = $this->getDoctrine()
            ->getManager();

        $person = $this->getDoctrine()
            ->getRepository(Person::class)
            ->find($id);

        $em->remove($person);
        $em->flush();

        return $this->redirectToRoute('persons');
    }
}
