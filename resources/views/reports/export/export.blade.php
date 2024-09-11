<table border="1" width="100%" style="border-collapse: collapse;">
    <thead>
    <tr>
        <td>{{ucwords(__('title'))}}</td>
        <td></td>
        <td>{{strtoupper(__('Report'))}}</td>
    </tr>
    <tr>
        <td>{{__('Downloaded by')}}</td>
        <td></td>
        <td>{{strtoupper(__($auth['email']))}}</td>
    </tr>
    <tr>
        <td>{{__('Date downloaded')}}</td>
        <td></td>
        <td>{{$date}}</td>
    </tr>
    <tr>
        <th>No</th>
        <th>Nomor WO</th>
        <th>Nomor SPK</th>
        <th>Nomor BA</th>
        <th>Nomor WP</th>
        <th>Departemen</th>
        <th>Kategori WO</th>
        <th>Kategori Pekerjaan</th>
        <th>Lokasi</th>
        <th>Alat</th>
        <th>Kategori Gangguan</th>
        <th>Deskripsi WO</th>
        <th>Deskripsi Pekerjaan</th>
        <th>Engineer</th>
        <th>Supervisor</th>
        <th>Progress Engineer</th>
        <th>Deskripsi Engineer</th>
        <th>Status</th>
        <th>Dibuat Oleh</th>
        <th>Tgl Efektif WO</th>
        <th>Disetujui Oleh</th>
        <th>Disetujui Pada</th>
        <th>Mulai Pada</th>
        <th>Tgl Estimasi Selesai</th>
        <th>Tgl Closed</th>
        <th>Tgl Cancel</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($items as $row)
        <tr>
            <td>{{$row['no']}}</td>
            <td>{{$row['wo_number']}}</td>
            <td>{{$row['spk_number']}}</td>
            <td>{{$row['ba_number']}}</td>
            <td>{{$row['wp_number']}}</td>
            <td>{{$row['department']}}</td>
            <td>{{$row['wo_type']}}</td>
            <td>{{$row['job_category']}}</td>
            <td>{{$row['location']}}</td>
            <td>{{$row['device_name']}}</td>
            <td>{{$row['disturbance_category']}}</td>
            <td>{{$row['wo_description']}}</td>
            <td>{{$row['job_description']}}</td>
            <td>{{$row['engineer']}}</td>
            <td>{{$row['supervisor']}}</td>
            <td>{{$row['engineer_progress']}}</td>
            <td>{{$row['engineer_description']}}</td>
            <td>{{$row['status']}}</td>
            <td>{{$row['created_by']}}</td>
            <td>{{$row['effective_date']}}</td>
            <td>{{$row['approve_by']}}</td>
            <td>{{$row['approve_at']}}</td>
            <td>{{$row['start_at']}}</td>
            <td>{{$row['estimated_end']}}</td>
            <td>{{$row['close_at']}}</td>
            <td>{{$row['cancelled_at']}}</td>
    @endforeach
    </tbody>
</table>

