<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Template</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .container {
            margin: 20px;
        }

        .header,
        .footer {
            text-align: center;
        }

        .header img {
            width: 50px;
        }

        .header table,
        .content table {
            width: 100%;
            border-collapse: collapse;
        }

        .header td {
            padding: 5px;
        }

        .content table,
        .content th,
        .content td {
            border: 1px solid black;
            padding: 5px;
            text-align: left;
        }

        .content th {
            background-color: #f2f2f2;
        }

        .footer {
            margin-top: 20px;
        }

        .footer td {
            padding: 5px;
        }

        .small-text {
            font-size: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <table width="100%">
                <tr>
                    <td width="10%">
                        <link rel="shortcut icon" href="{{ asset('images/pln_logo.png') }}">
                    </td>
                    <td width="50%" style="text-align: center;">
                        <div>PLN (Persero) UIP2B</div>
                        <div>UP2B JAWA TENGAH & DIY</div>
                        <div>JL. JENDRAL SUDIRMAN KM. 23 UNGARAN</div>
                        <div>SISTEM MANAJEMEN TERINTEGRASI</div>
                    </td>
                    <td width="10%" style="text-align: left;">
                        <div>No. Dokumen</div>
                        <div>Berlaku Efektif</div>
                        <div>Revisi</div>
                        <div>Halaman</div>
                    </td>
                    <td width="30%" style="text-align: left;">
                        <div>: 01/FR/JTD-FSO-TEL</div>
                        <div>: 01 Oktober 2021</div>
                        <div>: 00</div>
                        <div>: 1/2</div>
                    </td>
                </tr>
                <tr>
                    <td width="100%" style="text-align: center;">
                        <div>BERITA ACARA PEKERJAAN</div>
                    </td>
                </tr>
            </table>
            <!-- <table>
                <tr>
                    <td rowspan="3"><img src="{{ asset('images/pln_logo.png') }}" alt="PLN Logo"></td>
                    <td><strong>PT PLN (Persero)</strong></td>
                    <td>No. Dokumen : 01/FR/JTD-FSO-TEL</td>
                </tr>
                <tr>
                    <td>UP2B JAWA TENGAH & DIY</td>
                    <td>Berlaku Efektif : 01 Oktober 2021</td>
                </tr>
                <tr>
                    <td>JL. JENDRAL SUDIRMAN KM. 23 UNGARAN</td>
                    <td>Revisi : 00</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: center;"><strong>SISTEM MANAJEMEN TERINTEGRASI</strong></td>
                    <td>Halaman : 1/2</td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center;"><strong>BERITA ACARA PEKERJAAN</strong></td>
                </tr>
            </table> -->
        </div>

        <br>

        <div class="content">
            <table>
                <tr>
                    <td>GI 150KV KUDUS</td>
                </tr>
                <tr>
                    <td>NO WP</td>
                    <td>: 001/WP</td>
                </tr>
                <tr>
                    <td>NO SPK</td>
                    <td>: 00002/WO/TEL/05/2024</td>
                </tr>
                <tr>
                    <td>KATEGORI PEKERJAAN</td>
                    <td>: PERBAIKAN</td>
                </tr>
            </table>

            <br>

            <p>Pada hari ini <strong>Rabu, 22-05-2024</strong> di <strong>GI 150KV KUDUS</strong>, telah dilakukan pekerjaan <strong>Perbaikan</strong> dengan detail sebagai berikut:</p>

            <table>
                <tr>
                    <th>Alat</th>
                    <th>Brand</th>
                    <th>Nomor Seri</th>
                    <th>EQ ID</th>
                    <th>Kategori Alat</th>
                    <th>Engineer</th>
                    <th>Supervisor</th>
                    <th>Deskripsi WO</th>
                    <th>Deskripsi Tugas</th>
                </tr>
                <tr>
                    <td>Router/Switch</td>
                    <td>Siemens SWT 3000</td>
                    <td>BF1503507681</td>
                    <td>A-JTG2774</td>
                    <td>Teleprotection</td>
                    <td>ENGINEER 1</td>
                    <td>SPV 1</td>
                    <td>Device tidak bisa menyala</td>
                    <td>Ganti Baru</td>
                </tr>
            </table>

            <br>

            <p>Pekerjaan <strong>Perbaikan</strong> sudah selesai, hasil pengujian fungsi sudah sesuai.</p>
            <p>Demikian Berita Acara ini di buat untuk dapat digunakan sebaik baiknya.</p>
        </div>

        <br><br>

        <div class="footer">
            <table>
                <tr>
                    <td>PT. PLN (Persero)</td>
                    <td></td>
                    <td>PT. PLN (Persero)</td>
                </tr>
                <tr>
                    <td>UP2B Jateng & DIY</td>
                    <td></td>
                    <td>ULTG Surakarta</td>
                </tr>
                <tr>
                    <td>
                        <p>Dokumen ini telah ditandatangani secara komputerisasi oleh ENGINEER 2 pada tanggal 22/05/2024</p>
                    </td>
                    <td></td>
                    <td>(</td>
                </tr>
                <tr>
                    <td><strong>ENGINEER</strong></td>
                    <td></td>
                    <td>)</td>
                </tr>
            </table>

            <br>

            <p class="small-text">Dokumen ini milik PT. PLN (Persero) UIP2B JAMALI<br>Dilarang mengubah atau memperbanyak dokumen kepada pihak lain tanpa seijin dari IVMT</p>
        </div>
    </div>
</body>

</html>