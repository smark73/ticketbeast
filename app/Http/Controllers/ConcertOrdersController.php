<?php

namespace App\Http\Controllers;

use App\Billing\PaymentGateway;
use App\Concert;
use Illuminate\Http\Request;

class ConcertOrdersController extends Controller
{

    private $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway) {
        $this->paymentGateway = $paymentGateway;
    }

    public function store($concertId) {

        $concert = Concert::find($concertId);

//        $ticketQuantity = request('ticket_quantity');
//        $amount = $ticketQuantity * $concert->ticket_price;
//        $token = request('payment_token');
//        $this->paymentGateway->charge($amount, $token);
        // logic reduced to 1 line
        $this->paymentGateway->charge(request('ticket_quantity') * $concert->ticket_price, request('payment_token'));

        $order = $concert->orders()->create(['email'=>'johndoe@example.com']);

        foreach (range(1, request('ticket_quantity')) as $i){
            $order->tickets()->create([]);
        }

        return response()->json([],201);

    }
}
