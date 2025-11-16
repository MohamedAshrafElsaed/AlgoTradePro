<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompanySubscriptionController extends Controller
{
    /**
     * Subscribe to company notifications
     */
    public function store(Request $request, Company $company): RedirectResponse
    {
        $validated = $request->validate([
            'notify_recommendations' => 'boolean',
            'notify_updates' => 'boolean',
            'notify_news' => 'boolean',
            'notify_price_alerts' => 'boolean',
        ]);

        $request->user()->subscribedCompanies()->syncWithoutDetaching([
            $company->id => [
                'notify_recommendations' => $validated['notify_recommendations'] ?? true,
                'notify_updates' => $validated['notify_updates'] ?? true,
                'notify_news' => $validated['notify_news'] ?? false,
                'notify_price_alerts' => $validated['notify_price_alerts'] ?? false,
            ]
        ]);

        return back()->with('message', __('companies.subscription_updated'));
    }

    /**
     * Unsubscribe from company notifications
     */
    public function destroy(Request $request, Company $company): RedirectResponse
    {
        $request->user()->subscribedCompanies()->detach($company->id);

        return back()->with('message', __('companies.unsubscribed'));
    }
}
