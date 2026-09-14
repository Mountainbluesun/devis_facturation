<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Document;
use App\Services\DocumentNumberGenerator;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $generator = new DocumentNumberGenerator();

        // Client 1 — devis en attente
        $client1 = Client::create([
            'nom' => 'Menuiserie Dubois',
            'email' => 'contact@menuiserie-dubois.fr',
            'adresse' => '12 rue des Artisans, 69000 Lyon',
            'siret' => '12345678900012',
        ]);

        $devis1 = Document::create([
            'type' => 'devis',
            'numero' => $generator->generate('devis'),
            'client_id' => $client1->id,
            'statut' => 'envoye',
            'date_creation' => now()->subDays(5),
            'date_echeance' => now()->addDays(25),
        ]);
        $devis1->lines()->create(['designation' => 'Fabrication porte sur mesure', 'quantite' => 1, 'prix_unitaire' => 850, 'taux_tva' => 20]);
        $devis1->lines()->create(['designation' => 'Pose et finition', 'quantite' => 4, 'prix_unitaire' => 45, 'taux_tva' => 20]);

        // Client 2 — facture payée
        $client2 = Client::create([
            'nom' => 'Atelier Créatif SARL',
            'email' => 'compta@atelier-creatif.fr',
            'adresse' => '8 avenue des Frères Lumière, 69008 Lyon',
            'siret' => '98765432100019',
        ]);

        $facture2 = Document::create([
            'type' => 'facture',
            'numero' => $generator->generate('facture'),
            'client_id' => $client2->id,
            'statut' => 'brouillon',
            'date_creation' => now()->subDays(20),
            'date_echeance' => now()->subDays(10),
        ]);
        $facture2->lines()->create(['designation' => 'Prestation de conseil - 2 jours', 'quantite' => 2, 'prix_unitaire' => 600, 'taux_tva' => 20]);
        $facture2->enregistrerPaiement($facture2->total_ttc, now()->subDays(8)->format('Y-m-d'), 'virement');

        // Client 3 — facture partiellement payée
        $client3 = Client::create([
            'nom' => 'Boulangerie Martin',
            'email' => 'boulangerie.martin@gmail.com',
            'adresse' => '3 place du Marché, 69003 Lyon',
        ]);

        $facture3 = Document::create([
            'type' => 'facture',
            'numero' => $generator->generate('facture'),
            'client_id' => $client3->id,
            'statut' => 'brouillon',
            'date_creation' => now()->subDays(10),
            'date_echeance' => now()->addDays(20),
        ]);
        $facture3->lines()->create(['designation' => 'Réparation four professionnel', 'quantite' => 1, 'prix_unitaire' => 1200, 'taux_tva' => 20]);
        $facture3->enregistrerPaiement(600, now()->subDays(3)->format('Y-m-d'), 'cheque');
    }
}
