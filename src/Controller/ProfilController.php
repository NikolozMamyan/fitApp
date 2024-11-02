<?php

namespace App\Controller;

use App\Entity\Stats;
use App\Entity\UserData;
use App\Entity\DailyStats;
use App\Form\UserDataType;
use App\Form\StatsUserType;
use App\Repository\UserDataRepository;
use App\Repository\DailyStatsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(DailyStatsRepository $dailyStatsRepo, UserDataRepository $userDataRepo): Response
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
            // Gérer le cas où il n'y a pas de données
            $dailyStatsArray = null;
        } else {
              // Supposons que vous voulez utiliser le premier élément du tableau
        $caloriesGoal = $dailyStatsArray[0]['calories'];
        $caloriesConsumed = 1500; // Remplacez par la valeur réelle
        $caloriesPercentage = ($caloriesConsumed / $caloriesGoal) * 100;
    
        $proteinsGoal = $dailyStatsArray[0]['proteins'];
        $proteinsConsumed = 90; // Remplacez par la valeur réelle
        $proteinsPercentage = ($proteinsConsumed / $proteinsGoal) * 100;
    
        $carbsGoal = $dailyStatsArray[0]['glucides'];
        $carbsConsumed = 200; // Remplacez par la valeur réelle
        $carbsPercentage = ($carbsConsumed / $carbsGoal) * 100;
    
        $fibersGoal = $dailyStatsArray[0]['fibres'];
        $fibersConsumed = 30; // Remplacez par la valeur réelle
        $fibersPercentage = ($fibersConsumed / $fibersGoal) * 100;
    
        $waterGoal = $dailyStatsArray[0]['eau'];
        $waterConsumed = 1500; // Remplacez par la valeur réelle en ml
        $waterPercentage = ($waterConsumed / $waterGoal) * 0.10;
    
        $lipidsGoal = $dailyStatsArray[0]['lipides'];
        $lipidsConsumed = 70; // Remplacez par la valeur réelle
        $lipidsPercentage = ($lipidsConsumed / $lipidsGoal) * 100;
        
      

    
    
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
            'fibers_percentage' => round($fibersPercentage),
            'water_goal' => $waterGoal,
            'water_consumed' => $waterConsumed,
            'water_percentage' => round($waterPercentage),
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

}
