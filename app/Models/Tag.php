<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable= ['bane_tag', 'slug'];
     public function berita()
    {
        return $this->belongsTo(Berita::class, 'id_berita', 'id_tag', 'tag_berita');
    }
}
