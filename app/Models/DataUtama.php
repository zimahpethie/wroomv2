<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class DataUtama extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    protected $fillable = [
        'department_id',
        'subunit_id',
        'jenis_data_ptj_id',
        'doc_link',
        'created_by',
        'updated_by'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function subunit()
    {
        return $this->belongsTo(Subunit::class);
    }

    public function jenisDataPtj()
    {
        return $this->belongsTo(JenisDataPtj::class);
    }

    public function jumlahs()
    {
        return $this->hasMany(DataJumlah::class);
    }
}
