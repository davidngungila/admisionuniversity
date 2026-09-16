<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Applicant;
use Illuminate\Http\Request;

class ApplicantController extends Controller
{
    public function index(Request $request)
    {
        $q = Applicant::with(['user','citizenship'])->latest();

        if ($request->filled('search')) {
            $q->where(function ($query) use ($request) {
                $query->where('first_name','like','%'.$request->search.'%')
                    ->orWhere('last_name','like','%'.$request->search.'%')
                    ->orWhere('email','like','%'.$request->search.'%')
                    ->orWhere('applicant_number','like','%'.$request->search.'%');
            });
        }

        return view('admin.applicants.index', ['applicants'=>$q->paginate(20)->withQueryString()]);
    }

    public function show(Applicant $applicant)
    {
        $applicant->load(['user','citizenship','addresses.region','addresses.district','addresses.ward','applications.admissionWindow.admissionLevel','applications.admissionWindow.applicationRound','applications.academicYear']);

        return view('admin.applicants.show', compact('applicant'));
    }
}