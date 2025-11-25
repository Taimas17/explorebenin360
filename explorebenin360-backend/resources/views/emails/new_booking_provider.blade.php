<!doctype html>
<html>
  <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px;">
      <h1 style="color: #2c5aa0;">{{ config('app.name') }}</h1>
      
      <h2 style="color: #444;">{{ __('Nouvelle réservation') }}</h2>
      
      <p>{{ __('Bonjour') }} {{ $booking->offering->provider->name }},</p>
      
      <p>{{ __('Vous avez reçu une nouvelle réservation pour votre offre.') }}</p>
      
      <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
        <h3 style="margin-top: 0; color: #2c5aa0;">{{ __('Détails de la réservation') }}</h3>
        <ul style="list-style: none; padding: 0;">
          <li><strong>{{ __('Client') }}:</strong> {{ $booking->user->name }}</li>
          <li><strong>{{ __('Email') }}:</strong> {{ $booking->user->email }}</li>
          <li><strong>{{ __('Offre') }}:</strong> {{ $booking->offering->title }}</li>
          <li><strong>{{ __('Dates') }}:</strong> {{ $booking->start_date }} @if($booking->end_date) - {{ $booking->end_date }} @endif</li>
          <li><strong>{{ __('Invités') }}:</strong> {{ $booking->guests }}</li>
          <li><strong>{{ __('Montant') }}:</strong> {{ number_format($booking->amount, 2) }} {{ $booking->currency }}</li>
          <li><strong>{{ __('Statut') }}:</strong> {{ $booking->status }}</li>
        </ul>
      </div>
      
      <p style="margin-top: 20px;">
        <a href="{{ config('app.frontend_url') }}/provider/reservations/{{ $booking->id }}" 
           style="display: inline-block; background: #2c5aa0; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
          {{ __('Voir la réservation') }}
        </a>
      </p>
      
      <p style="color: #666; font-size: 14px; margin-top: 30px;">
        {{ __('Merci d\'utiliser') }} {{ config('app.name') }}!
      </p>
    </div>
  </body>
</html>
