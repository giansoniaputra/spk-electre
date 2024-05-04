<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubKriteria extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function get_bobot($kriteria_uuid)
    {
        $query = SubKriteria::where('kriteria_uuid', $kriteria_uuid)->where('is_bobot', 1)->first();
        if ($query) {
            $is_bobot = $query->bobot;
        } else {
            $is_bobot = 0;
        }
        return $is_bobot;
    }
    public function getSub($kriteria_uuid)
    {
        return SubKriteria::where('kriteria_uuid', $kriteria_uuid)->get();
    }
}
