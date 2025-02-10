<?php

// src/Controller/PaymentController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    /**
     * @Route("/success", name="payment_success")
     */
    public function success()
    {
        return $this->render('payment/success.html.twig');
    }

    /**
     * @Route("/cancel", name="payment_cancel")
     */
    public function cancel()
    {
        return $this->render('payment/cancel.html.twig');
    }
}
