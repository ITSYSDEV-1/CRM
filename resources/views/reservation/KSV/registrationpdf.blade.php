<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
        <title>{{ App\Helpers\UnitHelper::getRegistrationFormTitle() }}</title>
    <style>
        @page {
            size: A4;
            margin: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            position: relative;
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        .form-container {
            position: relative;
            width: 210mm;
            height: 297mm;
            background-image: url('{{ App\Helpers\UnitHelper::getRegistrationFormBackground() }}');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            overflow: hidden;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
        }
        
        .overlay-text {
            position: absolute;
            font-family: Arial, sans-serif;
            color: #272a33;
            font-weight: bold;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }
        
        /* Koordinat dikali 3.125 untuk kompensasi DPI 300 */
        .room-number {
            top: 450px;    /* 144 × 3.125 */
            left: 375px;   /* 120 × 3.125 */
            font-size: 44px; /* 14 × 3.125 */
        }
        
        .folio-number {
            top: 553px;    /* 177 × 3.125 */
            left: 375px;   /* 120 × 3.125 */
            font-size: 44px; /* 14 × 3.125 */
        }
        
        .room-type {
            top: 553px;    /* 177 × 3.125 */
            left: 859px;   /* 275 × 3.125 */
            font-size: 44px; /* 14 × 3.125 */
        }
        
        .guest-name {
            top: 828px;    /* 265 × 3.125 */
            left: 375px;   /* 120 × 3.125 */
            font-size: 44px; /* 14 × 3.125 */
        }
        
        .email {
            top: 938px;    /* 300 × 3.125 */
            left: 375px;   /* 120 × 3.125 */
            font-size: 44px; /* 14 × 3.125 */
        }
        
        .company {
            top: 1038px;   /* 332 × 3.125 */
            left: 375px;   /* 120 × 3.125 */
            font-size: 44px; /* 14 × 3.125 */
        }
        
        .nationality {
            top: 1144px;   /* 366 × 3.125 */
            left: 375px;   /* 120 × 3.125 */
            font-size: 44px; /* 14 × 3.125 */
        }
        
        .id-number {
            top: 1150px;   /* 368 × 3.125 */
            left: 903px;   /* 289 × 3.125 */
            font-size: 38px; /* 12 × 3.125 */
            max-width: 469px; /* 150 × 3.125 */
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .birthday {
            top: 1150px;   /* 368 × 3.125 */
            left: 1634px;  /* 523 × 3.125 */
            font-size: 44px; /* 14 × 3.125 */
        }
        
        .address {
            position: absolute;
            top: 1259px;   /* 403 × 3.125 */
            left: 375px;   /* 120 × 3.125 */
            width: 891px;  /* 285 × 3.125 */
            max-height: 94px; /* 30 × 3.125 */
            font-size: 44px; /* 14 × 3.125 */
            line-height: 1.2;
            word-wrap: break-word;
            white-space: normal;
            overflow: hidden;
        }
        
        .mobile-phone {
            top: 1394px;   /* 446 × 3.125 */
            left: 1406px;  /* 450 × 3.125 */
            font-size: 44px; /* 14 × 3.125 */
        }
        
        .arrival-date {
            top: 1134px;   /* 363 × 3.125 */
            left: 2053px;  /* 657 × 3.125 */
            font-size: 44px; /* 14 × 3.125 */
        }
        
        .departure-date {
            top: 1338px;   /* 428 × 3.125 */
            left: 2053px;  /* 657 × 3.125 */
            font-size: 44px; /* 14 × 3.125 */
        }
        
        .adult-count {
            top: 450px;    /* 144 × 3.125 */
            left: 859px;   /* 275 × 3.125 */
            font-size: 44px; /* 14 × 3.125 */
        }
        
        .child-count {
            top: 450px;    /* 144 × 3.125 */
            left: 1300px;  /* 416 × 3.125 */
            font-size: 44px; /* 14 × 3.125 */
        }
        
        .arrival-time {
            top: 875px;    /* 280 × 3.125 */
            left: 1406px;  /* 450 × 3.125 */
            font-size: 38px; /* 12 × 3.125 */
        }
    </style>
</head>
<body>
    <!-- HTML content tetap sama -->
    <div class="form-container">
        <!-- Room Number -->
        <div class="overlay-text room-number">
            {{ $room ?? '136' }}
        </div>
        
        <!-- Folio Number -->
        <div class="overlay-text folio-number">
            {{ $folio_master ?? '43956' }}
        </div>
        
        <!-- Room Type -->
        <div class="overlay-text room-type">
            {{ $roomtype ?? 'PGRT' }}
        </div>
        
        <!-- Guest Name -->
        <div class="overlay-text guest-name">
            {{ strtoupper($fname . ' ' . $lname . ', ' . $salutation) ?? 'GUEST NAME' }}
        </div>
        
        <!-- Email -->
        <div class="overlay-text email">
            {{ $email ?? '' }}
        </div>
        
        <!-- Company -->
        <div class="overlay-text company">
            {{ $company ?? 'EXPEDIA.COM' }}
        </div>
        
        <!-- Nationality -->
        <div class="overlay-text nationality">
            {{ $country_id ?? '' }}
        </div>
        
        <!-- ID Number -->
        <div class="overlay-text id-number">
            {{ $idnumber ?? '' }}
        </div>

       <!-- Birthday -->
        <div class="overlay-text birthday">
            {{ isset($birthday) ? \Carbon\Carbon::parse($birthday)->format('d M Y') : '' }}
        </div>

        <!-- Address -->
        <div class="overlay-text address">
            {{ $address ?? '' }}
        </div>
        
        <!-- Mobile Phone -->
        <div class="overlay-text mobile-phone">
            {{ $mobile == null ? '-' : $mobile }}
        </div>
        
        <!-- Arrival Date -->
        <div class="overlay-text arrival-date">
            {{ \Carbon\Carbon::parse($dateci)->format('d M Y') ?? '' }}
        </div>
        
        <!-- Departure Date -->
        <div class="overlay-text departure-date">
            {{ \Carbon\Carbon::parse($dateco)->format('d M Y') ?? '' }}
        </div>
        
        <!-- Adult Count -->
        <div class="overlay-text adult-count">
            {{ $pax ?? '2' }}
        </div>
    </div>
</body>
</html>
