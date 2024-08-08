<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Perintah Kerja Intern</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 16px;
        }

        .header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 40px;
            font-family: Arial, sans-serif;
            font-size: 20px;
        }

        .logo {
            text-align: left;
            position: absolute;
        }

        .header img {
            width: 50px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .content-table td,
        .content-table th {
            padding: 5px;
            vertical-align: top;
        }

        .signatures {
            width: 100%;
            margin-top: 30px;
            text-align: center;
        }

        .signatures td {
            padding: 20px;
        }

        .footer {
            margin-top: 50px;
        }
    </style>
</head>

@foreach($data as $data)

<body>
    <div class="header">
        <table width="100%">
            <tr>
                <td width="10%">
                    <link rel="shortcut icon" href="{{ asset('images/pln_logo.png') }}">
                </td>
                <td width="20%" style="text-align: left;">
                    <div>PLN (Persero) UIP2B</div>
                    <div>UIP2B JATENG & DIY</div>
                </td>
                <td width="70%" style="text-align: center;">
                    <div>SURAT PERINTAH KERJA INTERN</div>
                    <div>( SPKI )</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="content-table">
        <tr>
            <td>NOMOR SPK</td>
            <td>:</td>
            <td>{{$data['spk_number']}}</td>
        </tr>
        <tr>
            <td>NOMOR WO</td>
            <td>:</td>
            <td>{{$data['wo_number']}}</td>
        </tr>
        <tr>
            <td>DEPARTEMEN</td>
            <td>:</td>
            <td>{{$data['department']}}</td>
        </tr>
        <tr>
            <td>KATEGORI PEKERJAAN</td>
            <td>:</td>
            <td>{{$data['job_category']}}</td>
        </tr>
        <tr>
            <td>TANGGAL EFEKTIF</td>
            <td>:</td>
            <td>{{$data['effective_date']}}</td>
            <td colspan="3"></td>
        </tr>
    </table>

    <h4>DETAIL PEKERJAAN</h4>
    <table class="content-table">
        <tr>
            <td>1.</td>
            <td>Tanggal Mulai</td>
            <td>:</td>
            <td>{{$data['start_at']}}</td>
        </tr>
        <tr>
            <td>2.</td>
            <td>Estimasi Selesai</td>
            <td>:</td>
            <td>{{$data['estimated_end']}}</td>
        </tr>
        <tr>
            <td>3.</td>
            <td>Lokasi</td>
            <td>:</td>
            <td>{{$data['location']}}</td>
        </tr>
        <tr>
            <td>4.</td>
            <td>Alat</td>
            <td>:</td>
            <td>{{$data['device']}}</td>
        </tr>
        <tr>
            <td>5.</td>
            <td>Brand</td>
            <td>:</td>
            <td>{{$data['brand']}}</td>
        </tr>
        <tr>
            <td>6.</td>
            <td>Nomor Seri</td>
            <td>:</td>
            <td>{{$data['serial_number']}}</td>
        </tr>
        <tr>
            <td>7.</td>
            <td>Nomor Aktiva</td>
            <td>:</td>
            <td>{{$data['activa_number']}}</td>
        </tr>
        <tr>
            <td>8.</td>
            <td>Engineer</td>
            <td>:</td>
            <td>{{$data['engineer']}}</td>
        </tr>
        <tr>
            <td>9.</td>
            <td>Supervisor</td>
            <td>:</td>
            <td>{{$data['supervisor']}}</td>
        </tr>
        <tr>
            <td>10.</td>
            <td>Deskripsi WO</td>
            <td>:</td>
            <td>{{$data['wo_description']}}</td>
        </tr>
        <tr>
            <td>11.</td>
            <td>Deskripsi Tugas</td>
            <td>:</td>
            <td>{{$data['job_description']}}</td>
        </tr>
    </table>

    <table class="signatures">
        <tr>
            <td>&nbsp;</td>
            <td>Ungaran, {{$data['approve_at']}}</td>
        </tr>
        <tr>
            <td>Yang menerima perintah</td>
            <td>Mengetahui</td>
        </tr>
        <tr>
            <td>
                <br><br>
                <span>Dokumen ini telah ditandatangani secara komputerisasi oleh <strong>{{$data['engineer']}}</strong> pada tanggal {{$data['approve_at']}}</span>
            </td>
            <td>
                <br><br>
                <span>Dokumen ini telah ditandatangani secara komputerisasi oleh <strong>{{$data['supervisor']}}</strong> pada tanggal {{$data['approve_at']}}</span>
            </td>
        </tr>
        <tr>
            <td><strong>ENGINEER</strong></td>
            <td><strong>SUPERVISOR</strong></td>
        </tr>
    </table>

    <div class="footer">
        <strong>Keterangan</strong><br>
        1. Lembar 1 (asli) untuk arsip
    </div>
    @endforeach
</body>

</html>