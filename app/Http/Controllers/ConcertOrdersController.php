<?php

namespace App\Http\Controllers;

use App\Billing\PaymentFailedException;
use App\Billing\PaymentGateway;
use App\Concert;
use Illuminate\Http\Request;

class ConcertOrdersController extends Controller {

    private $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway) {
        $this->paymentGateway = $paymentGateway;
    }

    public function store($concertId) {

        $concert = Concert::published()->findOrFail($concertId);

        $this->validate(request(), [
            'email' => ['required', 'email'],
            'ticket_quantity' => ['required', 'integer', 'min:1'],
            'payment_token' => ['required'],
        ]);

        try {

//            $concert = Concert::published()->findOrFail($concertId);

            // Charge customer
            // 1
            // $ticketQuantity = request('ticket_quantity');
            // $amount = $ticketQuantity * $concert->ticket_price;
            // $token = request('payment_token');
            // $this->paymentGateway->charge($amount, $token);

            // 2
            $this->paymentGateway->charge(request('ticket_quantity') * $concert->ticket_price, request('payment_token'));

            // Create order

            // 1
            // $order = $concert->orders()->create(['email'=>'johndoe@example.com']);
            //
            // foreach (range(1, request('ticket_quantity')) as $i){
            //     $order->tickets()->create([]);
            // }

            // 2
            // $order = $concert->orderTickets($email, $ticketQuantity);

            // 3
            $order = $concert->orderTickets(request('email'), request('ticket_quantity'));

            return response()->json([], 201);

        } catch (PaymentFailedException $e) {

            return response()->json([], 422);

        }

    }
}
