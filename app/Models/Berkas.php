<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Berkas extends Model
{
    use HasFactory;
    protected $table = 'mhs_berkas';
    protected $fillable = ['penggunaan_bh', 'nim', 'bukti_pencairan_bh', 'khs', 'prestasi'];

    public function biodata(){
        return $this->belongsTo(Biodata::class, 'nim', 'nim');
    }

}
