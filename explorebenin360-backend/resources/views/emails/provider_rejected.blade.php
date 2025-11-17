<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1 style="color: #dc2626;">Demande provider</h1>
    
    <p>Bonjour {{ $provider->name }},</p>
    
    <p>Nous avons examiné votre demande pour devenir prestataire sur ExploreBenin360.</p>
    
    <p>Malheureusement, nous ne pouvons pas approuver votre demande pour la raison suivante :</p>
    
    <div style="background: #fee2e2; border-left: 4px solid #dc2626; padding: 12px; margin: 20px 0;">
        <strong>Raison :</strong> {{ $reason }}
    </div>
    
    <p>Vous pouvez soumettre une nouvelle demande en corrigeant les points mentionnés.</p>
    
    <p style="margin: 30px 0;">
        <a href="{{ config('app.frontend_url') }}/become-provider" 
           style="background: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
            Soumettre une nouvelle demande
        </a>
    </p>
    
    <p>Si vous avez des questions, n'hésitez pas à nous contacter.</p>
    
    <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">
    <p style="font-size: 12px; color: #666;">
        ExploreBenin360 - Découvrez le Bénin autrement<br>
        <a href="{{ config('app.frontend_url') }}">{{ config('app.frontend_url') }}</a>
    </p>
</body>
</html>
