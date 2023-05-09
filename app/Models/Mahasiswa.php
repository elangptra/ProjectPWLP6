<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\Mahasiswa as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model; //Model Eloquent
use App\Models\Kelas;


class Mahasiswa extends Model
{
    protected $table = "mahasiswa"; // Eloquent akan membuat model mahasiswa menyimpan record di tabel mahasiswas
    public $timestamps = false;
    protected $primaryKey = 'Nim';
    protected $fillable = [
        'Nim',
        'Nama',
        'Foto',
        'Email',
        'kelas_id', 
        'Jurusan', 
        'No_Handphone',
        'TanggalLahir',
    ];

    public function kelas(){
        return $this->belongsTo(kelas::class);
    }

    public function matakuliah(){
        return $this->hasMany(MataKuliah::class, 'mahasiswa_matakuliah','mahasiswa_id', 'matakuliah_id')->withPivot('nilai');
    }
}