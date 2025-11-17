<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompanySubscriptionController extends Controller
{
    public function store(Request $request, Company $company): RedirectResponse
    {
        $validated = $request->validate([
            'notify_recommendations' => 'boolean',
            'notify_updates' => 'boolean',
            'notify_news' => 'boolean',
            'notify_price_alerts' => 'boolean',
        ]);

        auth()->user()->companySubscriptions()->updateOrCreate(
            ['company_id' => $company->id],
            $validated
        );

        return back()->with('success', __('companies.subscription_updated'));
    }

    public function destroy(Company $company): RedirectResponse
    {
        auth()->user()->companySubscriptions()
            ->where('company_id', $company->id)
            ->delete();

        return back()->with('success', __('companies.unsubscribed'));
    }
}
