<!-- {% raw %} -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
    <style>

        .banner {
            width: 100%;
            max-width: 100%;
        }

        p.small {
            color: darkgrey;
            font-size: 11px;
        }

        p.small a {
            color: darkgrey;
        }

        #content {
            padding: 30px 43px;
            color: white;
            text-align: center;
            margin: 0;
            background: #000000;
        }

        .green {
            color: #27cf5f
        }

        .btn-adept {
            background-color: #27cf5f; /* Green */
            border: 1px solid #21b151;
            color: white;
            padding: 15px 25px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border-radius: 5px;
        }

        .btn-adept:hover {
            background-color: #21b151; /* Green */
        }

    </style>
</head>
<body>
<img src="{{env('BANNER_URL')}}"
     id="header" class="banner">
<div id="content">
    <h2>Bonjour <span class="green">{{$name}}</span>!</h2>
    <p>
        Merci d'avoir créé votre compte pour le LAN de l'ADEPT !
    </p>
    <p>
        Il ne vous reste qu'une seule étape avant de pouvoir réserver votre place pour la prochaine édition du LAN!
        Vous n'avez qu'a cliquer sur le bouton ci-dessous afin de confirmer votre adresse courriel.
    </p>
    <a href="{{env('BASE_URL')}}/api/user/confirm/{{$code}}" class="btn-adept">Confirmer mon adresse</a>
    <p>
        Notez que vous ne recevrez aucun autre courriel de l'ADEPT Informatique tant que votre adresse ne sera pas
        confirmée.
    </p>
    <p class="small">
        Pourquoi ais-je reçu ce courriel ?
        Quelqu'un a utilisé votre adresse courriel sur le site web lan.adeptinfo.ca.
        Si ce n'est pas vous, ne vous inquietez pas. Vous ne recevrez aucun autre courriel.
        Si vous avez des questions, n'hésitez pas à nous contacter par courriel à <a href="mailto:contact@adeptinfo.ca">contact@adeptinfo.ca</a>
    </p>

</div>
<img src="{{env('FOOTER_URL')}}"
     id="footer" class="banner">
</body>
</html>
<!-- {% endraw %} -->
