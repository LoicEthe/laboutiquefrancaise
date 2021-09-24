<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Carrier;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/commande/merci/{stripeSessionId}', name: 'order_success')]
    public function index($stripeSessionId,Cart $cart): Response
    {

        $order = $this->entityManager->getRepository(Order::class)->findOneBy(array('stripeSessionId'=>$stripeSessionId));

        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('home');
        }

        // Modifier le statut d'une commande
        if(!$order->getIsPaid()){
            $cart->remove();

            $order->setIsPaid(1);
            $this->entityManager->flush();

            // envoi du mail confirmation de commande
            $mail = new Mail();
            $content = "Bonjour ".$order->getUser()->getFirstname()."<br/>Merci pour votre de commande";
            $mail->send($order->getUser()->getEmail(),$order->getUser()->getFirstname(),'Votre commande sur la boutique française est bien validée',$content);
        }

        return $this->render('order_success/index.html.twig',[
            'order' => $order
        ]);
    }
}
