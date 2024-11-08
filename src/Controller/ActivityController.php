<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ActivityController extends AbstractController
{
    #[Route('/activity', name: 'app_activity')]
    public function index(): Response
    {
        $exercises = [
            [
                'title' => 'Squats',
                'description' => 'Faites 3 séries de 10 squats.',
                'image' => '/images/squat.jpg',
            ],
            [
                'title' => 'Planches',
                'description' => 'Tenez la planche pendant 30 secondes.',
                'image' => '/images/planche.jpg',
            ],
            [
                'title' => 'Burpees',
                'description' => 'Faites 15 burpees.',
                'image' => '/images/burpees.jpg',
            ],
        ];

        return $this->render('activity/index.html.twig', [
            'controller_name' => 'ActivityController',
            'exercises' => $exercises,
        ]);
    }
}
