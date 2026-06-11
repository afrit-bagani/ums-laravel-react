<?php

namespace App\Models\Admin;

use Database\Factories\Admin\ProgrammeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programme extends Model
{
    /** @use HasFactory<ProgrammeFactory> */
    use HasFactory;

    protected $primaryKey = 'programme_id';

    protected $table = 'programme_master';

    protected $guarded = [];
}
