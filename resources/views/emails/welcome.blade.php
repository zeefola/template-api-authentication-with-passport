@extends('emails.layouts.app')

@section('content')

    <h2>Welcome to {{ config('app.name') }}</h2>
    <p><b>Hello, {{ ucwords($content['name']) }}</b></p>
    
    <p class="info"> </p>

    <br/>
    <br/>
    <p class="thanks">Thank you for choosing {{ config('app.name') }}</p>
    <div class="button-link">
        <a class="btn-link" href="{{ config('app.login') }}">Sign In</a>
    </div>

@endsection
