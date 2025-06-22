<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class DataJumlah extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    protected $fillable = [
        'data_utama_id',
        'tahun_id',
        'jumlah',
    ];

    public function dataUtama()
    {
        return $this->belongsTo(DataUtama::class);
    }

    public function tahun()
    {
        return $this->belongsTo(Tahun::class);
    }
}
