<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ActivationController extends AbstractController
{
    #[Route('/activate', name: 'app_activate_account')]
    public function activateAccount(Request $request, EntityManagerInterface $em, UserRepository $userRepository): Response
    {
        $token = $request->query->get('token');
    
        // Vérifier si le token existe
        $user = $userRepository->findOneBy(['activationToken' => $token]);
    
        if (!$user) {
            throw $this->createNotFoundException("Token d'activation invalide.");
        }
    
        // Activer le compte de l'utilisateur
        $user->setIsVerified(true);
        $user->setActivationToken(null);
    
        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'Votre compte a été activé avec succès.');
    
        // Rediriger vers une page de confirmation
        return $this->redirectToRoute('app_login');
    }
}
