<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Department extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    protected $fillable = [
        'name',
        'publish_status'
    ];

    public function getPublishStatusAttribute()
    {
        return $this->attributes['publish_status'] ? 'Aktif' : 'Tidak Aktif';
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function subunits()
    {
        return $this->hasMany(SubUnit::class);
    }

    public function jenisDataPtj()
    {
        return $this->hasMany(JenisDataPtj::class);
    }

    public function dataUtama()
    {
        return $this->hasMany(DataUtama::class);
    }

    public function dataPtj()
    {
        return $this->hasMany(DataPtj::class);
    }
}
