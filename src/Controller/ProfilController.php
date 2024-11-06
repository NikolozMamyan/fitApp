<?php

namespace App\Controller;

use DateTime;
use App\Entity\Stats;
use App\Entity\UserData;
use App\Entity\DailyStats;
use App\Form\UserDataType;
use App\Form\StatsUserType;
use App\Entity\FoodConsumed;
use App\Repository\UserDataRepository;
use App\Repository\DailyStatsRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FoodConsumedRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(DailyStatsRepository $dailyStatsRepo, UserDataRepository $userDataRepo, FoodConsumedRepository $foodConsumedRepo): Response
    {
        $user = $this->getUser();
        $dailyStatsArray = [];
        
        foreach ($user->getUserData() as $userData) {
            foreach ($userData->getDailyStats() as $dailyStat) {
                $dailyStatsArray[] = [
                    'calories' => $dailyStat->getCalories(),
                    'proteins' => $dailyStat->getProteins(),
                    'glucides' => $dailyStat->getGlucides(),
                    'lipides' => $dailyStat->getLipides(),
                    'fibres' => $dailyStat->getFibres(),
                    'eau' => $dailyStat->getEau(),
                ];
            }
        }
    
        if (empty($dailyStatsArray)) {
            $dailyStatsArray = null;
        } else {
            // Supposons que vous voulez utiliser le premier élément du tableau
            $caloriesGoal = $dailyStatsArray[0]['calories'];
            $proteinsGoal = $dailyStatsArray[0]['proteins'];
            $carbsGoal = $dailyStatsArray[0]['glucides'];
            $lipidsGoal = $dailyStatsArray[0]['lipides'];
            $fibersGoal = $dailyStatsArray[0]['fibres'];
            $waterGoal = $dailyStatsArray[0]['eau'];
    
            // Récupérer les aliments consommés aujourd'hui
            $today = new DateTime();
            $today->setTime(0, 0, 0);
    
            $foodConsumedToday = $foodConsumedRepo->createQueryBuilder('fc')
                ->where('fc.user = :user')
                ->andWhere('fc.consumedAt >= :today')
                ->setParameter('user', $user)
                ->setParameter('today', $today)
                ->getQuery()
                ->getResult();
    
            // Calculer les totaux des nutriments consommés
            $caloriesConsumed = 0;
            $proteinsConsumed = 0;
            $carbsConsumed = 0;
            $lipidsConsumed = 0;
            $fibersConsumed = 0; // Si applicable
            $waterConsumed = 0; // Si applicable
    
            foreach ($foodConsumedToday as $foodConsumed) {
                $caloriesConsumed += $foodConsumed->getCalories();
                $proteinsConsumed += $foodConsumed->getProteins();
                $carbsConsumed += $foodConsumed->getCarbs();
                $lipidsConsumed += $foodConsumed->getFats();
                // $fibersConsumed += $foodConsumed->getFibers(); // Si vous enregistrez les fibres
                // $waterConsumed += $foodConsumed->getWater(); // Si vous enregistrez l'eau
            }
    
            // Calculer les pourcentages
            $caloriesPercentage = ($caloriesGoal > 0) ? ($caloriesConsumed / $caloriesGoal) * 100 : 0;
            $proteinsPercentage = ($proteinsGoal > 0) ? ($proteinsConsumed / $proteinsGoal) * 100 : 0;
            $carbsPercentage = ($carbsGoal > 0) ? ($carbsConsumed / $carbsGoal) * 100 : 0;
            $lipidsPercentage = ($lipidsGoal > 0) ? ($lipidsConsumed / $lipidsGoal) * 100 : 0;
            // Faites de même pour les fibres et l'eau
    
            return $this->render('profil/index.html.twig', [
                'stats' => $dailyStatsArray,
                'calories_goal' => $caloriesGoal,
                'calories_consumed' => $caloriesConsumed,
                'calories_percentage' => round($caloriesPercentage),
                'proteins_goal' => $proteinsGoal,
                'proteins_consumed' => $proteinsConsumed,
                'proteins_percentage' => round($proteinsPercentage),
                'carbs_goal' => $carbsGoal,
                'carbs_consumed' => $carbsConsumed,
                'carbs_percentage' => round($carbsPercentage),
                'fibers_goal' => $fibersGoal,
                'fibers_consumed' => $fibersConsumed,
                // 'fibers_percentage' => round($fibersPercentage),
                // 'water_goal' => $waterGoal,
                'water_consumed' => $waterConsumed,
                // 'water_percentage' => round($waterPercentage),
                'lipids_goal' => $lipidsGoal,
                'lipids_consumed' => $lipidsConsumed,
                'lipids_percentage' => round($lipidsPercentage),
            ]);
        }
    
        return $this->render('profil/index.html.twig', [
            'stats' => $dailyStatsArray,
        ]);
    }
    
    
    #[Route('/calcul', name: 'app_calcul')]
public function calcul(EntityManagerInterface $em, Request $request): Response
{
    $userData = new UserData();
    $form = $this->createForm(UserDataType::class, $userData);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Récupérer les données
        $taille = $userData->getTaille();
        $poids = $userData->getPoids();
        $age = $userData->getAge();
        $sexe = $userData->getSexe();
        $objectif = $userData->getObjectif();
        $userData->setUser($this->getuser());
        
        $em->persist($userData);
        $em->flush();
        // Calculs (BMR, TDEE, etc.)
        $bmr = $this->calculBMR($poids, $taille, $age, $sexe);
        $tdee = $this->calculTDEE($bmr);

        if ($objectif === 'sèche') {
            $calories = $tdee - 500;
        } else {
            $calories = $tdee + 500;
        }

        $proteines = $poids * 2;
        $lipides = $poids * 1;
        $glucides = ($calories - ($proteines * 4) - ($lipides * 9)) / 4;
        $vitamineC = 90;
        $fibres = 50;
        $wather = 2;

        $dailyStats = new DailyStats;

        $dailyStats
        ->setCalories($calories)
        ->setProteins($proteines)
        ->setGlucides($glucides)
        ->setLipides($lipides)
        ->setVitamineC($vitamineC)
        ->setFibres($fibres)
        ->setEau($wather)
        ->setUserDailyStats($userData);

        $em->persist($dailyStats);
        $em->flush();

        // Stocker les résultats dans la session
        $session = $request->getSession();
        $session->set('resultats', [
            'calories' => $calories,
            'proteines' => $proteines,
            'lipides' => $lipides,
            'glucides' => $glucides,
            'vitamineC' => $vitamineC,
            'fibres' => $fibres,
            'wather' => $wather,
        ]);

        // Rediriger vers la page des résultats
        return $this->redirectToRoute('app_resultat');
    }

    return $this->render('profil/create.html.twig', [
        'form' => $form->createView(),
    ]);
}

    private function calculBMR($poids, $taille, $age, $sexe)
    {
        // Formule de Mifflin-St Jeor
        if ($sexe === 'homme') {
            return (10 * $poids) + (6.25 * $taille) - (5 * $age) + 5;
        } else {
            return (10 * $poids) + (6.25 * $taille) - (5 * $age) - 161;
        }
    }

    private function calculTDEE($bmr)
    {
        // Niveau d'activité physique moyen
        $niveauActivite = 1.55;
        return $bmr * $niveauActivite;
    }
    #[Route('/resultat', name: 'app_resultat')]
public function resultat(Request $request): Response
{
    $session = $request->getSession();
    $resultats = $session->get('resultats');

    if (!$resultats) {
        // Si aucun résultat n'est trouvé, rediriger vers le formulaire
        return $this->redirectToRoute('app_calcul');
    }

    return $this->render('profil/resultat.html.twig', $resultats);
}
#[Route('/add-food', name: 'app_add_food', methods: ['GET', 'POST'])]
public function addFood(Request $request, EntityManagerInterface $em, HttpClientInterface $httpClient): Response
{
    if ($request->isMethod('POST')) {
        // Vérifier si c'est un ajout manuel
        if ($request->request->has('manual_add')) {
            // Récupérer les données du formulaire
            $foodName = $request->request->get('manual_food_name');
            $foodQuantity = $request->request->get('manual_food_quantity');
            $caloriesPer100g = $request->request->get('manual_calories');
            $proteinsPer100g = $request->request->get('manual_proteins');
            $carbsPer100g = $request->request->get('manual_carbs');
            $fatsPer100g = $request->request->get('manual_fats');

            $quantityFactor = $foodQuantity / 100;

            // Créer et sauvegarder l'aliment consommé
            $foodConsumed = new FoodConsumed();
            $foodConsumed->setUser($this->getUser());
            $foodConsumed->setName($foodName);
            $foodConsumed->setQuantity($foodQuantity);
            $foodConsumed->setCalories($caloriesPer100g * $quantityFactor);
            $foodConsumed->setProteins($proteinsPer100g * $quantityFactor);
            $foodConsumed->setCarbs($carbsPer100g * $quantityFactor);
            $foodConsumed->setFats($fatsPer100g * $quantityFactor);
            $foodConsumed->setConsumedAt(new \DateTime());

            $em->persist($foodConsumed);
            $em->flush();

            return new JsonResponse(['success' => true]);
        }

        // Si on reçoit food_id, c'est qu'un aliment a été sélectionné via OpenFoodFacts
        if ($request->request->has('food_id')) {
            $foodId = $request->request->get('food_id');
            $foodQuantity = $request->request->get('food_quantity');

            try {
                $response = $httpClient->request('GET', "https://fr.openfoodfacts.org/api/v0/product/{$foodId}.json");
                $product = $response->toArray()['product'];

                // Le reste du code pour sauvegarder l'aliment
                $nutriments = $product['nutriments'];
                $quantityFactor = $foodQuantity / 100;

                $foodConsumed = new FoodConsumed();
                $foodConsumed->setUser($this->getUser());
                $foodConsumed->setName($product['product_name']);
                $foodConsumed->setQuantity($foodQuantity);
                $foodConsumed->setCalories(($nutriments['energy-kcal_100g'] ?? 0) * $quantityFactor);
                $foodConsumed->setProteins(($nutriments['proteins_100g'] ?? 0) * $quantityFactor);
                $foodConsumed->setCarbs(($nutriments['carbohydrates_100g'] ?? 0) * $quantityFactor);
                $foodConsumed->setFats(($nutriments['fat_100g'] ?? 0) * $quantityFactor);
                $foodConsumed->setConsumedAt(new \DateTime());

                $em->persist($foodConsumed);
                $em->flush();

                return new JsonResponse(['success' => true]);
            } catch (TransportExceptionInterface $e) {
                return new JsonResponse(['error' => 'Erreur lors de la récupération du produit'], 400);
            }
        }

        // Sinon, c'est une recherche via OpenFoodFacts
        $foodName = $request->request->get('food_name');

        try {
            $response = $httpClient->request('GET', 'https://fr.openfoodfacts.org/cgi/search.pl', [
                'query' => [
                    'search_terms' => $foodName,
                    'search_simple' => 1,
                    'action' => 'process',
                    'json' => 1,
                    'page_size' => 10, // Limite à 10 résultats
                ],
            ]);

            $data = $response->toArray();

            $results = array_map(function($product) {
                return [
                    'id' => $product['_id'],
                    'name' => $product['product_name'] ?? 'Nom inconnu',
                    'brand' => $product['brands'] ?? 'Marque inconnue',
                    'image' => $product['image_front_thumb_url'] ?? null,
                    'calories' => $product['nutriments']['energy-kcal_100g'] ?? 0,
                    'proteins' => $product['nutriments']['proteins_100g'] ?? 0,
                    'carbs' => $product['nutriments']['carbohydrates_100g'] ?? 0,
                    'fats' => $product['nutriments']['fat_100g'] ?? 0,
                ];
            }, array_slice($data['products'], 0, 10));

            return new JsonResponse($results);

        } catch (TransportExceptionInterface $e) {
            return new JsonResponse(['error' => 'Erreur de connexion'], 400);
        }
    }

    return $this->redirectToRoute('app_profil');
}


#[Route('/nutrition-data', name: 'app_nutrition_data', methods: ['GET'])]
public function getNutritionData(Request $request, EntityManagerInterface $em): Response
{
    $period = $request->query->get('period', 'week'); // Valeur par défaut : semaine
    $user = $this->getUser();

    // Définir la date de début en fonction de la période
    $now = new \DateTime();
    switch ($period) {
        case 'month':
            $startDate = (clone $now)->modify('first day of this month')->setTime(0, 0);
            break;
        case 'year':
            $startDate = (clone $now)->setDate($now->format('Y'), 1, 1)->setTime(0, 0);
            break;
        case 'week':
        default:
            $startDate = (clone $now)->modify('monday this week')->setTime(0, 0);
            break;
    }

    // Récupérer les aliments consommés par l'utilisateur pendant la période
    $foodConsumedRepo = $em->getRepository(FoodConsumed::class);
    $foodConsumed = $foodConsumedRepo->createQueryBuilder('f')
        ->where('f.user = :user')
        ->andWhere('f.consumedAt BETWEEN :start AND :end')
        ->setParameter('user', $user)
        ->setParameter('start', $startDate)
        ->setParameter('end', $now)
        ->getQuery()
        ->getResult();

    // Calculer les totaux
    $totals = [
        'calories' => 0,
        'proteins' => 0,
        'carbs' => 0,
        'fats' => 0,
    ];

    foreach ($foodConsumed as $food) {
        $totals['calories'] += $food->getCalories();
        $totals['proteins'] += $food->getProteins();
        $totals['carbs'] += $food->getCarbs();
        $totals['fats'] += $food->getFats();
    }

    // Préparer les données pour le graphique de tendance
    $trendData = $this->prepareTrendData($foodConsumed, $period);

    return new JsonResponse([
        'totals' => $totals,
        'trendData' => $trendData,
    ]);
}

// Fonction pour préparer les données de tendance
private function prepareTrendData($foodConsumed, $period)
{
    $trendData = [];
    $dateFormat = 'Y-m-d';

    // Initialiser les dates en fonction de la période
    $dates = [];
    $now = new \DateTime();
    switch ($period) {
        case 'month':
            $startDate = (clone $now)->modify('first day of this month')->setTime(0, 0);
            $interval = new \DateInterval('P1D');
            $periodRange = new \DatePeriod($startDate, $interval, $now->modify('+1 day'));
            foreach ($periodRange as $date) {
                $dates[$date->format($dateFormat)] = 0;
            }
            break;
        case 'year':
            $startDate = (clone $now)->setDate($now->format('Y'), 1, 1)->setTime(0, 0);
            $interval = new \DateInterval('P1M');
            $periodRange = new \DatePeriod($startDate, $interval, $now->modify('+1 month'));
            foreach ($periodRange as $date) {
                $dates[$date->format('Y-m')] = 0;
            }
            break;
        case 'week':
        default:
            $startDate = (clone $now)->modify('monday this week')->setTime(0, 0);
            $interval = new \DateInterval('P1D');
            $periodRange = new \DatePeriod($startDate, $interval, $now->modify('+1 day'));
            foreach ($periodRange as $date) {
                $dates[$date->format($dateFormat)] = 0;
            }
            break;
    }

    // Agréger les calories consommées par date
    foreach ($foodConsumed as $food) {
        $dateKey = $food->getConsumedAt()->format($period === 'year' ? 'Y-m' : $dateFormat);
        if (!isset($dates[$dateKey])) {
            $dates[$dateKey] = 0;
        }
        $dates[$dateKey] += $food->getCalories();
    }

    // Préparer les données pour le graphique
    foreach ($dates as $date => $calories) {
        $trendData[] = [
            'date' => $date,
            'calories' => $calories,
        ];
    }

    return $trendData;
}


}