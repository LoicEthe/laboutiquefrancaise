<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;}

    /**
     * @route("/inscription", name="register")
     */
    public function index(Request $request,UserPasswordHasherInterface $encoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class,$user);

        $form->handleRequest($request);

        // ENREGISTREMENT DANS LA BASE DE DONNEE
        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();

            $password = $encoder->hashPassword($user,$user->getPassword()); // CRYPTAGE DU PASSWORD
            $user->setPassword($password); // REINJECTION DU PASSWORD AVEC LA VARIABLE CRYPTEE

            $this->entityManager->persist($user); // persist va figer la data dans la variable mais pour la création
            $this->entityManager->flush(); // flush va l'envoyer

        }

        return $this->render('register/index.html.twig',[
            'form'=> $form->createView()
        ]);
    }
}
