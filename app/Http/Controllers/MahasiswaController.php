<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Mahasiswa_Matakuliah;
use App\Models\MataKuliah;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\PDF;

class MahasiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $mahasiswa = Mahasiswa::paginate(5);
        $posts = Mahasiswa::orderBy('Nim', 'desc')->paginate(5);
        return view('mahasiswa.index', compact('mahasiswa'))
        ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $kelas = Kelas::all(); //mendapatkan data dari tabel kelas
        return view('mahasiswa.create',['kelas' => $kelas]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //melakukan validasi data
        $request->validate([
            'Nim' => 'required',
            'Nama' => 'required',
            'foto' => 'required',
            'Email' => 'required',
            'kelas' => 'required',
            'Jurusan' => 'required',
            'No_Handphone' => 'required',
            'TanggalLahir' => 'required',
        ]);

        if ($request->file('foto')) {
            $image_name = $request->file('foto')->store('images','public');
        }

        //fungsi eloquent untuk menambah data
        $mahasiswa = new Mahasiswa;
        $mahasiswa->Nim=$request->get('Nim');
        $mahasiswa->Nama=$request->get('Nama');
        $mahasiswa->Foto=$image_name;
        $mahasiswa->Email=$request->get('Email');
        $mahasiswa->Jurusan=$request->get('Jurusan');
        $mahasiswa->No_Handphone=$request->get('No_Handphone');
        $mahasiswa->TanggalLahir=$request->get('TanggalLahir');

        //fungsi eloquent untuk menambah data dengan relasi belongs to
        $kelas = new Kelas;
        $kelas->id = $request->get('kelas');

        $mahasiswa->kelas()->associate($kelas);
        $mahasiswa->save();

        //jika data berhasil ditambahakan, akan kembali ke halaman utama
        return redirect()->route('mahasiswa.index')
            ->with('success', 'Mahasiswa Berhasil Ditambahkan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($Nim)
    {
        //menampilkan detail data dengan menemukan/berdasarkan Nim Mahasiswa
        $Mahasiswa = Mahasiswa::find($Nim);
        return view('mahasiswa.detail', compact('Mahasiswa'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($Nim)
    {
        //menampilkan detail data dengan menemukan/berdasarkan Nim Mahasiswa
        $Mahasiswa = Mahasiswa::find($Nim);
        $user = Auth::user();
        $kelas = Kelas::all();
        return view('mahasiswa.edit', compact('Mahasiswa', 'user', 'kelas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $Nim)
    {
        //melakukan validasi data
        $request->validate([
            'Nim' => 'required',
            'Nama' => 'required',
            'Email' => 'required',
            'kelas' => 'required',
            'Jurusan' => 'required',
            'No_Handphone' => 'required',
            'TanggalLahir' => 'required',
        ]);

         //fungsi eloquent untuk menambah data
         $mahasiswa = Mahasiswa::find($Nim);
         if ($mahasiswa->Foto && file_exists(storage_path('app/public/' .$mahasiswa->Foto))) {
            Storage::delete('public/' .$mahasiswa->Foto);
         }
         $image_name = $request->file('Foto')->store('images', 'public');
         $mahasiswa->Nim=$request->get('Nim');
         $mahasiswa->Nama=$request->get('Nama');
         $mahasiswa->Foto=$image_name;
         $mahasiswa->Email=$request->get('Email');
         $mahasiswa->Jurusan=$request->get('Jurusan');
         $mahasiswa->No_Handphone=$request->get('No_Handphone');
         $mahasiswa->TanggalLahir=$request->get('TanggalLahir');
 
         //fungsi eloquent untuk menambah data dengan relasi belongs to
         $kelas = new Kelas;
         $kelas->id = $request->get('kelas');
 
         $mahasiswa->kelas()->associate($kelas);
         $mahasiswa->save();
 
         //jika data berhasil ditambahakan, akan kembali ke halaman utama
         return redirect()->route('mahasiswa.index')
             ->with('success', 'Mahasiswa Berhasil Diupdate');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($Nim)
    {
        //fungsi eloquent untuk menghapus data
        Mahasiswa::find($Nim)->delete();
        return redirect()->route('mahasiswa.index')
            ->with('success', 'Mahasiswa Berhasil Dihapus');
    }

    public function search(Request $request){
        $keyword = $request->search;
        $mahasiswa = Mahasiswa::where('Nama', 'like', "%". $keyword . "%")->paginate(5);
        return view(view:'mahasiswa.index', data:compact(var_name:'mahasiswa'));
    }

    public function nilai($Nim)
    {
        //$Mahasiswa = Mahasiswa::find($nim);
        $Mahasiswa = Mahasiswa::find($Nim);
        $Matakuliah = Matakuliah::all();
        //$MataKuliah = $Mahasiswa->MataKuliah()->get();
        $Mahasiswa_Matakuliah = Mahasiswa_Matakuliah::where('mahasiswa_id','=',$Nim)->get();
        return view('mahasiswa.nilai',['mahasiswa' => $Mahasiswa],['mahasiswa_matakuliah' => $Mahasiswa_Matakuliah],
        ['matakuliah' => $Matakuliah], compact('Mahasiswa_Matakuliah'));
    }

    public function cetak_pdf($Nim){
        $Mahasiswa = Mahasiswa::find($Nim);
        $Matakuliah = Matakuliah::all();
        $Mahasiswa_Matakuliah = Mahasiswa_Matakuliah::where('mahasiswa_id','=',$Nim)->get();
        $pdf = PDF::loadview('mahasiswa.nilai_pdf', compact('Mahasiswa','Mahasiswa_Matakuliah'));
        return $pdf->stream();
    }
}
