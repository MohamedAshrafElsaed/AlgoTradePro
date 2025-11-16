<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CompanyController extends Controller
{
    /**
     * Display a listing of companies
     */
    public function index(Request $request): Response
    {
        $query = Company::with(['companyType', 'statistics'])
            ->when($request->search, function ($q) use ($request) {
                $q->where('symbol', 'like', "%{$request->search}%")
                    ->orWhere('name_en', 'like', "%{$request->search}%")
                    ->orWhere('name_ar', 'like', "%{$request->search}%");
            })
            ->when($request->type, function ($q) use ($request) {
                $q->where('company_type_id', $request->type);
            });

        $companies = $query->paginate(15)->through(function ($company) use ($request) {
            return [
                'id' => $company->id,
                'symbol' => $company->symbol,
                'name_en' => $company->name_en,
                'name_ar' => $company->name_ar,
                'current_price' => $company->current_price,
                'price_change' => $company->price_change,
                'change_percentage' => $company->change_percentage,
                'type' => [
                    'name_en' => $company->companyType->name_en,
                    'name_ar' => $company->companyType->name_ar,
                    'slug' => $company->companyType->slug,
                ],
                'is_favorited' => $company->isFavoritedBy($request->user()?->id),
            ];
        });

        $types = CompanyType::all()->map(function ($type) {
            return [
                'id' => $type->id,
                'name_en' => $type->name_en,
                'name_ar' => $type->name_ar,
                'slug' => $type->slug,
            ];
        });

        return Inertia::render('companies/Index', [
            'companies' => $companies,
            'types' => $types,
            'filters' => [
                'search' => $request->search,
                'type' => $request->type,
            ],
        ]);
    }

    /**
     * Display user's favorite companies
     */
    public function favorites(Request $request): Response
    {
        $companies = $request->user()
            ->favoriteCompanies()
            ->with(['companyType', 'statistics'])
            ->paginate(15)
            ->through(function ($company) {
                return [
                    'id' => $company->id,
                    'symbol' => $company->symbol,
                    'name_en' => $company->name_en,
                    'name_ar' => $company->name_ar,
                    'current_price' => $company->current_price,
                    'price_change' => $company->price_change,
                    'change_percentage' => $company->change_percentage,
                    'type' => [
                        'name_en' => $company->companyType->name_en,
                        'name_ar' => $company->companyType->name_ar,
                        'slug' => $company->companyType->slug,
                    ],
                    'is_favorited' => true,
                ];
            });

        return Inertia::render('companies/Favorites', [
            'companies' => $companies,
        ]);
    }

    /**
     * Display company profile
     */
    public function show(Request $request, Company $company): Response
    {
        $company->load(['companyType', 'statistics', 'news' => function ($query) {
            $query->latest('published_at')->take(5);
        }]);

        $relatedCompanies = Company::with('companyType')
            ->where('company_type_id', $company->company_type_id)
            ->where('id', '!=', $company->id)
            ->take(4)
            ->get()
            ->map(function ($related) {
                return [
                    'id' => $related->id,
                    'symbol' => $related->symbol,
                    'name_en' => $related->name_en,
                    'name_ar' => $related->name_ar,
                    'current_price' => $related->current_price,
                ];
            });

        $subscription = null;
        if ($request->user()) {
            $sub = $request->user()->subscribedCompanies()
                ->where('company_id', $company->id)
                ->first();

            if ($sub) {
                $subscription = [
                    'notify_recommendations' => (bool) $sub->pivot->notify_recommendations,
                    'notify_updates' => (bool) $sub->pivot->notify_updates,
                    'notify_news' => (bool) $sub->pivot->notify_news,
                    'notify_price_alerts' => (bool) $sub->pivot->notify_price_alerts,
                ];
            }
        }

        return Inertia::render('companies/Show', [
            'company' => [
                'id' => $company->id,
                'symbol' => $company->symbol,
                'name_en' => $company->name_en,
                'name_ar' => $company->name_ar,
                'current_price' => $company->current_price,
                'price_change' => $company->price_change,
                'change_percentage' => $company->change_percentage,
                'description_en' => $company->description_en,
                'description_ar' => $company->description_ar,
                'ceo' => $company->ceo,
                'headquarter_en' => $company->headquarter_en,
                'headquarter_ar' => $company->headquarter_ar,
                'type' => [
                    'name_en' => $company->companyType->name_en,
                    'name_ar' => $company->companyType->name_ar,
                    'slug' => $company->companyType->slug,
                ],
                'statistics' => $company->statistics ? [
                    'market_cap' => $company->statistics->market_cap,
                    'value_today' => $company->statistics->value_today,
                    'adtv_6m' => $company->statistics->adtv_6m,
                    'eps' => $company->statistics->eps,
                    'pe_ratio' => $company->statistics->pe_ratio,
                    'dividend_yield' => $company->statistics->dividend_yield,
                    'week_52_high' => $company->statistics->week_52_high,
                    'week_52_low' => $company->statistics->week_52_low,
                ] : null,
                'news' => $company->news->map(function ($news) {
                    return [
                        'id' => $news->id,
                        'title_en' => $news->title_en,
                        'title_ar' => $news->title_ar,
                        'source' => $news->source,
                        'url' => $news->url,
                        'published_at' => $news->published_at->diffForHumans(),
                    ];
                }),
                'is_favorited' => $company->isFavoritedBy($request->user()?->id),
                'is_subscribed' => $company->isSubscribedBy($request->user()?->id),
            ],
            'related_companies' => $relatedCompanies,
            'subscription' => $subscription,
        ]);
    }
}
