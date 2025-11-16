<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\CompanyType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TwelveDataSaudiCompaniesSeeder extends Seeder
{
    private string $apiKey;
    private string $baseUrl = 'https://api.twelvedata.com';
    private ?int $limit = null;

    public function __construct()
    {
        $this->apiKey = 'ee1fdda784bc4891977a050a6a3555bb';
    }

    public function run(): void
    {
        $testMode = $this->command->confirm('Do you want to run in test mode (5 records only)?', true);

        if ($testMode) {
            $this->limit = 5;
            $this->command->info('🧪 Running in TEST MODE - Processing only 5 companies');
        } else {
            $this->command->info('🚀 Running in FULL MODE - Processing all companies');
        }

        $this->command->info('Fetching Saudi Arabia companies from Twelve Data API...');

        $stocks = $this->fetchSaudiStocks();

        if (empty($stocks)) {
            $this->command->error('No stocks found for Saudi Arabia.');
            return;
        }

        if ($this->limit) {
            $stocks = array_slice($stocks, 0, $this->limit);
        }

        $this->command->info('Found ' . count($stocks) . ' companies to process.');

        $progressBar = $this->command->getOutput()->createProgressBar(count($stocks));
        $progressBar->start();

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];

        foreach ($stocks as $index => $stock) {
            try {
                // Validate required fields
                if (empty($stock['symbol']) || empty($stock['name'])) {
                    $this->command->warn("\n  ⚠ Skipping stock with missing symbol or name");
                    $skipped++;
                    $progressBar->advance();
                    continue;
                }

                // Get or create company type
                $companyType = $this->getOrCreateCompanyType($stock['type'] ?? 'Common Stock');

                // Normalize figi_code: empty string becomes null
                $figiCode = !empty($stock['figi_code']) && $stock['figi_code'] !== ''
                    ? $stock['figi_code']
                    : null;

                // Check if company exists by symbol
                $company = Company::where('symbol', $stock['symbol'])->first();

                // If not found by symbol, try by figi_code (only if figi_code is not null)
                if (!$company && $figiCode) {
                    $company = Company::where('figi_code', $figiCode)->first();
                }

                // Prepare data with all fallbacks
                $data = [
                    'company_type_id' => $companyType->id,
                    'symbol' => $stock['symbol'],
                    'currency' => $stock['currency'] ?? 'SAR',
                    'exchange' => $stock['exchange'] ?? 'Tadawul',
                    'mic_code' => $stock['mic_code'] ?? 'XSAU',
                    'country' => $stock['country'] ?? 'Saudi Arabia',
                    'figi_code' => $figiCode,
                    'name_en' => $stock['name'],
                    'name_ar' => $stock['name'],
                    'description_en' => null,
                    'description_ar' => null,
                    'ceo' => null,
                    'headquarter_en' => null,
                    'headquarter_ar' => null,
                    'current_price' => null,
                    'price_change' => null,
                    'change_percentage' => null,
                    'last_updated' => null,
                ];

                // Fetch current price only for new companies
                if (!$company) {
                    $this->command->info("\n  📊 Fetching price for {$stock['symbol']}...");

                    $priceData = $this->fetchPrice($stock['symbol'], $stock['exchange'] ?? 'Tadawul');

                    if ($priceData) {
                        $data['current_price'] = $priceData['price'];
                        $data['price_change'] = 0.00;
                        $data['change_percentage'] = 0.00;
                        $data['last_updated'] = now();
                        $this->command->info("     ✓ Price: {$priceData['price']} SAR");
                    } else {
                        $this->command->warn("     ⚠ Price not available");
                    }
                }

                if ($company) {
                    // Update existing company
                    $updateData = [
                        'company_type_id' => $data['company_type_id'],
                        'currency' => $data['currency'],
                        'exchange' => $data['exchange'],
                        'mic_code' => $data['mic_code'],
                        'country' => $data['country'],
                        'name_en' => $data['name_en'],
                    ];

                    // Only update figi_code if it's not null and company doesn't have one
                    if ($figiCode && !$company->figi_code) {
                        $updateData['figi_code'] = $figiCode;
                    }

                    // Only update name_ar if it's currently null or empty
                    if (empty($company->name_ar)) {
                        $updateData['name_ar'] = $data['name_ar'];
                    }

                    $company->update($updateData);
                    $updated++;
                    $this->command->info("\n  ↻ Updated: {$stock['symbol']} - {$stock['name']}");
                } else {
                    // Create new company
                    Company::create($data);
                    $created++;
                    $this->command->info("\n  ✓ Created: {$stock['symbol']} - {$stock['name']}");
                }
            } catch (\Exception $e) {
                $errorMsg = "{$stock['symbol']} - {$e->getMessage()}";
                $errors[] = $errorMsg;

                Log::error('Error processing company: ' . ($stock['symbol'] ?? 'UNKNOWN'), [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'stock' => $stock,
                ]);

                $this->command->error("\n  ✗ Failed: {$stock['symbol']} - {$e->getMessage()}");
                $skipped++;
            }

            $progressBar->advance();

            // Rate limiting: sleep for 200ms between requests
            usleep(200000);
        }

        $progressBar->finish();
        $this->command->newLine(2);

        // Display summary
        $this->displaySummary($created, $updated, $skipped, $errors);
    }

    /**
     * Display seeding summary
     */
    private function displaySummary(int $created, int $updated, int $skipped, array $errors): void
    {
        $this->command->info("════════════════════════════════════════");
        $this->command->info("  ✅ SEEDING COMPLETED");
        $this->command->info("════════════════════════════════════════");
        $this->command->info("  📊 Statistics:");
        $this->command->info("     ✓ Created:  {$created}");
        $this->command->info("     ↻ Updated:  {$updated}");
        $this->command->info("     ✗ Skipped:  {$skipped}");
        $this->command->info("     ━━━━━━━━━━━━━━━━━━━");
        $this->command->info("     📈 Total:    " . ($created + $updated + $skipped));
        $this->command->info("════════════════════════════════════════");

        if (!empty($errors)) {
            $this->command->newLine();
            $this->command->error("⚠️  ERRORS ENCOUNTERED :");
            $this->command->error("════════════════════════════════════════");
            foreach (array_slice($errors, 0, 10) as $error) {
                $this->command->error("  • {$error}");
            }
            if (count($errors) > 10) {
                $this->command->error("  ... and " . (count($errors) - 10) . " more errors");
                $this->command->error("  Check storage/logs/laravel.log for full details");
            }
        }
    }

    /**
     * Fetch stocks from Saudi Arabia
     */
    private function fetchSaudiStocks(): array
    {
        try {
            $this->command->info('📡 Calling Twelve Data API...');

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "apikey {$this->apiKey}",
                ])
                ->get("{$this->baseUrl}/stocks", [
                    'country' => 'Saudi Arabia',
                    'format' => 'json',
                ]);

            if ($response->successful()) {
                $data = $response->json('data', []);

                if (empty($data)) {
                    $this->command->warn('API returned empty data array');
                }

                $this->command->info("✓ API call successful - " . count($data) . " stocks found");

                return $data;
            }

            $this->command->error('✗ API request failed with status: ' . $response->status());
            return [];
        } catch (\Exception $e) {
            $this->command->error('✗ Exception: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetch current price for a symbol
     */
    private function fetchPrice(string $symbol, string $exchange = 'Tadawul'): ?array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => "apikey {$this->apiKey}",
                ])
                ->get("{$this->baseUrl}/price", [
                    'symbol' => $symbol,
                    'exchange' => $exchange,
                    'format' => 'json',
                ]);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['price']) && is_numeric($data['price'])) {
                    return [
                        'price' => (float) $data['price'],
                    ];
                }
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get or create company type
     */
    private function getOrCreateCompanyType(string $typeName): CompanyType
    {
        $slug = Str::slug($typeName);

        return CompanyType::firstOrCreate(
            ['slug' => $slug],
            [
                'name_en' => $typeName,
                'name_ar' => $this->translateTypeToArabic($typeName),
            ]
        );
    }

    /**
     * Simple translation mapping for common stock types
     */
    private function translateTypeToArabic(string $type): string
    {
        $translations = [
            'Common Stock' => 'سهم عادي',
            'Preferred Stock' => 'سهم ممتاز',
            'ETF' => 'صندوق تداول',
            'Mutual Fund' => 'صندوق استثمار',
            'Index' => 'مؤشر',
            'REIT' => 'صندوق عقاري',
            'Warrant' => 'ضمان',
            'Bond' => 'سند',
            'American Depositary Receipt' => 'إيصال إيداع أمريكي',
            'Depositary Receipt' => 'إيصال إيداع',
            'Global Depositary Receipt' => 'إيصال إيداع عالمي',
            'Exchange-Traded Note' => 'سند متداول',
            'Limited Partnership' => 'شراكة محدودة',
            'Trust' => 'صندوق ائتماني',
            'Unit' => 'وحدة',
            'Closed-end Fund' => 'صندوق مغلق',
            'Bond Fund' => 'صندوق سندات',
            'Physical Currency' => 'عملة مادية',
            'Digital Currency' => 'عملة رقمية',
            'Structured Product' => 'منتج منظم',
            'Right' => 'حق',
        ];

        return $translations[$type] ?? $type;
    }
}
