<!doctype html>
<html>
  <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px;">
      <h1 style="color: #2c5aa0;">{{ config('app.name') }}</h1>
      
      <h2 style="color: #dc3545;">{{ __('Votre compte a été suspendu') }}</h2>
      
      <p>{{ __('Bonjour') }} {{ $user->name }},</p>
      
      <p>{{ __('Votre compte ExploreBenin360 a été temporairement suspendu.') }}</p>
      
      @if($reason)
        <div style="background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0;">
          <strong>{{ __('Raison') }}:</strong> {{ $reason }}
        </div>
      @endif
      
      <p>{{ __('Pendant cette période, vous ne pourrez pas accéder aux fonctionnalités de votre compte.') }}</p>
      
      <p>{{ __('Si vous pensez qu\'il s\'agit d\'une erreur ou si vous souhaitez plus d\'informations, veuillez contacter notre support.') }}</p>
      
      <p style="margin-top: 20px;">
        <a href="{{ config('app.frontend_url') }}/contact" 
           style="display: inline-block; background: #2c5aa0; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
          {{ __('Contacter le support') }}
        </a>
      </p>
      
      <p style="color: #666; font-size: 14px; margin-top: 30px;">
        {{ config('app.name') }}
      </p>
    </div>
  </body>
</html>
