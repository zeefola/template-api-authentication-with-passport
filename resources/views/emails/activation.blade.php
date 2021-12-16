@extends('emails.layouts.app')

@section('content')
    <h2>Verify Your Email To Complete Your Registration</h2>
    <p><b>Hello, {{ ucwords($content['name']) }}</b></p>
    <p>Thank you for choosing {{ config('app.name') }}</p>
    <i class="info">Please confirm that {{ $content['email'] }} is your email address, by clicking on the button
        below
        or
        open this link
    </i>
    <p><a href="{{ $content['url'] }}">{{ $content['url'] }}</a></p>

    <br />
    <div class="button-link">
        <a class="btn-link" href="{{ $content['url'] }}">Confirm Account</a>
    </div>
@endsection
