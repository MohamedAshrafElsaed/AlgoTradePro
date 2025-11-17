<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CompanyController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Company::with(['type', 'statistics', 'recommendation'])
            ->orderBy('symbol');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('symbol', 'like', "%{$search}%")
                    ->orWhere('name_en', 'like', "%{$search}%")
                    ->orWhere('name_ar', 'like', "%{$search}%");
            });
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('company_type_id', $request->type);
        }

        $companies = $query->paginate(24)->withQueryString();

        // Get user's favorited company IDs
        $favoritedIds = auth()->user()
            ->favoriteCompanies()
            ->pluck('company_id')
            ->toArray();

        // Add is_favorited to each company
        $companies->getCollection()->transform(function ($company) use ($favoritedIds) {
            $company->is_favorited = in_array($company->id, $favoritedIds);
            return $company;
        });

        return Inertia::render('companies/Index', [
            'companies' => $companies,
            'types' => CompanyType::all(),
            'filters' => [
                'search' => $request->search,
                'type' => $request->type,
            ],
        ]);
    }

    public function show(Company $company): Response
    {
        $company->load([
            'type',
            'statistics',
            'news' => fn($q) => $q->latest('published_at')->limit(10),
            'recommendation',
            'analystRatings' => fn($q) => $q->latest('rating_date')->limit(5),
            'earnings' => fn($q) => $q->latest('earnings_date')->limit(5),
            'dividends' => fn($q) => $q->latest('ex_date')->limit(5),
            'splits' => fn($q) => $q->latest('split_date')->limit(5),
            'timeSeries' => fn($q) => $q->where('interval', '1day')
                ->where('date', '>=', now()->subDays(30))
                ->orderBy('date', 'desc'),
            'technicalIndicators' => fn($q) => $q->where('interval', '1day')
                ->latest('date')
                ->limit(1),
            'financials' => fn($q) => $q->where('period', 'annual')
                ->latest('fiscal_date')
                ->limit(5),
        ]);

        // Check if favorited
        $company->is_favorited = auth()->user()
            ->favoriteCompanies()
            ->where('company_id', $company->id)
            ->exists();

        // Get subscription
        $subscription = auth()->user()
            ->subscribedCompanies()
            ->where('company_id', $company->id)
            ->first();

        $company->is_subscribed = $subscription !== null;

        // Get related companies (same type)
        $relatedCompanies = Company::where('company_type_id', $company->company_type_id)
            ->where('id', '!=', $company->id)
            ->limit(4)
            ->get(['id', 'symbol', 'name_en', 'name_ar', 'current_price']);

        return Inertia::render('companies/Show', [
            'company' => $company,
            'subscription' => $subscription,
            'related_companies' => $relatedCompanies,
        ]);
    }

    public function favorites(): Response
    {
        $companies = auth()->user()
            ->favoriteCompanies()
            ->with(['type', 'statistics'])
            ->paginate(24);

        // All are favorited by definition
        $companies->getCollection()->transform(function ($company) {
            $company->is_favorited = true;
            return $company;
        });

        return Inertia::render('companies/Favorites', [
            'companies' => $companies,
        ]);
    }
}
