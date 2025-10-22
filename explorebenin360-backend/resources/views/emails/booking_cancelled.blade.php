<!doctype html>
<html>
  <body>
    <h1>{{ config('app.name') }}</h1>
    <p>{{ __('Bonjour') }} {{ $booking->user->name }},</p>
    <p>{{ __('Votre réservation a été annulée.') }}</p>
    <ul>
      <li>{{ __('Offre') }}: {{ $booking->offering->title }}</li>
      <li>{{ __('Raison') }}: {{ $booking->cancel_reason ?? '-' }}</li>
    </ul>
  </body>
</html>
