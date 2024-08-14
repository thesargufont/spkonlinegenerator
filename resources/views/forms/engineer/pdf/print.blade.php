<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Pekerjaan</title>
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
            font-size: 16px;
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

        .content td {
            border: 1px solid black;
            padding: 5px;
        }

        .content th {
            border: 1px solid black;
            padding: 5px;
            background-color: #f2f2f2;
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
            /* margin-top: 50px; */
            position: fixed;
            bottom: -60px;
            left: 0px;
            right: 0px;
            height: 50px;
            margin-bottom: 40px;
            font-family: Arial, sans-serif;
            font-size: 11px;
        }

        .page-break {
            page-break-before: always;
        }

        .page-break-aft {
            page-break-after: always;
        }
    </style>
</head>

@foreach($data as $data)

<body>
    <div class="header">
        <table width="100%">
            <tr style="border: 1px solid black;">
                <td width="10%" style="padding-left: 10px;">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('/images/pln_logo.png'))) }}" alt="shortcut icon" width="125%" style="filter: grayscale(100%);">
                </td>
                <td width="60%" style="text-align: center;">
                    <div>PT PLN (Persero) UIP2B</div>
                    <div>UP2B JAWA TENGAH & DIY</div>
                    <div>JL. JENDRAL SUDIRMAN KM. 23 UNGARAN</div>
                </td>
                <td width="15%" style="text-align: left; border-left: 1px solid black; border-right: 1px solid black;">
                    <div>No. Dokumen</div>
                    <div style="border-top: 1px solid black;">Berlaku Efektif</div>
                    <div style="border-top: 1px solid black;">Revisi</div>

                </td>
                <td width="15%" style="text-align: left;">
                    <div>01/FR/JTD-FSO-TEL</div>
                    <div style="border-top: 1px solid black;">{{$data['effective_date']}}</div>
                    <div style="border-top: 1px solid black;">00</div>
                </td>
            </tr>
            <tr style="border: 1px solid black;">
                <td width="10%">
                    &nbsp;
                </td>
                <td width="60%" style="text-align: center;">
                    <div>SISTEM MANAJEMEN TERINTEGRASI</div>
                </td>
                <td width="15%" style="text-align: left; border-left: 1px solid black; border-right: 1px solid black;">
                    <div>Halaman</div>
                </td>
                <td width="15%" style="text-align: left;">
                    <div>1/2</div>
                </td>
            </tr>
        </table>
        <table width="100%" style="margin-top : 20px; font-size:18px;">
            <tr>
                <td width="100%" style="text-align: center;">
                    <div>BERITA ACARA PEKERJAAN</div>
                </td>
            </tr>
        </table>
        <table width="100%" style="margin-top : 20px;">
            <tr>
                <td width="100%" style="text-align: center;">
                    <div>{{$data['location']}}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="content-table" width="100%">
        <tr>
            <td width="20%" style="font-weight:bold;">NOMOR WP</td>
            <td width="2%" style="font-weight:bold;">:</td>
            <td width="60%" style="font-weight:bold;">{{$data['wp_number']}}</td>
        </tr>
        <tr>
            <td width="20%" style="font-weight:bold;">NOMOR SPK</td>
            <td width="2%" style="font-weight:bold;">:</td>
            <td width="60%" style="font-weight:bold;">{{$data['spk_number']}}</td>
        </tr>
        <tr>
            <td width="20%" style="font-weight:bold;">KATEGORI PEKERJAAN</td>
            <td width="2%" style="font-weight:bold;">:</td>
            <td width="60%" style="font-weight:bold;">{{$data['job_category']}}</td>
            <td colspan="3"></td>
        </tr>
    </table>

    <div class="content">
        <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
        <!-- <p>Pada hari ini <strong>{{$data['day']}}, {{$data['effective_date']}}4</strong> di <strong>{{$data['location']}}</strong>, telah dilakukan pekerjaan <strong>Perbaikan</strong> dengan detail sebagai berikut:</p>

        <table>
            <tr>
                <th>Alat</th>
                <th>Brand</th>
                <th>Nomor Seri</th>
                <th>Nomor Aktiva</th>
                <th>Kategori Alat</th>
                <th>Engineer</th>
                <th>Supervisor</th>
                <th>Deskripsi WO</th>
                <th>Deskripsi Tugas</th>
            </tr>
            <tr>
                <td>{{$data['device']}}</td>
                <td>{{$data['brand']}}</td>
                <td>{{$data['serial_number']}}</td>
                <td>{{$data['activa_number']}}</td>
                <td>{{$data['device_category']}}</td>
                <td>{{$data['engineer']}}</td>
                <td>{{$data['supervisor']}}</td>
                <td>{{$data['wo_description']}}</td>
                <td>{{$data['job_description']}}</td>
            </tr>
        </table>

        <br>

        <p>Pekerjaan <strong>Perbaikan</strong> sudah selesai, hasil pengujian fungsi sudah sesuai.</p> -->
        <p>&nbsp;&nbsp;Demikian Berita Acara ini di buat untuk dapat digunakan sebaik baiknya.</p>
    </div>

    <table width="100%" style="margin-top : 30px;">
        <tr>
            <td width="100%" style="text-align: center;">
                <strong>Mengetahui, </strong>
            </td>
        </tr>
    </table>

    <table class="signatures" width="100%">
        <tr>
            <td width="50%" style="text-align: center; font-weight:bold;">
                <div>PT. PLN (Persero)</div>
                <div>UP2B JAWA TENGAH & DIY</div>
            </td>
            <td width="50%" style="text-align: center; font-weight:bold;">
                <div>PT. PLN (Persero)</div>
                <div>ULTG {{$data['basecamp']}}</div>
            </td>
        </tr>
        <tr>
            <td width="50%" style="text-align: center; font-size:14px; font-style:italic;">
                <div>
                    Dokumen ini telah ditandatangani secara komputerisasi</strong>
                </div>
                <div>
                    oleh <strong>{{$data['engineer']}}</strong>
                </div>
                <div>
                    pada tanggal {{$data['approve_at']}}
                </div>
            </td>
            <td width="50%" style="text-align: center;">
                <br><br>
            </td>
        </tr>
        <tr>
            <td width="50%" style="text-align: center;"><strong>{{$data['engineer']}}</strong></td>
            <td width="50%" style="text-align: center;">
                <table>
                    <tr style="text-align: center;">
                        <td>(</td>
                        <td>)</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table class="footer" width="100%">
        <tr>
            <td width="100%" style="text-align: center; border-top: 1px solid black; font: size 10px;  font-family: Arial, sans-serif;">
                <div>Dokumen ini milik PT. PLN (Persero) UIP2B JAMALI</div>
                <div>Dilarang menyalin atau memperbanyak dokumen</div>
                <div>kepada pihak lain tanpa seijin dari WMT</div>
            </td>
        </tr>
    </table>

    <!-- BREAK PAGE -->
    <div class="page-break"></div>
    <!-- BREAK PAGE -->

    <div class="header">
        <table width="100%" style="border-bottom: 1px solid black;">
            <tr style="border: 1px solid black;">
                <td width="10%" style="padding-left: 10px;">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('/images/pln_logo.png'))) }}" alt="shortcut icon" width="125%" style="filter: grayscale(100%);">
                </td>
                <td width="60%" style="text-align: center;">
                    <div>PLN (Persero) UIP2B</div>
                    <div>UP2B JAWA TENGAH & DIY</div>
                    <div>JL. JENDRAL SUDIRMAN KM. 23 UNGARAN</div>
                </td>
                <td width="15%" style="text-align: left; border-left: 1px solid black; border-right: 1px solid black;">
                    <div>No. Dokumen</div>
                    <div style="border-top: 1px solid black;">Berlaku Efektif</div>
                    <div style="border-top: 1px solid black;">Revisi</div>

                </td>
                <td width="15%" style="text-align: left;">
                    <div>01/FR/JTD-FSO-TEL</div>
                    <div style="border-top: 1px solid black;">{{$data['effective_date']}}</div>
                    <div style="border-top: 1px solid black;">00</div>
                </td>
            </tr>
            <tr style="border: 1px solid black;">
                <td width="10%">
                    &nbsp;
                </td>
                <td width="60%" style="text-align: center;">
                    <div>SISTEM MANAJEMEN TERINTEGRASI</div>
                </td>
                <td width="15%" style="text-align: left; border-left: 1px solid black; border-right: 1px solid black;">
                    <div>Halaman</div>
                </td>
                <td width="15%" style="text-align: left;">
                    <div>2/2</div>
                </td>
            </tr>
        </table>
        <table width="100%" style="margin-top : 20px; font-size:18px;">
            <tr>
                <td width="100%" style="text-align: center;">
                    <div>BERITA ACARA PEKERJAAN</div>
                </td>
            </tr>
        </table>
        <table width="100%" style="margin-top : 20px;">
            <tr>
                <td width="100%" style="text-align: center;">
                    <div>LAMPIRAN</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="content-table" width="100%">
        <tr>
            <td width="5%">&nbsp;</td>
            <!--            <td>{{ $data['image_path1'] }}</td>-->
            @if($data['image_path1'] != '')
            <td width="30%">
                @php
                $imagePath = storage_path($data['image_path1'] ?? '');
                @endphp
                @if (file_exists($imagePath))
                @php
                $imageData = base64_encode(file_get_contents($imagePath));
                @endphp
                <img src="data:image/jpg;base64,{{ $imageData }}" alt="..tidak ditemukan." style="max-width: 100%;">
                @else
                <img src="" alt="..tidak ditemukan." style="max-width: 100%;">
                @endif
            </td>
            @endif
            @if($data['image_path2'] != '')
            <td width="30%">
                @php
                $imagePath = storage_path($data['image_path2'] ?? '');
                @endphp
                @if (file_exists($imagePath))
                @php
                $imageData = base64_encode(file_get_contents($imagePath));
                @endphp
                <img src="data:image/jpg;base64,{{ $imageData }}" alt="..tidak ditemukan." style="max-width: 100%;">
                @else
                <img src="" alt="..tidak ditemukan." style="max-width: 100%;">
                @endif
            </td>
            @endif
            @if($data['image_path3'] != '')
            <td width="30%">
                @php
                $imagePath = storage_path($data['image_path3'] ?? '');
                @endphp
                @if (file_exists($imagePath))
                @php
                $imageData = base64_encode(file_get_contents($imagePath));
                @endphp
                <img src="data:image/jpg;base64,{{ $imageData }}" alt="..tidak ditemukan." style="max-width: 100%;">
                @else
                <img src="" alt="..tidak ditemukan." style="max-width: 100%;">
                @endif
            </td>
            @endif
            <td width="5%">&nbsp;</td>
        </tr>
        <!-- <tr>
            <td width="5%">&nbsp;</td>
            <td width="30%">
                Lampiran 1
            </td>
            <td width="30%">
                Lampiran 2
            </td>
            <td width="30%">
                Lampiran 3
            </td>
            <td width="5%">&nbsp;</td>
        </tr> -->
        <tr>
        </tr>
    </table>

    <table class="content-table" width="100%">

    </table>

    <!-- BREAK PAGE -->
    <div class="page-break"></div>
    <!-- BREAK PAGE -->
    @endforeach
</body>

</html>