# Devis & Facturation

Application de gestion de devis et factures développée avec Laravel, pensée pour une PME ou un artisan indépendant.

## Cas d'usage

Un artisan ou une petite entreprise a besoin de créer des devis, les convertir en factures une fois acceptés, suivre les paiements reçus, et éditer des documents PDF conformes.

## Fonctionnalités

- Gestion des clients (CRUD complet)
- Création de devis avec lignes multiples et calcul automatique HT/TVA/TTC
- Conversion devis → facture en un clic, avec numérotation légale séquentielle sans trou
- Suivi des statuts (brouillon, envoyé, accepté, payé...)
- Enregistrement des paiements (partiels ou complets), passage automatique en "payé"
- Génération PDF des devis et factures
- Interface responsive (Tailwind CSS)

## Stack technique

- Laravel 13
- Blade + Tailwind CSS
- SQLite
- DomPDF pour la génération de documents

## Installation

\`\`\`bash
git clone <url-du-repo>
cd devis-facturation
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan db:seed --class=DemoSeeder
npm run build
php artisan serve
\`\`\`

## Captures d'écran

### Liste des documents
![Liste des documents](docs/liste-documents.png)

### Détail d'une facture avec paiements
![Détail facture](docs/detail-facture.png)

### Facture PDF générée
![PDF facture](docs/pdf-facture.png)

## Limites connues / pistes d'évolution

- Pas d'authentification multi-utilisateurs (prototype mono-utilisateur)
- Pas de gestion des avoirs (annulation de facture émise)
- Pas d'export comptable
