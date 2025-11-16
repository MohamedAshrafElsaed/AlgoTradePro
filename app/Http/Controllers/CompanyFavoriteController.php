<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;


class CompanyFavoriteController extends Controller
{
    /**
     * Add company to favorites
     */
    public function store(Request $request, Company $company): RedirectResponse
    {
        $request->user()->favoriteCompanies()->syncWithoutDetaching($company->id);

        return back()->with('message', __('companies.favorite_added'));
    }

    /**
     * Remove company from favorites
     */
    public function destroy(Request $request, Company $company): RedirectResponse
    {
        $request->user()->favoriteCompanies()->detach($company->id);

        return back()->with('message', __('companies.favorite_removed'));
    }
}
