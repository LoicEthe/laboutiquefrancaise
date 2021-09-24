<?php

namespace App\Controller;

use App\Classe\Mail;
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

        $notification = null;
        $user = new User();
        $form = $this->createForm(RegisterType::class,$user);

        $form->handleRequest($request);

        // ENREGISTREMENT DANS LA BASE DE DONNEE
        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();


            $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());

            if(!$search_email){
                $password = $encoder->hashPassword($user,$user->getPassword()); // CRYPTAGE DU PASSWORD
                $user->setPassword($password); // REINJECTION DU PASSWORD AVEC LA VARIABLE CRYPTEE

                $this->entityManager->persist($user); // persist va figer la data dans la variable mais pour la création
                $this->entityManager->flush(); // flush va l'envoyer
                $notification = "Merci de vous être inscrit, vous pouvez vous connecter";

            }else{
                $notification = "L email existe deja";
            }


            // envoi du mail de confirmation d'inscription
            $mail = new Mail();
            $content = "Bonjour ".$user->getFirstname()."<br/>Bienvenue sur la premiere boutique fr";
            $mail->send($user->getEmail(),$user->getFirstname(),'Bienvenue sur la Boutique Française',$content);

        }

        return $this->render('register/index.html.twig',[
            'form'=> $form->createView(),
            'notification' => $notification
        ]);
    }
}
