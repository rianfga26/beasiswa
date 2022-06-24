<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Biodata extends Model
{
    use HasFactory;
    protected $table = 'mhs_bio';
    protected $guarded = [];
    protected $primaryKey = 'nim';

    public function berkas(){
        return $this->hasMany(Berkas::class, 'nim', 'nim');
    }
}
