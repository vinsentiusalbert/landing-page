<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRegistrationRequest;
use App\Models\Registration;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function create(): View
    {
        return view('registration', [
            'businessTypes' => Registration::BUSINESS_TYPES,
            'facilities' => Registration::FACILITIES,
        ]);
    }

    public function store(StoreRegistrationRequest $request): RedirectResponse
    {
        Registration::create($request->validated());

        return to_route('registration.create')->with('registered', true);
    }
}
