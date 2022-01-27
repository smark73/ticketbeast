<?php

use App\Billing\FakePaymentGateway;
use App\Concert;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestResponse;

class PurchaseTicketsTest extends TestCase {

    use DatabaseMigrations;

    /**
     * @var FakePaymentGateway
     */
    private $paymentGateway;

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
    public function customer_can_purchase_published_concert_tickets() {
        //arrange
        // create a concert
        $concert = factory(Concert::class)->states('published')->create(['ticket_price' => 3250,])->addTickets(3);

        //act
        // purchase ticket
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
        $this->assertTrue($concert->hasOrderFor('johndoe@example.com'));
        $this->assertEquals(3, $concert->ordersFor('johndoe@example.com')->first()->ticketQuantity());
    }

    /** @test */
    public function cannot_purchase_tickets_to_unpublished_concert() {
        $concert = factory(Concert::class)->states('unpublished')->create()->addTickets(3);

        $response = $this->orderTickets($concert, [
            'email' => 'johndoe@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $response->assertStatus(404);
        $this->assertFalse($concert->hasOrderFor('johndoe@example.com'));
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
    }

    /** @test */
    public function cannot_purchase_more_tickets_than_remain() {
        $concert = factory(Concert::class)->states('published')->create()->addTickets(50);

        $response = $this->orderTickets($concert, [
            'email' => 'johndoe@example.com',
            'ticket_quantity' => 51,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $response->assertStatus(422);
        $this->assertFalse($concert->hasOrderFor('johndoe@example.com'));
        $this->assertEquals(0, $this->paymentGateway->totalCharges());
        $this->assertEquals(50, $concert->ticketsRemaining());
    }


    /** @test */
    public function an_order_is_not_created_if_payment_fails() {
//        $this->disableExceptionHandling();

        $concert = factory(Concert::class)->states('published')->create()->addTickets(3);

        $response = $this->orderTickets($concert, [
            'email' => 'johndoe@example.com',
            'ticket_quantity' => 3,
            'payment_token' => 'invalid-payment-token',
        ]);

        $response->assertStatus(422);
        $this->assertFalse($concert->hasOrderFor('johndoe@example.com'));
    }


    /** @test */
    public function email_is_required_to_purchase_tickets() {
        $concert = factory(Concert::class)->states('published')->create()->addTickets(3);

        $response = $this->orderTickets($concert, [
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError($response, 'email', 422);
    }

    /** @test */
    public function valid_email_is_required_to_purchase_tickets() {
        $concert = factory(Concert::class)->states('published')->create([])->addTickets(3);

        $response = $this->orderTickets($concert, [
            'email' => 'not-a-valid-email',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError($response, 'email', 422);
    }

    /** @test */
    public function ticket_quantity_is_required_to_purchase_tickets() {
        $concert = factory(Concert::class)->states('published')->create([]);

        $response = $this->orderTickets($concert, [
            'email' => 'johndoe@example.com',
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError($response, 'ticket_quantity', 422);
    }

    /** @test */
    public function ticket_quantity_must_be_at_least_1_to_purchase_tickets() {
        $concert = factory(Concert::class)->states('published')->create([]);

        $response = $this->orderTickets($concert, [
            'email' => 'johndoe@example.com',
            'ticket_quantity' => 0,
            'payment_token' => $this->paymentGateway->getValidTestToken(),
        ]);

        $this->assertValidationError($response, 'ticket_quantity', 422);
    }

    /** @test */
    public function payment_token_is_required() {
        $concert = factory(Concert::class)->states('published')->create([])->addTickets(3);

        $response = $this->orderTickets($concert, [
            'email' => 'johndoe@example.com',
            'ticket_quantity' => 3,
        ]);

        $this->assertValidationError($response, 'payment_token', 422);
    }

}