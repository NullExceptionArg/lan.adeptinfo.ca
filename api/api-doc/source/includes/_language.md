# Langue

L'API du LAN de l'ADEPT peut renvoyer les réponses dans différentes langues.

Langue | Code de langue
--------- | -----------
Anglais | en
Français | fr
Défaut (Si aucune langue n'est spécifiée) | La langue par défaut peut être changée dans les variables d'environnement (.env).

### Changer de langue

Il suffit d'inclure le query param 'lang' avec le code de la langue dans n'importe laquelle de vos requêtes.

Exemple: 
`GET /api/lan/1?lang=fr`

