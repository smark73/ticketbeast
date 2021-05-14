<?php

use App\Billing\FakePaymentGateway;
use App\Concert;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestResponse;

class PurchaseTicketsTest extends TestCase {

    use DatabaseMigrations;

    protected function setUp() {
        parent::setUp();

        $this->paymentGateway = new FakePaymentGateway;
        $this->app->instance(\App\Billing\PaymentGateway::class, $this->paymentGateway);
    }

    public function orderTickets($concert, $params) {
        return $this->json('POST', "/concerts/{$concert->id}/orders", $params);
    }

    public function assertValidationError($response, $field, $error) {
        $response
            ->assertStatus($error)
            ->assertJsonValidationErrors([$field]);
    }

    /** @test */
    function customer_can_purchase_published_concert_tickets() {

//        $paymentGateway = new FakePaymentGateway;
//        $this->app->instance(\App\Billing\PaymentGateway::class, $paymentGateway);

        //arrange
        // create a concert
        $concert = factory(Concert::class)->states('published')->create([
            'ticket_price' => 3250,
        ]);


        //act
        // purchase ticket
//        $response = $this->json('POST', "/concerts/{$concert->id}/orders", [
//            'email' => 'johndoe@example.com',
//            'ticket_quantity' => 3,
//            'payment_token' => $this->paymentGateway->getValidTestToken(),
//        ]);
        $response = $this->orderTickets($concert, [
            'email' => 'johndoe@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        //assert
        $response->assertStatus(201);

        // make sure cust was charged
        $this->assertEquals(9750, $this->paymentGateway->totalCharges());

        // make sure order exists
        $order = $concert->orders()->where('email', 'johndoe@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals(3, $order->tickets()->count());

    }

    /** @test */
    function cannot_purchase_tickets_to_unpublished_concert() {

//        $this->disableExceptionHandling();

        $concert = factory(Concert::class)->states('unpublished')->create([]);

        $response = $this->orderTickets($concert, [
            'email' => 'johndoe@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $response->assertStatus(404);
        $this->assertEquals(0, $concert->orders()->count());
        $this->assertEquals(0, $this->paymentGateway->totalCharges());

    }

    /** @test */
    function an_order_is_not_created_if_payment_fails() {

        $this->disableExceptionHandling();

        $concert = factory(Concert::class)->states('published')->create([]);

        $response = $this->orderTickets($concert, [
            'email' => 'johndoe@example.com',
            'ticket_quantity' => 3,
            'payment_token' => 'invalid-payment-token',
        ]);

        $response->assertStatus(422);
        $order = $concert->orders()->where('email', 'johndoe@example.com')->first();
        $this->assertNull($order);

    }


    /** @test */
    function email_is_required_to_purchase_tickets() {

//        $paymentGateway = new FakePaymentGateway;
//        $this->app->instance(\App\Billing\PaymentGateway::class, $paymentGateway);

        $concert = factory(Concert::class)->states('published')->create([]);

//        $response = $this->json('POST', "/concerts/{$concert->id}/orders", [
//            'ticket_quantity' => 3,
//            'payment_token' => $this->paymentGateway->getValidTestToken(),
//        ]);

        $response = $this->orderTickets($concert, [
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

//        $response
//            ->assertStatus(422)
//            ->assertJsonValidationErrors(['email']);

        $this->assertValidationError($response, 'email', 422);

    }

    /** @test */
    function valid_email_is_required_to_purchase_tickets() {

        $concert = factory(Concert::class)->states('published')->create([]);

//        $response = $this->json('POST', "/concerts/{$concert->id}/orders", [
//            'email' => 'not-a-valid-email',
//            'ticket_quantity' => 3,
//            'payment_token' => $this->paymentGateway->getValidTestToken(),
//        ]);

        $response = $this->orderTickets($concert, [
            'email' => 'not-a-valid-email',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

//        $response
//            ->assertStatus(422)
//            ->assertJsonValidationErrors(['email']);

        $this->assertValidationError($response, 'email', 422);

    }

    /** @test */
    function ticket_quantity_is_required_to_purchase_tickets() {

        $concert = factory(Concert::class)->states('published')->create([]);

//        $response = $this->json('POST', "/concerts/{$concert->id}/orders", [
//            'email' => 'johndoe@example.com',
//            'payment_token' => $this->paymentGateway->getValidTestToken(),
//        ]);

        $response = $this->orderTickets($concert, [
            'email' => 'johndoe@example.com',
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

//        $response
//            ->assertStatus(422)
//            ->assertJsonValidationErrors(['ticket_quantity']);

        $this->assertValidationError($response, 'ticket_quantity', 422);

    }

    /** @test */
    function ticket_quantity_must_be_at_least_1_to_purchase_tickets() {

        $concert = factory(Concert::class)->states('published')->create([]);

//        $response = $this->json('POST', "/concerts/{$concert->id}/orders", [
//            'email' => 'johndoe@example.com',
//            'ticket_quantity' => 0,
//            'payment_token' => $this->paymentGateway->getValidTestToken(),
//        ]);

        $response = $this->orderTickets($concert, [
            'email' => 'johndoe@example.com',
            'ticket_quantity' => 0,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

//        $response
//            ->assertStatus(422)
//            ->assertJsonValidationErrors(['ticket_quantity']);

        $this->assertValidationError($response, 'ticket_quantity', 422);

    }

    /** @test */
    function payment_token_is_required() {

        $concert = factory(Concert::class)->states('published')->create([]);

//        $response = $this->json('POST', "/concerts/{$concert->id}/orders", [
//            'email' => 'johndoe@example.com',
//            'ticket_quantity' => 3,
//        ]);

        $response = $this->orderTickets($concert, [
            'email' => 'johndoe@example.com',
            'ticket_quantity' => 3,
        ]);

//        $response
//            ->assertStatus(422)
//            ->assertJsonValidationErrors(['payment_token']);

        $this->assertValidationError($response, 'payment_token', 422);

    }

}