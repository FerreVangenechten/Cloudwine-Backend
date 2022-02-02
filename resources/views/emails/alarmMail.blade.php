@component('mail::message')
    <h1>{{ $details['title'] }} <b>{{ $details['name'] }}</b></h1>
    <p>{{ $details['body'] }}</p>
    <h2>ALARM:</h2>
    <h3>{{ $details['alarm'] }}</h3>

@component('mail::button', ['url' => 'https://www.wijnbouwer.be/'])
    naar Wijnbouwer.be
@endcomponent

{{ config('app.name') }}
@endcomponent
