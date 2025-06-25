<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class DataJumlahPtj extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    protected $fillable = [
        'data_ptj_id',
        'tahun_id',        
        'is_kpi',
        'pi_no',
        'pi_target',
        'jumlah',
    ];

    public function dataPtj()
    {
        return $this->belongsTo(DataPtj::class);
    }

    public function tahun()
    {
        return $this->belongsTo(Tahun::class);
    }
}
