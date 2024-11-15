<?php

namespace App\Controller;

use App\Entity\Exercise;
use App\Repository\ExerciseRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ActivityController extends AbstractController
{
    #[Route('/activity', name: 'app_activity')]
    public function index(ExerciseRepository $exerciseRepository): Response
    {
        $exercises = $exerciseRepository->findAll();


        return $this->render('activity/index.html.twig', [
            'exercises' => $exercises,
        ]);
    }


    #[Route('/exercise/{id}', name: 'exercise_start')]
    public function start(int $id, ExerciseRepository $exerciseRepository): Response
    {
        $exercise = $exerciseRepository->findWithVariations($id);
    
        if (!$exercise) {
            throw $this->createNotFoundException('Exercice non trouvé');
        }
    
        // Préparer les variations sous forme de JSON
        $variations = $exercise->getVariations()->toArray();
        $data = array_map(fn($v) => $v->toArray(), $variations);
  
        
    
        // Passer uniquement l'exercice à la vue Twig
        return $this->render('activity/start.html.twig', [
            'exercise' => $exercise,
            'data' => $data, // On peut le passer en option
        ]);
    }
}
