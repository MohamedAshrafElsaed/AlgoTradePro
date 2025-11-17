<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\RedirectResponse;

class CompanyFavoriteController extends Controller
{
    public function store(Company $company): RedirectResponse
    {
        auth()->user()->favoriteCompanies()->syncWithoutDetaching([$company->id]);

        return back()->with('success', __('companies.added_to_favorites'));
    }

    public function destroy(Company $company): RedirectResponse
    {
        auth()->user()->favoriteCompanies()->detach($company->id);

        return back()->with('success', __('companies.removed_from_favorites'));
    }
}
