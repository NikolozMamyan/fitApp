<?php

namespace App\Controller;

use DateTime;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'app_blog')]
    public function index(): Response
    {
        $posts = [
            [
                'id' => 1,
                'title' => 'Comment bien débuter le fitness',
                'content' => 'Les conseils essentiels pour commencer le fitness en douceur...',
                'image' => 'https://picsum.photos/200',
                'date' => new DateTime('2024-03-15'),
                'category' => 'Fitness'
            ],
            [
                'id' => 2,
                'title' => '5 exercices pour muscler son core',
                'content' => 'Découvrez les meilleurs exercices pour renforcer vos abdominaux...',
                'image' => 'https://picsum.photos/200',
                'date' => new \DateTime('2024-03-20'),
                'category' => 'Exercices'
            ],
        ];
    
        $plats = [
            [
                'id' => 1,
                'title' => 'Bowl protéiné au poulet',
                'content' => 'Un repas équilibré riche en protéines...',
                'image' => 'https://picsum.photos/200',
                'calories' => 450,
                'date' => new \DateTime('2024-03-18')
            ],
            [
                'id' => 2,
                'title' => 'Smoothie post-workout',
                'content' => 'Le smoothie parfait pour la récupération...',
                'image' => 'https://picsum.photos/200',
                'calories' => 280,
                'date' => new \DateTime('2024-03-19')
            ],
        ];
    
        return $this->render('blog/index.html.twig', [
            'posts' => $posts,
            'plats' => $plats
        ]);
    }

        

    
}
