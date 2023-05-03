@extends('mahasiswa.layout')
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left mt-2">
                <h2>JURUSAN TEKNOLOGI INFORMASI-POLITEKNIK NEGERI MALANG</h2>
            </div>
            <h2>
                <center>KARTU HASIL STUDI (KHS)</center>
            </h2>
        </div>
    </div>

    <h4>Nama : {{ $mahasiswa->Nama }}</h4>
    <h4>NIM : {{ $mahasiswa->Nim }}</h4>
    <h4>Kelas : {{ $mahasiswa->Kelas->nama_kelas }}</h4>
    <table class="table table-bordered">
        <tr>
            <th>Mata Kuliah</th>
            <th>SKS</th>
            <th>Semester</th>
            <th>Nilai</th>
        </tr>
        @foreach ($mahasiswa_matakuliah as $mahasiswa_matkul)
            <tr>
                <td>{{ $mahasiswa_matkul->matakuliah->nama_matkul }}</td>
                <td>{{ $mahasiswa_matkul->matakuliah->sks }}</td>
                <td>{{ $mahasiswa_matkul->matakuliah->semester }}</td>
                <td>{{ $mahasiswa_matkul->nilai }}</td>
            </tr>
        @endforeach
    </table>
@endsection