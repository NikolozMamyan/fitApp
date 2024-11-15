<?php

namespace App\DataFixtures;

use App\Entity\Exercise;
use App\Entity\Variation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Données initiales des exercices
        $exercisesData = [
            [
                'id' => 'squats',
                'title' => 'Squats',
                'description' => 'Faites 3 séries de 10 squats.',
                'image' => '/images/squat.jpg',
                'variations' => [
                    [
                        'title' => 'Squats classiques',
                        'description' => 'Effectuez des squats en gardant le dos droit',
                        'series' => 3,
                        'repetitions' => 10,
                        'restTime' => 60,
                    ],
                    [
                        'title' => 'Squats sautés',
                        'description' => 'Effectuez des squats avec un saut à la montée',
                        'series' => 3,
                        'repetitions' => 8,
                        'restTime' => 60,
                    ],
                    [
                        'title' => 'Corde à sauter',
                        'description' => 'Sautez à la corde à un rythme modéré',
                        'duration' => 120, // 2 minutes
                        'series' => 1,
                        'restTime' => 60,
                    ],
                ],
            ],
            [
                'id' => 'planches',
                'title' => 'Planches',
                'description' => 'Tenez la planche pendant 30 secondes.',
                'image' => '/images/planche.jpg',
                'variations' => [
                    [
                        'title' => 'Planche classique',
                        'description' => 'Maintenez la position de planche standard',
                        'duration' => 30,
                        'series' => 3,
                        'restTime' => 60,
                    ],
                    [
                        'title' => 'Planche latérale',
                        'description' => 'Maintenez la position de planche sur le côté',
                        'duration' => 30,
                        'series' => 2,
                        'restTime' => 60,
                    ],
                ],
            ],
            [
                'id' => 'burpees',
                'title' => 'Burpees',
                'description' => 'Faites 15 burpees.',
                'image' => '/images/burpees.jpg',
                'variations' => [
                    [
                        'title' => 'Burpees standard',
                        'description' => 'Effectuez des burpees complets avec saut',
                        'series' => 3,
                        'repetitions' => 15,
                        'restTime' => 90,
                    ],
                ],
            ],
        ];

        foreach ($exercisesData as $exerciseData) {
            $exercise = new Exercise();
            $exercise->setTitle($exerciseData['title']);
            $exercise->setDescription($exerciseData['description']);
            $exercise->setImage($exerciseData['image']);

            // Ajouter les variations
            foreach ($exerciseData['variations'] as $variationData) {
                $variation = new Variation();
                $variation->setTitle($variationData['title']);
                $variation->setDescription($variationData['description']);
                $variation->setSeries($variationData['series']);
                $variation->setRestTime($variationData['restTime']);

                if (isset($variationData['repetitions'])) {
                    $variation->setRepetitions($variationData['repetitions']);
                } else {
                    $variation->setRepetitions(null);
                }

                if (isset($variationData['duration'])) {
                    $variation->setDuration($variationData['duration']);
                } else {
                    $variation->setDuration(null);
                }
                if (isset($exerciseData['finalRestTime'])) {
                    $exercise->setFinalRestTime($exerciseData['finalRestTime']);
                }

                $variation->setExercise($exercise);
                $manager->persist($variation);
            }

            $manager->persist($exercise);
        }

        $manager->flush();
    }
}
