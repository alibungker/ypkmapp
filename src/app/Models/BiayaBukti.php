<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiayaBukti extends Model
{
    protected $table = 'biaya_buktis';
    protected $fillable = ['biaya_id', 'file_path', 'file_name', 'tipe'];

    public function biaya() { return $this->belongsTo(BiayaOperasional::class); }
}
