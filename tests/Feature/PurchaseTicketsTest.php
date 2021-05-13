<?php

use App\Billing\FakePaymentGateway;
use App\Concert;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseTicketsTest extends TestCase {

    use DatabaseMigrations;

    /** @test */
    function customer_can_purchase_concert_tickets() {

        $paymentGateway = new FakePaymentGateway;

        $this->app->instance(\App\Billing\PaymentGateway::class, $paymentGateway);

        //arrange
        // create a concert
        $concert = factory(Concert::class)->create([
            'ticket_price' => 3250,
        ]);


        //act
        // purchase ticket
        $response = $this->json('POST', "/concerts/{$concert->id}/orders", [
            'email' => 'johndoe@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $paymentGateway->getValidTestToken(),
        ]);

        //assert
        $response->assertStatus(201);

        // make sure cust was charged
        $this->assertEquals(9750, $paymentGateway->totalCharges());

        // make sure order exists
        $order = $concert->orders()->where('email', 'johndoe@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals(3, $order->tickets()->count());

    }


}