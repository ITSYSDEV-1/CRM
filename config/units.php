<?php

return [
    'RMY' => [
        'name' => 'Ramayana Suites & Resort',
        'logo_path' => 'logo/rms-logo.png',
        'registration_form' => [
            'background_image' => 'images/RMY/300DPIcrm-tanpa-judul-2480-x-3508-piksel_eDbYxo1O.png',
            'title' => 'Registration Form - Rama Maya',
            'blade_template' => 'reservation.RMY.registrationpdf', // Template khusus RMY
        ],
        'domain' => 'ramamayanasuiteskuta.com',
    ],
    
    'KSV' => [
        'name' => 'Kuta Seaview Beach Resort',
        'logo_path' => 'logo/Kuta-Seaview_Main_Logo1.jpg',
        'registration_form' => [
            'background_image' => 'images/KSV/300DPIcrm-tanpa-judul-2480-x-3508-piksel_eDbYxo1O.png',
            'title' => 'Registration Form - Kuta Seaview',
            'blade_template' => 'reservation.KSV.registrationpdf', // Template khusus KSV
        ],
        'domain' => 'kutaseaviewhotel.com',
    ],
    
    'RCD' => [
        'name' => 'Ramayana Candidasa Beach Resort',
        'logo_path' => 'logo/rcd-logo.png',
        'registration_form' => [
            'background_image' => 'images/RCD/300DPIcrm-tanpa-judul-2480-x-3508-piksel_eDbYxo1O.png',
            'title' => 'Registration Form - Rama Candidasa',
            'blade_template' => 'reservation.RCD.registrationpdf', // Template khusus RCD
        ],
        'domain' => 'ramayanasuitescandidasa.com',
    ],
    
    'RRP' => [
        'name' => 'Rama Residence Padma',
        'logo_path' => 'logo/rrp-logo.png',
        'registration_form' => [
            'background_image' => 'images/RRP/300DPIcrm-tanpa-judul-2480-x-3508-piksel_eDbYxo1O.png',
            'title' => 'Registration Form - Rama Residence Padma',
            'blade_template' => 'reservation.RRP.registrationpdf', // Template khusus RRP
        ],
        'domain' => 'ramaresidencepadma.com',
    ],
    
    'PS' => [
        'name' => 'Pondok Sari',
        'logo_path' => 'logo/ps-logo.png',
        'registration_form' => [
            'background_image' => 'images/PS/300DPIcrm-tanpa-judul-2480-x-3508-piksel_eDbYxo1O.png',
            'title' => 'Registration Form - Puri Santrian',
            'blade_template' => 'reservation.PS.registrationpdf', // Template khusus PS
        ],
        'domain' => 'pondoksarikutabali.com',
    ],
    
    'RRPTG' => [
        'name' => 'Rama Residence Petitenget',
        'logo_path' => 'logo/rrpt.png',
        'registration_form' => [
            'background_image' => 'images/RRPTG/300DPIcrm-tanpa-judul-2480-x-3508-piksel_eDbYxo1O.png',
            'title' => 'Registration Form - Rama Residence Petitenget',
            'blade_template' => 'reservation.RRPTG.registrationpdf', // Template khusus RRPTG
        ],
        'domain' => 'ramaresidencepetitenget.com',
    ],
    
    'RGH' => [
        'name' => 'Rama Garden Hotel Bali',
        'logo_path' => 'logo/rgh.png',
        'registration_form' => [
            'background_image' => 'images/RGH/300DPIcrm-tanpa-judul-2480-x-3508-piksel_eDbYxo1O.png',
            'title' => 'Registration Form - Rama Garden Hotel',
            'blade_template' => 'reservation.RGH.registrationpdf', // Template khusus RGH
        ],
        'domain' => 'ramagardenhotelbali.com',
    ],
    
    // Default fallback
    'default' => [
        'name' => 'CRM System',
        'logo_path' => 'logo/rrpt.png',
        'registration_form' => [
            'background_image' => 'images/300DPIcrm-tanpa-judul-2480-x-3508-piksel_eDbYxo1O.png',
            'title' => 'Registration Form',
            'blade_template' => 'reservation.registrationpdf', // Template default
        ],
        'domain' => 'localhost',
    ],
];