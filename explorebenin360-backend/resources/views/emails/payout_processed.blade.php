<!doctype html>
<html>
  <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px;">
      <h1 style="color: #2c5aa0;">{{ config('app.name') }}</h1>
      
      @if($payout->status === 'completed')
        <h2 style="color: #28a745;">{{ __('Votre retrait a été effectué') }}</h2>
        
        <p>{{ __('Bonjour') }} {{ $payout->provider->name }},</p>
        
        <p>{{ __('Votre demande de retrait a été traitée avec succès.') }}</p>
        
        <div style="background: #d4edda; padding: 15px; border-left: 4px solid #28a745; margin: 20px 0;">
          <ul style="list-style: none; padding: 0;">
            <li><strong>{{ __('Montant') }}:</strong> {{ number_format($payout->amount, 2) }} {{ $payout->currency }}</li>
            <li><strong>{{ __('Référence') }}:</strong> {{ $payout->reference }}</li>
            <li><strong>{{ __('Méthode') }}:</strong> {{ $payout->paymentMethod->type }} - {{ $payout->paymentMethod->account_number_masked }}</li>
            <li><strong>{{ __('Date') }}:</strong> {{ $payout->completed_at->format('d/m/Y H:i') }}</li>
          </ul>
        </div>
        
        <p>{{ __('Les fonds devraient apparaître sur votre compte sous 1-3 jours ouvrables.') }}</p>
        
      @elseif($payout->status === 'failed')
        <h2 style="color: #dc3545;">{{ __('Votre retrait a échoué') }}</h2>
        
        <p>{{ __('Bonjour') }} {{ $payout->provider->name }},</p>
        
        <p>{{ __('Votre demande de retrait n\'a pas pu être traitée.') }}</p>
        
        <div style="background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545; margin: 20px 0;">
          <p><strong>{{ __('Montant') }}:</strong> {{ number_format($payout->amount, 2) }} {{ $payout->currency }}</p>
          <p><strong>{{ __('Référence') }}:</strong> {{ $payout->reference }}</p>
          @if($payout->failure_reason)
            <p style="margin-top: 10px;"><strong>{{ __('Raison') }}:</strong> {{ $payout->failure_reason }}</p>
          @endif
        </div>
        
        <p>{{ __('Veuillez vérifier vos coordonnées de paiement et réessayer.') }}</p>
        
        <p style="margin-top: 20px;">
          <a href="{{ config('app.frontend_url') }}/provider/payment-methods" 
             style="display: inline-block; background: #2c5aa0; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            {{ __('Gérer mes méthodes de paiement') }}
          </a>
        </p>
      @endif
      
      <p style="color: #666; font-size: 14px; margin-top: 30px;">
        {{ __('Merci d\'utiliser') }} {{ config('app.name') }}!
      </p>
    </div>
  </body>
</html>
