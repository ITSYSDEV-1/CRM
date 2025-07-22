<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Registration Form - Kuta Seaview</title>
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
            background-image: url('<?php echo e(asset('images/300DPIcrm-tanpa-judul-2480-x-3508-piksel_eDbYxo1O.png')); ?>');
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
            top: 593.75px;
            left: 821.875px;
            font-size: 75px;
        }

        .folio-number {
            top: 696.875px;
            left: 796.875px;
            font-size: 50px;
        }
        

        .room-type {
            top: 631.25px;
            left: 1675px;
            font-size: 50px;
        }

        .guest-name {
            top: 1187.5px;
            left: 678.125px;
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
            top: 1512.5px;
            left: 759.375px;
            font-size: 50px;
        }
        
        .company {
            top: 568.75px;
            left: 1675px;
            font-size: 50px;
        }
        
        .nationality {
            top: 1859.375px;
            left: 681.25px;
            font-size: 50px;
        }
        
        .id-number {
           top: 1659.375px;
            left: 912.5px;
            font-size: 50px;
            max-width: 1093.75px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .birthday {
            top: 1659.375px;
            left: 1868.75px;
            font-size: 50px;
        }
        
        .address {
            position: absolute;
            top: 1340.625px;
            left: 759.375px;
            width: 2784.375px;
            max-height: 293.75px;
            font-size: 50px; 
            line-height: 1.2;
            word-wrap: break-word;
            white-space: normal;
            overflow: hidden;
        }
        
        .mobile-phone {
            top: 1434.375px;
            left: 759.375px;
            font-size: 50px;
        }
        
        .arrival-date {
            top: 765.625px;
            left: 796.875px;
            font-size: 50px;
        }
        
        .departure-date {
            top: 884.375px;
            left: 796.875px;
            font-size: 50px;
        }

        .number-of-nights {
            top: 821.875px;
            left: 796.875px;
            font-size: 50px;
        }
        
        .adult-count {
            top: 703.125px;
            left: 1675px;
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
            <?php echo e($room ?? '136'); ?>

        </div>
        
        <!-- Folio Number -->
        <div class="overlay-text folio-number">
            <?php echo e($folio_master ?? '43956'); ?>

        </div>
        
        <!-- Room Type -->
        <div class="overlay-text room-type">
            <?php echo e($roomtype ?? 'PGRT'); ?>

        </div>
        
        <!-- Guest Name -->
        <!-- <div class="overlay-text guest-name">
            <?php echo e(strtoupper($fname . ' ' . $lname . ', ' . $salutation) ?? 'GUEST NAME'); ?>

        </div>
         -->
                <!-- Guest First Name -->
        <div class="overlay-text guest-first-name">
            <?php echo e(strtoupper($fname) ?? 'FIRST NAME'); ?>

        </div>
        
        <!-- Guest Last Name -->
        <div class="overlay-text guest-last-name">
            <?php echo e(strtoupper($lname . ', ' . $salutation) ?? 'LAST NAME'); ?>

        </div>

        <!-- Email -->
        <div class="overlay-text email">
            <?php echo e($email ?? ''); ?>

        </div>
        
        <!-- Company -->
        <div class="overlay-text company">
            <?php echo e($company ?? 'EXPEDIA.COM'); ?>

        </div>
        
        <!-- Nationality -->
        <div class="overlay-text nationality">
            <?php echo e($country_id ?? ''); ?>

        </div>
        
        <!-- ID Number -->
        <div class="overlay-text id-number">
            <?php echo e($idnumber ?? ''); ?>

        </div>

       <!-- Birthday -->
        <div class="overlay-text birthday">
            <?php echo e(isset($birthday) ? \Carbon\Carbon::parse($birthday)->format('d M Y') : ''); ?>

        </div>

        <!-- Address -->
        <div class="overlay-text address">
            <?php echo e($address ?? ''); ?>

        </div>
        
        <!-- Mobile Phone -->
        <div class="overlay-text mobile-phone">
            <?php echo e($mobile == null ? '-' : $mobile); ?>

        </div>
        
        <!-- Arrival Date -->
        <div class="overlay-text arrival-date">
            <?php echo e(\Carbon\Carbon::parse($dateci)->format('d M Y') ?? ''); ?>

        </div>
        
         <!-- Number of Nights -->
        <div class="overlay-text number-of-nights">
            <?php if($dateci && $dateco): ?>
                <?php echo e(\Carbon\Carbon::parse($dateci)->diffInDays(\Carbon\Carbon::parse($dateco))); ?>

            <?php else: ?>
                <?php echo e(''); ?>

            <?php endif; ?>
        </div>

        <!-- Departure Date -->
        <div class="overlay-text departure-date">
            <?php echo e(\Carbon\Carbon::parse($dateco)->format('d M Y') ?? ''); ?>

        </div>
        
        <!-- Adult Count -->
        <div class="overlay-text adult-count">
            <?php echo e($pax ?? '2'); ?>

        </div>
    </div>
</body>
</html>
<?php /**PATH /home/jalakdev/www/crm/resources/views/reservation/registrationpdf.blade.php ENDPATH**/ ?>