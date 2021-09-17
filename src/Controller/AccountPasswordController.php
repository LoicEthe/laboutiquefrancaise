<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountPasswordController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;}
    /**
     * @route("/compte/modifier-mon-mot-de-passe", name="account_password")
     */
    public function index(Request $request,UserPasswordHasherInterface $encoder): Response
    {
        $notification = null;
        $user = $this->getUser(); // on recup l'utilisateur connecté

        $form = $this->createForm(ChangePasswordType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $old_password = $form->get('old_password')->getData(); // On va recupérer dans le form le old password et on injecte dans une var

            if($encoder->isPasswordValid($user,$old_password)){ // comparaison des deux passwords
                 $new_password = $form->get('new_password')->getData();
                 $password = $encoder->hashPassword($user,$new_password);

                $user->setPassword($password);

                $this->entityManager->flush();
                $notification = 'Votre mot de passe a bien été mis à jour';
            } else{
                $notification = "Votre mot de passe n'est pas le bon";
            }
        }

        return $this->render('account/password.html.twig',[
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
