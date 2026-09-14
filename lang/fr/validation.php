<?php

return [
    'required' => 'Le champ :attribute est obligatoire.',
    'email' => 'Le champ :attribute doit être une adresse email valide.',
    'max' => [
        'string' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
        'numeric' => 'Le champ :attribute ne doit pas dépasser :max.',
    ],
    'min' => [
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
        'numeric' => 'Le champ :attribute doit être au moins :min.',
    ],
    'numeric' => 'Le champ :attribute doit être un nombre.',
    'in' => 'La valeur sélectionnée pour :attribute est invalide.',
    'exists' => 'La valeur sélectionnée pour :attribute est invalide.',
    'array' => 'Le champ :attribute doit être une liste.',
    'date' => 'Le champ :attribute doit être une date valide.',
    'string' => 'Le champ :attribute doit être une chaîne de caractères.',

    'attributes' => [
        'nom' => 'nom',
        'email' => 'email',
        'adresse' => 'adresse',
        'siret' => 'SIRET',
        'type' => 'type',
        'client_id' => 'client',
        'date_echeance' => 'date d\'échéance',
        'statut' => 'statut',
    ],
];
