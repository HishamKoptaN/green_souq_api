<?php

namespace App\Features\Jobs\Data\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $fillable = ['title', 'description', 'location', 'company_name', 'status'];
}
