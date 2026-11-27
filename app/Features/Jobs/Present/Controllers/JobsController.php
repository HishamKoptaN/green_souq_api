<?php

namespace App\Features\Jobs\Present\Controllers;

use App\Features\Jobs\Data\Models\Job;
use App\Features\Jobs\Present\Resources\JobResource;
use Illuminate\Http\Request;

class JobsController
{
    public function index(Request $request)
    {
        $query = Job::where('status', 'open');

        if ($request->filled('query')) {
            $search = '%'.$request->query('query').'%';
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', $search)
                    ->orWhere('company_name', 'like', $search)
                    ->orWhere('location', 'like', $search);
            });
        }

        return paginateRes(
            $query->latest()->paginate(15),
            JobResource::class,
        );
    }
}
