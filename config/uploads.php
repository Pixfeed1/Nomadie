<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Limites de Taille de Fichiers
    |--------------------------------------------------------------------------
    |
    | Tailles maximales en kilo-octets (KB) pour différents types d'uploads.
    |
    */
    'max_sizes' => [
        'image' => 5120,        // 5 MB pour les images
        'attachment' => 5120,   // 5 MB pour les pièces jointes
        'avatar' => 2048,       // 2 MB pour les avatars
        'document' => 10240,    // 10 MB pour les documents
    ],

    /*
    |--------------------------------------------------------------------------
    | Types MIME Autorisés
    |--------------------------------------------------------------------------
    */
    'allowed_mimes' => [
        'images' => [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/webp',
        ],

        'attachments' => [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/jpg',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/zip',
            'application/x-zip-compressed',
        ],

        'documents' => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Extensions de Fichiers Autorisées
    |--------------------------------------------------------------------------
    */
    'allowed_extensions' => [
        'images' => ['jpg', 'jpeg', 'png', 'webp'],
        'attachments' => ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'zip'],
        'documents' => ['pdf', 'doc', 'docx'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Limites de Quantité
    |--------------------------------------------------------------------------
    */
    'limits' => [
        'max_trip_images' => 20,
        'min_trip_images' => 5,
        'max_article_images' => 10,
    ],
];
