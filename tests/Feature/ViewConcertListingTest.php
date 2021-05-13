<?php

use App\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewConcertListingTest extends TestCase {

    use DatabaseMigrations;

    /**
     * @test
     */
    public function user_can_view_a_published_concert_listing() {

        $concert = factory(Concert::class)->states('published')->create([
            'title' => 'Type O Negative',
            'subtitle' => 'with Pantera',
            'date' => Carbon::parse('Oct 31, 2021, 10pm'),
            'ticket_price' => 4200,
            'venue' => 'Greenwood Cemetery',
            'venue_address' => '500 25th St',
            'city' => 'Brooklyn',
            'state' => 'NY',
            'zip' => '11232',
            'additional_information' => 'Lorem ipsum dolor sit amet',
        ]);

        $response = $this->get('/concerts/' . $concert->id);

        $response->assertStatus(200);

        $response->assertSee('Type O Negative');
        $response->assertSee('with Pantera');
        $response->assertSee('October 31, 2021');
        $response->assertSee('10:00pm');
        $response->assertSee('42.00');
        $response->assertSee('Greenwood Cemetery');
        $response->assertSee('500 25th St');
        $response->assertSee('Brooklyn');
        $response->assertSee('NY');
        $response->assertSee('11232');
        $response->assertSee('Brooklyn');
        $response->assertSee('Lorem ipsum dolor sit amet');
    }

    /** @test */
    public function user_cannot_view_unpublished_concert_listings() {

        $concert = factory(Concert::class)->states('unpublished')->create();

        $response = $this->get('/concerts/' . $concert->id);

        $response->assertStatus(404);

    }
}
