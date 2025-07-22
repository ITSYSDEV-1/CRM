<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Registration Form - Kuta Seaview</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            position: relative;
        }
        
        .form-container {
            position: relative;
            width: 210mm;
            height: 297mm;
            background-image: url('{{ asset('images/300DPIcrm-tanpa-judul-2480-x-3508-piksel_eDbYxo1O.png') }}');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
        }
        
        .overlay-text {
            position: absolute;
            font-family: Arial, sans-serif;
            color: #272a33;
            font-weight: bold;
        }
        
        /* Room Number - koordinat awal, bisa disesuaikan dari devtools */
        .room-number {
            top: 144px;
            left: 120px;
            font-size: 14px;
        }
        
        /* Folio Number */
        .folio-number {
            top: 177px;
            left: 120px;
            font-size: 14px;
        }
        
        /* Room Type */
        .room-type {
            top: 177px;
            left: 275px;
            font-size: 14px;
        }
        
        /* Guest Name */
        .guest-name {
            top: 265px;
            left: 120px;
            font-size: 14px;
        }
        
        /* Email */
        .email {
            top: 300px;
            left: 120px;
            font-size: 14px;
        }
        
        /* Company */
        .company {
            top: 332px;
            left: 120px;
            font-size: 14px;
        }
        
        /* Nationality */
        .nationality {
            top: 366px;
            left: 120px;
            font-size: 14px;
        }
        


/* untuk 20 id number max */
.id-number {
    top: 368px;
    left: 289px;
    font-size: 12px;
}
/* Birthday */
.birthday {
    top: 368px;
    left: 523px;
    font-size: 14px;
}




        /* Address */
.address {
    position: absolute;
    top: 403px;
    left: 120px;
    width: 285px; /* lebar maksimum, dari 120px ke 405px */
    font-size: 14px;
    line-height: 1.2; /* atur tinggi baris agar rapi */
    word-wrap: break-word;
    white-space: normal;
}

        
        /* Mobile Phone */
.mobile-phone {
    top: 446px;
    left: 450px;
    font-size: 14px;
}
        
        /* Arrival Date */
.arrival-date {
    top: 363px;
    left: 657px;
    font-size: 14px;
}
        
        /* Departure Date */
        .departure-date {
            top: 428px;
            left: 657px;
            font-size: 14px;
        }
        




        /* Adult Count */
        .adult-count {
            top: 144px;
            left: 275px;
            font-size: 14px;
        }
        
        /* Child Count */
        .child-count {
            top: 144px;
            left: 416px;
            font-size: 14px;
        }
        
        /* Arrival Time */
        .arrival-time {
            top: 280px;
            left: 450px;
            font-size: 12px;
        }
    </style>
</head>
<body>
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
        <div class="overlay-text id-number" id="idNumber">
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
        
        <!-- Child Count
        <div class="overlay-text child-count">
            0
        </div> -->
        
        <!-- Arrival Time
        <div class="overlay-text arrival-time">
            14:00
        </div> -->
    </div>
</body>
</html>



<script>
   window.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('idNumber');
    const content = el.textContent.trim();
    const length = content.length;

    let fontSize = '14px';
    let left = '288px';

    if (length > 24) {
        fontSize = '11px';
        left = '285px';
    } else if (length > 20) {
        fontSize = '12px';
        left = '289px';
    } else if (length > 18) {
        fontSize = '13px';
        left = '288px';
    } else {
        fontSize = '14px';
        left = '288px';
    }

    el.style.fontSize = fontSize;
    el.style.left = left;
});

</script>
