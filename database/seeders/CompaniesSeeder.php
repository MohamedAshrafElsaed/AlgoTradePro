<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyNews;
use App\Models\CompanyStatistic;
use App\Models\CompanyType;
use Illuminate\Database\Seeder;

class CompaniesSeeder extends Seeder
{
    public function run(): void
    {
        // Create company types
        $stockType = CompanyType::create([
            'name_en' => 'Stock',
            'name_ar' => 'سهم',
            'slug' => 'stock',
        ]);

        $mutualFundType = CompanyType::create([
            'name_en' => 'Mutual Fund',
            'name_ar' => 'صندوق استثماري',
            'slug' => 'mutual-fund',
        ]);

        // Create sample companies
        $companies = [
            [
                'company_type_id' => $stockType->id,
                'symbol' => 'AAPL',
                'name_en' => 'Apple Inc.',
                'name_ar' => 'شركة أبل',
                'current_price' => 185.92500,
                'price_change' => 2.45000,
                'change_percentage' => 1.34,
                'description_en' => 'Apple Inc. designs, manufactures, and markets smartphones, personal computers, tablets, wearables, and accessories worldwide.',
                'description_ar' => 'تصمم شركة أبل وتصنع وتسوق الهواتف الذكية وأجهزة الكمبيوتر الشخصية والأجهزة اللوحية والأجهزة القابلة للارتداء والإكسسوارات في جميع أنحاء العالم.',
                'ceo' => 'Tim Cook',
                'headquarter_en' => 'Cupertino, California',
                'headquarter_ar' => 'كوبرتينو، كاليفورنيا',
            ],
            [
                'company_type_id' => $stockType->id,
                'symbol' => 'MSFT',
                'name_en' => 'Microsoft Corporation',
                'name_ar' => 'شركة مايكروسوفت',
                'current_price' => 378.91000,
                'price_change' => -1.23000,
                'change_percentage' => -0.32,
                'description_en' => 'Microsoft Corporation develops, licenses, and supports software, services, devices, and solutions worldwide.',
                'description_ar' => 'تطور شركة مايكروسوفت وترخص وتدعم البرامج والخدمات والأجهزة والحلول في جميع أنحاء العالم.',
                'ceo' => 'Satya Nadella',
                'headquarter_en' => 'Redmond, Washington',
                'headquarter_ar' => 'ريدموند، واشنطن',
            ],
            [
                'company_type_id' => $mutualFundType->id,
                'symbol' => 'VFIAX',
                'name_en' => 'Vanguard 500 Index Fund',
                'name_ar' => 'صندوق فانجارد 500 المؤشر',
                'current_price' => 421.35000,
                'price_change' => 3.12000,
                'change_percentage' => 0.75,
                'description_en' => 'The fund employs an indexing investment approach designed to track the performance of the S&P 500 Index.',
                'description_ar' => 'يستخدم الصندوق نهج استثمار مفهرس مصمم لتتبع أداء مؤشر S&P 500.',
                'ceo' => 'Mortimer J. Buckley',
                'headquarter_en' => 'Valley Forge, Pennsylvania',
                'headquarter_ar' => 'فالي فورج، بنسلفانيا',
            ],
        ];

        foreach ($companies as $companyData) {
            $company = Company::create($companyData);

            // Create statistics
            CompanyStatistic::create([
                'company_id' => $company->id,
                'market_cap' => rand(100000000, 3000000000),
                'value_today' => rand(1000000, 50000000),
                'adtv_6m' => rand(500000, 10000000),
                'eps' => rand(2, 15) + (rand(0, 99) / 100),
                'pe_ratio' => rand(15, 35) + (rand(0, 99) / 100),
                'dividend_yield' => rand(0, 5) + (rand(0, 99) / 100),
                'week_52_high' => $company->current_price * (1 + rand(5, 20) / 100),
                'week_52_low' => $company->current_price * (1 - rand(5, 20) / 100),
            ]);

            // Create news items
            for ($i = 0; $i < 5; $i++) {
                CompanyNews::create([
                    'company_id' => $company->id,
                    'title_en' => "Latest update about {$company->name_en} - Story " . ($i + 1),
                    'title_ar' => "آخر الأخبار عن {$company->name_ar} - قصة " . ($i + 1),
                    'source' => ['Bloomberg', 'Reuters', 'Financial Times', 'CNBC'][rand(0, 3)],
                    'url' => 'https://example.com/news/' . $company->symbol . '-' . $i,
                    'published_at' => now()->subDays(rand(1, 60)),
                ]);
            }
        }
    }
}
