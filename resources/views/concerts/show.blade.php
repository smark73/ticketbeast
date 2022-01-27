@extends('layout')

@section('content')
    <concert
        :concert-title="'{{ $concert->title }}'"
        :concert-subtitle="'{{ $concert->subtitle }}'"
        :date="'{{ $concert->formatted_date }}'"
        :start-time="'{{ $concert->formatted_start_time }}'"
        :ticket-price="'{{ $concert->ticket_price_in_dollars }}'"
        :venue="'{{ $concert->venue }}'"
        :venue-address="'{{ $concert->venue_address }}'"
        :venue-city="'{{ $concert->city }}'"
        :venue-state="'{{ $concert->state }}'"
        :venue-zip="'{{ $concert->zip }}'"
        :additional-info="'{{ $concert->additional_information }}'"
    ></concert>
@endsection

{{--<h1>{{ $concert->title }}</h1>--}}
{{--<h2>{{ $concert->subtitle }}</h2>--}}
{{--<p>{{ $concert->formatted_date }}</p>--}}
{{--<p>Doors open at {{ $concert->formatted_start_time }}</p>--}}
{{--<p>{{ $concert->ticket_price_in_dollars }}</p>--}}
{{--<p>{{ $concert->venue }}</p>--}}
{{--<p>{{ $concert->venue_address }}</p>--}}
{{--<p>{{ $concert->city }}, {{ $concert->state }}, {{ $concert->zip }}</p>--}}
{{--<p>{{ $concert->additional_information }}</p>--}}