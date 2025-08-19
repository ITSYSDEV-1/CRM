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
        
        /* Koordinat dikali 3.125 */
        .room-number {
            top: 415.625px;
            left: 284.375px;
            font-size: 81.25px;
        }

        .room-number2 {
            top: 531.25px;
            left: 250px;
            font-size: 59.375px;
        }

        .folio-number {
            top: 675px;
            left: 1831.25px;
            font-size: 50px;
        }

        .room-type {
            top: 678.125px;
            left: 387.5px;
            font-size: 50px;
        }

        .guest-name {
            top: 1065.625px;
            left: 346.875px;
            font-size: 50px;
        }

        .guest-first-name {
            top: 1187.5px;
            left: 678.125px;
            font-size: 50px;
        }
        
        .guest-last-name {
            top: 1187.5px;
            left: 1771.875px;
            font-size: 50px;
        }
                
        .email {
            top: 1596.875px;
            left: 346.875px;
            font-size: 43.75px;
        }
                
        .company {
            top: 965.625px;
            left: 515.625px;
            font-size: 43.75px;
        }
                
        .nationality {
            top: 1471.875px;
            left: 346.875px;
            font-size: 50px;
        }
                
        .id-number {
            top: 1193.75px;
            left: 459.375px;
            font-size: 40.625px;
            max-width: 1093.75px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
                
        .birthday {
            top: 1215.625px;
            left: 1590.625px;
            font-size: 50px;
        }
                
        .address {
            position: absolute;
            top: 1300px;
            left: 346.875px;
            width: 2784.375px;
            max-height: 293.75px;
            font-size: 50px;
            line-height: 1.2;
            word-wrap: break-word;
            white-space: normal;
            overflow: hidden;
        }
                
        .mobile-phone {
            top: 1459.375px;
            left: 1081.25px;
            font-size: 50px;
        }
            
        .arrival-date {
            top: 1721.875px;
            left: 2025px;
            font-size: 50px;
        }
        
        .departure-date {
            top: 1831.25px;
            left: 2025px;
            font-size: 50px;
        }

        .number-of-nights {
            top: 821.875px;
            left: 796.875px;
            font-size: 50px;
        }
        
        .adult-count {
            top: 653.125px;
            left: 1078.125px;
            font-size: 50px;
        }
        
        .child-count {
            top: 765.625px;
            left: 1675px;
            font-size: 50px;
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

        <!-- <div class="overlay-text room-number2">
            {{ $room ?? '136' }}
        </div> -->
        
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
        
                <!-- Guest First Name -->
        <!-- <div class="overlay-text guest-first-name">
            {{ strtoupper($fname) ?? 'FIRST NAME' }}
        </div> -->
        
        <!-- Guest Last Name -->
        <!-- <div class="overlay-text guest-last-name">
            {{ strtoupper($lname . ', ' . $salutation) ?? 'LAST NAME' }}
        </div> -->

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
        <!-- <div class="overlay-text birthday">
            {{ isset($birthday) ? \Carbon\Carbon::parse($birthday)->format('d M Y') : '' }}
        </div> -->

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
        
         <!-- Number of Nights -->
        <!-- <div class="overlay-text number-of-nights">
            @if($dateci && $dateco)
                {{ \Carbon\Carbon::parse($dateci)->diffInDays(\Carbon\Carbon::parse($dateco)) }}
            @else
                {{ '' }}
            @endif
        </div> -->

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
