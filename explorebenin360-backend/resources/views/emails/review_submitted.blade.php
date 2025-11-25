<!doctype html>
<html>
  <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px;">
      <h1 style="color: #2c5aa0;">{{ config('app.name') }}</h1>
      
      <h2 style="color: #444;">{{ __('Nouvel avis reçu') }}</h2>
      
      <p>{{ __('Bonjour') }} {{ $provider->name }},</p>
      
      <p>{{ __('Un client a laissé un avis pour votre offre.') }}</p>
      
      <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
        <p><strong>{{ __('Note') }}:</strong> {{ str_repeat('⭐', $review->rating) }} ({{ $review->rating }}/5)</p>
        @if($review->comment)
          <p style="margin-top: 10px;"><strong>{{ __('Commentaire') }}:</strong></p>
          <p style="white-space: pre-wrap;">{{ $review->comment }}</p>
        @endif
        <p style="margin-top: 10px;"><strong>{{ __('Statut') }}:</strong> {{ __('En attente de modération') }}</p>
      </div>
      
      <p>{{ __('L\'avis sera visible publiquement après validation par notre équipe.') }}</p>
      
      <p style="color: #666; font-size: 14px; margin-top: 30px;">
        {{ __('Merci d\'utiliser') }} {{ config('app.name') }}!
      </p>
    </div>
  </body>
</html>
