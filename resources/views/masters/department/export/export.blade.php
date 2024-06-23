
<table border="1" width="100%" style="border-collapse: collapse;">
    <thead>
    <tr>
          <td>{{ucwords(__('title'))}}</td>
          <td></td>
          <td>{{strtoupper(__('Data Master Bagian'))}}</td>
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
            <th>Bagian</th>
            <th>Deskripsi</th>
            <th>Status</th>
            <th>Start Effective</th>
            <th>End Effective</th>
            <th>Dibuat Oleh</th>
            <th>Dibuat Pada</th>
            <th>Diubah Oleh</th>
            <th>Diubah Pada</th>
        </tr>
    </thead>
    <tbody>
      @foreach ($items as $row)   
        <tr>
            <td>{{$row['no']}}</td>
            <td>{{ $row['department'] }}</td>
            <td>{{ $row['department_description'] }}</td>
            <td>{{ $row['active'] }}</td>
            <td>{{ $row['start_effective'] }}</td>
            <td>{{ $row['end_effective'] }}</td>
            <td>{{ $row['created_by'] }}</td>
            <td>{{ $row['created_at'] }}</td>
            <td>{{ $row['updated_by'] }}</td>
            <td>{{ $row['updated_at'] }}</td>
      @endforeach
    </tbody>
  </table>
    