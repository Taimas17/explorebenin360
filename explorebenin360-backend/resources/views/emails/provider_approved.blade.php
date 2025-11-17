<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1 style="color: #16a34a;">Félicitations !</h1>
    
    <p>Bonjour {{ $provider->name }},</p>
    
    <p>Nous avons le plaisir de vous informer que votre demande pour devenir prestataire sur <strong>ExploreBenin360</strong> a été approuvée.</p>
    
    <p>Vous pouvez maintenant :</p>
    <ul>
        <li>Créer vos offres (hébergements, expériences, services)</li>
        <li>Gérer votre calendrier de disponibilités</li>
        <li>Recevoir et gérer vos réservations</li>
        <li>Suivre vos revenus en temps réel</li>
    </ul>
    
    <p style="margin: 30px 0;">
        <a href="{{ config('app.frontend_url') }}/provider" 
           style="background: #16a34a; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
            Accéder à mon dashboard
        </a>
    </p>
    
    <p>Merci de faire partie de la communauté ExploreBenin360 !</p>
    
    <hr style="margin: 30px 0; border: none; border-top: 1px solid #ddd;">
    <p style="font-size: 12px; color: #666;">
        ExploreBenin360 - Découvrez le Bénin autrement<br>
        <a href="{{ config('app.frontend_url') }}">{{ config('app.frontend_url') }}</a>
    </p>
</body>
</html>
