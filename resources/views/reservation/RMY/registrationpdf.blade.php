<!DOCTYPE html>
<html lang="">
<style>
    .table{
        width: 100%;
        margin-bottom: 1rem;
        background-color: transparent;
        border-collapse: collapse;
    }
    table#table1 td{
        vertical-align: inherit;
        font-size: 0.75rem;
    }

    .table-bordered {
        border: 1px solid #000000;
    }
    .table-bordered td, .table-bordered th {
        border: 1px solid #000000;
    }
    .table td, .table th {
        padding: 0.15rem;
        /*vertical-align: top;*/
        border-top: 1px solid #000000;
    }
    .right{
        text-align: right;
    }
    .center {
        text-align: center;
    }
    /* Might want to wrap a span around your checkbox text */
    .checkboxtext
    {
        /* Checkbox text */
        font-size: 110%;
        display: inline;
    }
    table#table2 label{
        float: left;
        margin-left:30px;
        margin-top: -10px;
    }
    table#table2 label input[type='checkbox']{
        margin-left:-20px;
        vertical-align:top;
    }
    table#table2 td{
        border: 0px;
    }
    table#table1 p{
        font-size: 0.6rem;
        margin-top: 1px;
        margin-bottom: 1px;
    }
</style>
<head>
    <title>Registration From</title>
</head>
<body>
<p class="center" style="font-size: 1rem"><STRONG>REGISTRATION FORM - RAMAYANA SUITES AND RESORT</STRONG></p>

<table id="table1" class="table table-bordered">
    <tr>
        <td>Room Type<br>Jenis Kamar</td>
        <td class="center"><strong>{{$roomtype}}</strong></td>
        <td>No. of Person<br>Jumlah Tamu</td>
        <td class="center"><strong>{{$pax}}</strong></td>
        <td colspan="2">Room No<br>No. Kamar</td>
        <td class="center"><strong>{{$room}}</strong></td>
    </tr>
    <tr>
        <td>Folio No<br>No Folio</td>
        <td class="center"><strong>{{$folio_master}}</strong></td>
        <td>No. of Room<br>Jumlah Kamar</td>
        <td class="center"><strong>1</strong></td>
        <td colspan="2">Front Desk Assistant<br>Penerima Tamu</td>
        <td class="center"><strong>-</strong></td>
    </tr>
    <tr>
        <td colspan="7" style="text-align: center; font-size: 1rem"><strong>Check Out Time : 12.00 Noon -</strong></td>
    </tr>
    <tr>
        <td>Name(Mr/Mrs/Miss)<br>Nama(Tn/Ny/Nona)</td>
        <td colspan="5"><strong> {{$fname}} {{$lname}}, {{$salutation}}</strong></td>
        <td rowspan="2" class="center">Traveling Purpose<br>Tujuan Perjalanan <br><strong>-</strong></td>
    </tr>
    <tr>
        <td>Nationality<br>Kewarganegaraan</td>
        <td class="center"><strong>{{$country_id}}</strong></td>
        <td>Passport Number<br>Nomor Paspor</td>
        <td class="center"><strong>{{$idnumber}}</strong></td>
        <td>Birth Date <br>Tanggal Lahir</td>
        <td class="center"><strong>{{$birthday}}</strong></td>
    </tr>
    <tr>
        <td>Email Address<br>Alamat Email</td>
        <td class="center" colspan="2"><strong>{{$email}}</strong></td>
        <td>Telephone<br>Telepon</td>
        <td class="center" colspan="2"><strong>{{$mobile == null ? '-':$mobile}}</strong></td>
        <td rowspan="2" class="center">Arrival Time<br>Waktu Kedatangan<br><strong>14:00</strong></td>
    </tr>
    <tr>
        <td>Company Name<br>Nama Perusahaan</td>
        <td class="center" colspan="2">-</td>
        <td>Company Address<br>Alamat Perusahaan</td>
        <td class="center" colspan="2">-</td>
    </tr>
    <tr>
        <td rowspan="2">Home Address <br> Alamat Rumah</td>
        <td class="center" colspan="2" rowspan="2">{{$address == null ? '-':$address}}</td>
        <td>City <br>Kota</td>
        <td class="center" colspan="2">-</td>
        <td rowspan="2" class="center">Arrival Date <br> Tanggal Kedatangan<br><strong>{{\Carbon\Carbon::parse($dateci)->format('d M Y')}}</strong></td>
    </tr>
    <tr>
        <td>Country <br> Negara</td>
        <td class="center" colspan="2"><strong>{{$country_id}}</strong></td>
    </tr>
    <tr>
        <td>Post Code <br>Kode Pos</td>
        <td class="center" colspan="2"><strong>-</strong></td>
        <td>Valid Until <br>Berlaku Sampai</td>
        <td class="center" colspan="2"><strong>-</strong></td>
        <td rowspan="2" class="center">Departure Date <br> Tanggal Keberangkatan <br><strong>{{\Carbon\Carbon::parse($dateco)->format('d M Y')}}</strong></td>
    </tr>
    <tr>
        <td style="vertical-align: top; height: 3em">Method of Payment <br> Cara Pembayaran</td>
        <td class="center" style="vertical-align: top; height: 3em"><strong>CASH</strong></td>
        <td style="vertical-align: top; height: 3em" colspan="4">
            <table id="table2">
                <tr>
                    <td><label><input type="checkbox"> Cash <br>Tunai</label></td>
                    <td><label><input type="checkbox"> Voucher <br>Voucher</label></td>
                    <td><label><input type="checkbox"> Company <br>Perusahaan</label></td>
                    <td><label><input type="checkbox"> Other <br>Lain-lain</label></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: top;" colspan="2">Credit Card No <br> No. Kartu Kredit</td>
        <td style="vertical-align: inherit;" colspan="5">
            <table id="table2">
                <tr>
                    <td><label><input type="checkbox"> Visa</label></td>
                    <td><label><input type="checkbox"> Master</label></td>
                    <td><label><input type="checkbox"> Diners</label></td>
                    <td><label><input type="checkbox"> JCB</label></td>
                    <td><label><input type="checkbox"> Amex</label></td>
                    <td><label><input type="checkbox"> BCA Card</label></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="height: 2em" colspan="7"></td>
    </tr>
    <tr>
        <td colspan="7">
            <input style="vertical-align:top;" type="checkbox"> Non - Smoking Room / Kamar Dilarang Merokok <br>
            <p>
                RAMAYANA Resort and Spa reserves the right of admission, all dispute are subject to jurisdiction of Indonesian High Court.
                Please note that guest have been assigned in a NON - SMOKING room and agree to obey the NON - SMOKING regulation and refrain from smoking in the room during stay. In the event
                the room is found not rent able as result of SMOKING either by guest themselves or visiting guest, family or related person they are responsible for, the undersigned are liable to pay the
                complete cleaning fee and fabric replacement charge equal to one room night charge, as well as will be held responsible for any damage caused to the hotel property.
            </p>
            <p>
                Safe deposit box is available in the room as a courtesy and without charges, the hotel has no means to verify the content of the safe and will not be held resposible for loss or damage of
                valuable things which are misplaced or stollen, and damage caused of any nature including as result of negligent act or omission to properties belonging to the guest whether placed in the
                room's safe or not or from physical injury suffered by guest.
            </p>
            <p>
                Pets are not allowed to enter the hotel premises, as well as to bring foods or fruits with strong odor or pigmentation which may affect the scent of the room or creating permanent stain on
                the linen (i.e durian and mangosteen)
            </p>
        </td>
    </tr>
    <tr>
        <td colspan="7">
            <p>
                Individuals who are not registered, are prohibited to stay overnight, a joining fee will be charged for each additional guest, and required to register and provide ID for safety and protection
                of all guest. Management reserves the right to ask for proof of relationship between adults and minors. The company reserves the right to require any individuals who do not obey the
                regulation or the applicable laws of the country to leave the premises.</p>
            <p>
                Guest are prohibited to hang clothes on the balcony. We request the guest to excercise extra care when on the balcony and please keep children away from the railing. Additional caution
                should be excercised if guest consumed alcohol on the balcony</p>
            <p>
                To bring or playing Bibby Guns is also prohibited in hotel premises. The hotel will not take any responsibility for any accident and loss suffered by guest or visiting guest, family or related
                person they are responsible for, whilst using hotel's facility and guest reserves no right to prosecute hotel's staff, hotel management, corporate and stake holders for any immoderation
            </p>
        </td>
    </tr>
    <tr>
        <td colspan="7">
            <p>
                The management collects and processes guest personal information required in this form to manage stay and provide guest with personalized service or comply with local law
                requirements, and this information may be transferred through our global reservations and system and shared within the group, selected service provider may also have restricted access
                to our guest record but only under strict privacy control.
            </p>
        </td>
    </tr>
    <tr>
        <td colspan="7">
            <p>
                Guests are being made aware that in the event of early check out than the confirmed date as written in the hotel confirmation letter and signed registration card at the time of check in, the
                hotel has the right to charge an early departure fee. Early departure for Travel Agent or contracted bookings, full costs as per original reservation will be charged.
                Please return the key card to Front Desk Agent on your check out time. Additional charge of Rp. 50.000 nett per card will be applied if your card is lost.
            </p>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: top; height: 5em" colspan="4">
            <p>
                By signing this form, hereby guest declare to have read, understood, and agree to be hold responsible for all the above
                requirements, and authorize the hotel to charge total amount of expenses occured during stay in the payment method
                specified.
            </p>
        </td>
        <td style="vertical-align: top; height: 5em" colspan="3">
            <p class="center"><strong>GUEST SIGNATURE / TANDA TANGAN</strong></p>
        </td>
    </tr>
    <tr>
        <td style="vertical-align: top; height: 5em" class="center"><strong>Checked - In by</strong></td>
        <td style="vertical-align: top; height: 5em" class="center"><strong>Checked - out by</strong></td>
        <td style="vertical-align: top; height: 5em" class="center"><strong>Front Desk / Supervisor</strong></td>
        <td style="vertical-align: top; height: 5em" class="center" colspan="4"><strong>Rates are included applicable Goverment Tax and Service Charge</strong></td>
    </tr>
</table>
<br>

</body>
</html>
