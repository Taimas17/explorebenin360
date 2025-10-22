<!doctype html>
<html>
  <body>
    <h1>{{ config('app.name') }}</h1>
    <p>{{ __('Bonjour') }} {{ $booking->user->name }},</p>
    <p>{{ __('Votre réservation est confirmée.') }}</p>
    <ul>
      <li>{{ __('Offre') }}: {{ $booking->offering->title }}</li>
      <li>{{ __('Dates') }}: {{ $booking->start_date }} @if($booking->end_date) - {{ $booking->end_date }} @endif</li>
      <li>{{ __('Invités') }}: {{ $booking->guests }}</li>
      <li>{{ __('Montant') }}: {{ number_format($booking->amount,2) }} {{ $booking->currency }}</li>
    </ul>
    <p>{{ __('Merci d\'avoir choisi ExploreBenin360!') }}</p>
  </body>
</html>
