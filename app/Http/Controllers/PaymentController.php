<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    

public function createPaymentIntent(Request $request)
{
    Stripe::setApiKey(config('services.stripe.secret'));

    $amount = $request->input('amount'); // Asegúrate de validar y calcular la cantidad en centavos

    $paymentIntent = PaymentIntent::create([
        'amount' => $amount,
        'currency' => 'usd',
    ]);

    return response()->json([
        'clientSecret' => $paymentIntent->client_secret,
    ]);
}

 }