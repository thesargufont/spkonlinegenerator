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

        .outline {
            outline: 2px solid #000;
        }

        .header {
            text-align: center;
            font-weight: bold;
            /*margin-bottom: 40px;*/
            font-family: Arial, sans-serif;
            font-size: 20px;
            border: 2px solid black;
            padding: 50px;
        }
        .bodyBorder {
            border: 2px solid black;
            padding: 50px;
            /*padding-top: 50px;*/
            /*padding-bottom: 50px;*/
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
            /*border: 2px solid black;*/
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

        .page-break {
            page-break-before: always;
        }

        .fontBold {
            font-weight: bold;
        }

        .alignment-cell {
            text-align: center; /* Aligns text to center */
        }
    </style>
</head>

@foreach($data as $data)
<body>
    <div class="header">
        <table width="100%">
            <tr>
                <td width="10%">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('/images/pln_logo.png'))) }}" alt="shortcut icon" width="125%" style="filter: grayscale(100%);">
                </td>
                <td width="25%" style="text-align: left;">
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

    <div class="bodyBorder">
        <table class="content-table">
            <tr>
                <td class="fontBold">NOMOR SPK</td>
                <td style="width: 10px">:</td>
                <td>{{$data['spk_number']}}</td>
            </tr>
            <tr>
                <td class="fontBold">NOMOR WO</td>
                <td style="width: 10px">:</td>
                <td>{{$data['wo_number']}}</td>
            </tr>
            <tr>
                <td class="fontBold">DEPARTEMEN</td>
                <td style="width: 10px">:</td>
                <td>{{$data['department']}}</td>
            </tr>
            <tr>
                <td class="fontBold">KATEGORI PEKERJAAN</td>
                <td style="width: 10px">:</td>
                <td>{{$data['job_category']}}</td>
            </tr>
            <tr>
                <td class="fontBold">TANGGAL EFEKTIF</td>
                <td style="width: 10px">:</td>
                <td>{{$data['effective_date']}}</td>
                <td colspan="3"></td>
            </tr>
        </table>

        <h4>DETAIL PEKERJAAN</h4>
        <table class="content-table">
            <tr>
                <td style="width: 25px">1.</td>
                <td style="width: 330px">Tanggal Mulai</td>
                <td style="width: 10px">:</td>
                <td>{{$data['start_at']}}</td>
            </tr>
            <tr>
                <td>2.</td>
                <td>Estimasi Selesai</td>
                <td style="width: 10px">:</td>
                <td>{{$data['estimated_end']}}</td>
            </tr>
            <tr>
                <td>3.</td>
                <td>Lokasi</td>
                <td style="width: 10px">:</td>
                <td>{{$data['location']}}</td>
            </tr>
            <tr>
                <td>4.</td>
                <td>Alat</td>
                <td style="width: 10px">:</td>
                <td>{{$data['device']}}</td>
            </tr>
            <tr>
                <td>5.</td>
                <td>Brand</td>
                <td style="width: 10px">:</td>
                <td>{{$data['brand']}}</td>
            </tr>
            <tr>
                <td>6.</td>
                <td>Nomor Seri</td>
                <td style="width: 10px">:</td>
                <td>{{$data['serial_number']}}</td>
            </tr>
            <tr>
                <td>7.</td>
                <td>Nomor Aktiva</td>
                <td style="width: 10px">:</td>
                <td>{{$data['activa_number']}}</td>
            </tr>
            <tr>
                <td>8.</td>
                <td>Engineer</td>
                <td style="width: 10px">:</td>
                <td>{{$data['engineer']}}</td>
            </tr>
            <tr>
                <td>9.</td>
                <td>Supervisor</td>
                <td style="width: 10px">:</td>
                <td>{{$data['supervisor']}}</td>
            </tr>
            <tr>
                <td>10.</td>
                <td>Deskripsi WO</td>
                <td style="width: 10px">:</td>
                <td>{{$data['wo_description']}}</td>
            </tr>
            <tr>
                <td>11.</td>
                <td>Deskripsi Tugas</td>
                <td style="width: 10px">:</td>
                <td>{{$data['job_description']}}</td>
            </tr>
        </table>

        <table class="signatures">
            <tr>
                <td>&nbsp;</td>
                <td class="fontBold">Ungaran, {{$data['approve_at']}}</td>
            </tr>
            <tr>
                <td class="fontBold">Yang menerima perintah</td>
                <td class="fontBold">Mengetahui</td>
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
    </div>

    <!-- BREAK PAGE -->
    <div class="page-break"></div>
    <!-- BREAK PAGE -->
    @endforeach
</body>

</html>

