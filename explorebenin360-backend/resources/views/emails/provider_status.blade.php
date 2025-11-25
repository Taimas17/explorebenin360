<!doctype html>
<html>
  <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px;">
      <h1 style="color: #2c5aa0;">{{ config('app.name') }}</h1>
      
      @if($status === 'approved')
        <h2 style="color: #28a745;">{{ __('Félicitations ! Votre compte provider a été approuvé') }}</h2>
        
        <p>{{ __('Bonjour') }} {{ $user->name }},</p>
        
        <p>{{ __('Nous avons le plaisir de vous informer que votre demande de compte provider a été approuvée.') }}</p>
        
        <p>{{ __('Vous pouvez maintenant créer et publier vos offres sur ExploreBenin360.') }}</p>
        
        <p style="margin-top: 20px;">
          <a href="{{ config('app.frontend_url') }}/provider" 
             style="display: inline-block; background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            {{ __('Accéder à mon espace provider') }}
          </a>
        </p>
      @else
        <h2 style="color: #dc3545;">{{ __('Votre demande provider a été rejetée') }}</h2>
        
        <p>{{ __('Bonjour') }} {{ $user->name }},</p>
        
        <p>{{ __('Nous sommes désolés de vous informer que votre demande de compte provider a été rejetée.') }}</p>
        
        @if($reason)
          <div style="background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0;">
            <strong>{{ __('Raison') }}:</strong> {{ $reason }}
          </div>
        @endif
        
        <p>{{ __('Vous pouvez soumettre une nouvelle demande après avoir corrigé les points mentionnés.') }}</p>
        
        <p style="margin-top: 20px;">
          <a href="{{ config('app.frontend_url') }}/become-provider" 
             style="display: inline-block; background: #2c5aa0; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            {{ __('Soumettre une nouvelle demande') }}
          </a>
        </p>
      @endif
      
      <p style="color: #666; font-size: 14px; margin-top: 30px;">
        {{ __('Merci d\'utiliser') }} {{ config('app.name') }}!
      </p>
    </div>
  </body>
</html>
