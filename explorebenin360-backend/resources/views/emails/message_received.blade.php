<!doctype html>
<html>
  <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px;">
      <h1 style="color: #2c5aa0;">{{ config('app.name') }}</h1>
      
      <h2 style="color: #444;">{{ __('Nouveau message') }}</h2>
      
      <p>{{ __('Bonjour') }},</p>
      
      <p>{{ __('Vous avez reçu un nouveau message concernant') }}: <strong>{{ $thread->subject }}</strong></p>
      
      <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
        <p><strong>{{ __('De') }}:</strong> {{ $message->sender->name }}</p>
        <p style="margin-top: 10px;"><strong>{{ __('Message') }}:</strong></p>
        <p style="white-space: pre-wrap;">{{ \Illuminate\Support\Str::limit($message->body, 200) }}</p>
      </div>
      
      <p style="margin-top: 20px;">
        <a href="{{ config('app.frontend_url') }}/dashboard/messages/{{ $thread->id }}" 
           style="display: inline-block; background: #2c5aa0; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
          {{ __('Répondre au message') }}
        </a>
      </p>
      
      <p style="color: #666; font-size: 14px; margin-top: 30px;">
        {{ __('Merci d\'utiliser') }} {{ config('app.name') }}!
      </p>
    </div>
  </body>
</html>
