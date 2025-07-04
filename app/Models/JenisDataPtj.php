<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class JenisDataPtj extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    protected $table = 'jenis_data_ptjs';

    protected $fillable = [
        'name',
        'department_id',
        'subunit_id',
        'publish_status',
    ];

    public function getPublishStatusAttribute()
    {
        return $this->attributes['publish_status'] ? 'Aktif' : 'Tidak Aktif';
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function subunit()
    {
        return $this->belongsTo(SubUnit::class);
    }

    public function dataUtama()
    {
        return $this->hasMany(DataUtama::class);
    }

    public static function boot()
    {
        parent::boot();

        logger('Table used: ' . (new static)->getTable());
    }
}
