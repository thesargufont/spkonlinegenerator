<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Perintah Kerja Intern</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 24px;
        }

        .outline {
            outline: 2px solid #000;
        }

        .header {
            text-align: center;
            font-weight: bold;
            /*margin-bottom: 40px;*/
            font-family: Arial, sans-serif;
            font-size: 24px;
            border: 2px solid black;
            padding: 15px;
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
            width: 100px;
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
            text-align: center;
            /* Aligns text to center */
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
                <td width="35%" style="text-align: left; padding-left:20px">
                    <div>PLN (Persero) UIP2B</div>
                    <div>UP2B JATENG & DIY</div>
                </td>
                <td width="65%" style="text-align: center; font-size: 28px;">
                    <div>SURAT PERINTAH KERJA INTERN</div>
                    <div>( SPKI )</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="bodyBorder">
        <table class="content-table">
            <tr>
                <td class="fontBold">NOMOR </td>
                <td style="width: 10px">:</td>
                <td>{{$data['spk_number']}}</td>
            </tr>
            <!-- <tr>
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
            </tr> -->
        </table>

        <div><br><br></div>
        <table class="content-table">
            <tr>
                <td class="fontBold">PEKERJAAN</td>
            </tr>
        </table>

        <table class="content-table">
            <tr>
                <td>&nbsp;</td>
                <td style="width: 25px">1.</td>
                <td style="width: 330px">Tanggal Mulai</td>
                <td style="width: 10px">:</td>
                <td>{{$data['start_at']}}</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>2.</td>
                <td>Estimasi Selesai</td>
                <td style="width: 10px">:</td>
                <td>{{$data['estimated_end']}}</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>3.</td>
                <td>Nama Pekerjaan</td>
                <td style="width: 10px">:</td>
                <td>{{$data['job_description']}}</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>4.</td>
                <td>Lokasi</td>
                <td style="width: 10px">:</td>
                <td>{{$data['location']}}</td>
            </tr>
            <!-- <tr>
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
            </tr> -->
            <tr>
                <td>&nbsp;</td>
                <td>5.</td>
                <td>Pelaksana Pekerjaan</td>
                <td style="width: 10px">:</td>
                <td>{{$data['engineer']}}</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>6.</td>
                <td>Pengawas Pekerjaan</td>
                <td style="width: 10px">:</td>
                <td>{{$data['supervisor']}}</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>7.</td>
                <td>Pengawas K3</td>
                <td style="width: 10px">:</td>
                <td>{{$data['aid']}}</td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td>8.</td>
                <td>Keterangan</td>
                <td style="width: 10px">:</td>
                <td></td>
            </tr>

        </table>

        <div>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
        </div>

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
                <td style="text-align: center; width: 40%;">
                    @if($data['executor_signature_path'] != '')
                    @php
                    $imagePath = storage_path($data['executor_signature_path'] ?? '');
                    @endphp
                    @if (file_exists($imagePath))
                    @php
                    $imageData = base64_encode(file_get_contents($imagePath));
                    @endphp
                    <div style="display: flex; justify-content: center; align-items: center;">
                        <img src="data:image/png;base64,{{ $imageData }}" alt="..tidak ditemukan." style="max-width: 100%; display: block; margin: 0 auto;">
                    </div>
                    @else
                    <!-- <img src="" alt="..tidak ditemukan." style="max-width: 100%;"> -->
                     <br><br>
                    <span>Dokumen ini telah ditandatangani secara komputerisasi oleh <strong>{{$data['engineer']}}</strong> pada tanggal {{$data['approve_at']}}</span>
                    @endif
                    @else
                    <br><br>
                    <span>Dokumen ini telah ditandatangani secara komputerisasi oleh <strong>{{$data['engineer']}}</strong> pada tanggal {{$data['approve_at']}}</span>
                    @endif
                    <!--                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('/images/pln_logo.png'))) }}" alt="tidak ditemukan..." width="125%" style="filter: grayscale(100%);">-->
                </td>
                <td style="text-align: center; width: 40%;">
                    @if($data['supervisor_signature_path'] != '')
                        @php
                        $imagePath = storage_path($data['supervisor_signature_path'] ?? '');
                        @endphp
                        @if (file_exists($imagePath))
                            @php
                            $imageData = base64_encode(file_get_contents($imagePath));
                            @endphp
                            <div style="display: flex; justify-content: center; align-items: center;">
                                <img src="data:image/png;base64,{{ $imageData }}" alt="..tidak ditemukan." style="max-width: 100%; display: block; margin: 0 auto;">
                            </div>
                        @else
                            <!-- <img src="" alt="..tidak ditemukan." style="max-width: 100%;"> -->
                            <br><br>
                                <span>Dokumen ini telah ditandatangani secara komputerisasi</span>
                                <span>oleh <strong>{{$data['supervisor']}}</strong></span> 
                                <span>pada tanggal {{$data['approve_at']}}</span>
                        @endif
                    @else
                    <br><br>
                        <span>Dokumen ini telah ditandatangani secara komputerisasi</span>
                        <span>oleh <strong>{{$data['supervisor']}}</strong></span> 
                        <span>pada tanggal {{$data['approve_at']}}</span>
                    @endif
                    <!--                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('/images/pln_logo.png'))) }}" alt="tidak ditemukan" width="125%" style="filter: grayscale(100%);">-->

                </td>
            </tr>
            <tr>
                <td><strong>{{$data['engineer']}}</strong></td>
                <td><strong>{{$data['supervisor']}}</strong></td>
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