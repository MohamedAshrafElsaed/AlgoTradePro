<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TwelveDataCompanyAnalysisSeeder extends Seeder
{
    private string $apiKey;
    private string $baseUrl = 'https://api.twelvedata.com';
    private Company $company;

    public function __construct()
    {
        $this->apiKey = '742806420f33434da64ee4f88b0c1b77';
    }

    public function run(): void
    {
        // Query specific company from database
        $this->company = Company::where('id', 160)->first();

        dd($this->company->toArray());
        if (!$this->company) {
            $this->command->error("❌ Company with ID 160 not found!");
            return;
        }

        $this->displayHeader();

        // Call all APIs in sequence
        $this->fetchRealTimeQuote();
        $this->fetchTimeSeries();
        $this->fetchTechnicalIndicators();
        $this->fetchFundamentalData();
        $this->fetchAnalystData();
        $this->fetchCorporateActions();
        $this->fetchMarketData();

        $this->displayFooter();
    }

    /**
     * Display header with company info
     */
    private function displayHeader(): void
    {
        $this->command->newLine();
        $this->command->info("╔════════════════════════════════════════════════════════════════╗");
        $this->command->info("║           TWELVE DATA API - COMPANY ANALYSIS                   ║");
        $this->command->info("╚════════════════════════════════════════════════════════════════╝");
        $this->command->newLine();

        $this->command->info("📊 Company Information:");
        $this->command->line("   ID:           " . $this->company->id);
        $this->command->line("   Symbol:       " . $this->company->symbol);
        $this->command->line("   Name (EN):    " . $this->company->name_en);
        $this->command->line("   Name (AR):    " . $this->company->name_ar);
        $this->command->line("   Exchange:     " . $this->company->exchange);
        $this->command->line("   Currency:     " . $this->company->currency);
        $this->command->line("   Country:      " . $this->company->country);
        $this->command->newLine();
        $this->command->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
    }

    /**
     * 1. Real-time Quote Data
     */
    private function fetchRealTimeQuote(): void
    {
        $this->command->newLine();
        $this->command->info("📈 [1/7] Fetching REAL-TIME QUOTE DATA...");

        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => "apikey {$this->apiKey}",
            ])->get("{$this->baseUrl}/quote", [
                'symbol' => $this->company->symbol,
                'exchange' => $this->company->exchange,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                $this->command->line("   ✓ API Response: SUCCESS");
                $this->command->line("   ├─ Open:              " . ($data['open'] ?? 'N/A'));
                $this->command->line("   ├─ High:              " . ($data['high'] ?? 'N/A'));
                $this->command->line("   ├─ Low:               " . ($data['low'] ?? 'N/A'));
                $this->command->line("   ├─ Close:             " . ($data['close'] ?? 'N/A'));
                $this->command->line("   ├─ Price:             " . ($data['price'] ?? 'N/A') . " " . $this->company->currency);
                $this->command->line("   ├─ Change:            " . ($data['change'] ?? 'N/A'));
                $this->command->line("   ├─ Percent Change:    " . ($data['percent_change'] ?? 'N/A') . "%");
                $this->command->line("   ├─ Volume:            " . number_format($data['volume'] ?? 0));
                $this->command->line("   ├─ Average Volume:    " . number_format($data['average_volume'] ?? 0));
                $this->command->line("   ├─ Previous Close:    " . ($data['previous_close'] ?? 'N/A'));
                $this->command->line("   ├─ 52 Week High:      " . ($data['fifty_two_week']['high'] ?? 'N/A'));
                $this->command->line("   ├─ 52 Week Low:       " . ($data['fifty_two_week']['low'] ?? 'N/A'));
                $this->command->line("   └─ Timestamp:         " . ($data['datetime'] ?? 'N/A'));

            } else {
                $this->command->warn("   ✗ API Response: FAILED (" . $response->status() . ")");
                $this->command->line("   └─ Message: " . $response->body());
            }
        } catch (\Exception $e) {
            $this->command->error("   ✗ Exception: " . $e->getMessage());
        }
    }

    /**
     * 2. Time Series (Historical OHLCV)
     */
    private function fetchTimeSeries(): void
    {
        $this->command->newLine();
        $this->command->info("📊 [2/7] Fetching TIME SERIES DATA (Last 5 Days)...");

        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => "apikey {$this->apiKey}",
            ])->get("{$this->baseUrl}/time_series", [
                'symbol' => $this->company->symbol,
                'exchange' => $this->company->exchange,
                'interval' => '1day',
                'outputsize' => 5,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $values = $data['values'] ?? [];

                $this->command->line("   ✓ API Response: SUCCESS");
                $this->command->line("   ├─ Interval: " . ($data['meta']['interval'] ?? 'N/A'));
                $this->command->line("   └─ Data Points: " . count($values));
                $this->command->newLine();

                if (!empty($values)) {
                    $this->command->line("   📅 Historical Data:");
                    foreach (array_slice($values, 0, 5) as $index => $record) {
                        $this->command->line("   " . ($index + 1) . ". Date: " . $record['datetime']);
                        $this->command->line("      Open:   " . $record['open'] . " | High:   " . $record['high']);
                        $this->command->line("      Low:    " . $record['low'] . "  | Close:  " . $record['close']);
                        $this->command->line("      Volume: " . number_format($record['volume'] ?? 0));
                        if ($index < 4) $this->command->line("");
                    }
                }
            } else {
                $this->command->warn("   ✗ API Response: FAILED (" . $response->status() . ")");
            }
        } catch (\Exception $e) {
            $this->command->error("   ✗ Exception: " . $e->getMessage());
        }
    }

    /**
     * 3. Technical Indicators
     */
    private function fetchTechnicalIndicators(): void
    {
        $this->command->newLine();
        $this->command->info("📉 [3/7] Fetching TECHNICAL INDICATORS...");

        // RSI
        $this->command->newLine();
        $this->command->line("   ▶ RSI (Relative Strength Index):");
        $rsi = $this->fetchIndicator('rsi', ['time_period' => 14]);
        $this->displayIndicatorData($rsi);

        // MACD
        $this->command->newLine();
        $this->command->line("   ▶ MACD (Moving Average Convergence Divergence):");
        $macd = $this->fetchIndicator('macd', [
            'fast_period' => 12,
            'slow_period' => 26,
            'signal_period' => 9
        ]);
        $this->displayIndicatorData($macd, ['macd', 'macd_signal', 'macd_hist']);

        // Bollinger Bands
        $this->command->newLine();
        $this->command->line("   ▶ Bollinger Bands:");
        $bbands = $this->fetchIndicator('bbands', [
            'time_period' => 20,
            'sd' => 2,
            'ma_type' => 'SMA'
        ]);
        $this->displayIndicatorData($bbands, ['upper_band', 'middle_band', 'lower_band']);

        // SMA
        $this->command->newLine();
        $this->command->line("   ▶ SMA (Simple Moving Averages):");
        $sma20 = $this->fetchIndicator('sma', ['time_period' => 20]);
        $sma50 = $this->fetchIndicator('sma', ['time_period' => 50]);
        $sma20Val = isset($sma20['values'][0]['sma']) ? $sma20['values'][0]['sma'] : 'N/A';
        $sma50Val = isset($sma50['values'][0]['sma']) ? $sma50['values'][0]['sma'] : 'N/A';
        $this->command->line("      SMA 20: " . $sma20Val);
        $this->command->line("      SMA 50: " . $sma50Val);

        // Stochastic
        $this->command->newLine();
        $this->command->line("   ▶ Stochastic Oscillator:");
        $stoch = $this->fetchIndicator('stoch', [
            'fast_k_period' => 14,
            'slow_k_period' => 3,
            'slow_d_period' => 3
        ]);
        $this->displayIndicatorData($stoch, ['slow_k', 'slow_d']);
    }

    /**
     * 4. Fundamental Data
     */
    private function fetchFundamentalData(): void
    {
        $this->command->newLine();
        $this->command->info("💼 [4/7] Fetching FUNDAMENTAL DATA...");

        // Profile
        $this->command->newLine();
        $this->command->line("   ▶ Company Profile:");
        $this->fetchProfile();

        // Statistics
        $this->command->newLine();
        $this->command->line("   ▶ Financial Statistics:");
        $this->fetchStatistics();

        // Income Statement
        $this->command->newLine();
        $this->command->line("   ▶ Income Statement (Latest):");
        $this->fetchIncomeStatement();

        // Balance Sheet
        $this->command->newLine();
        $this->command->line("   ▶ Balance Sheet (Latest):");
        $this->fetchBalanceSheet();

        // Earnings
        $this->command->newLine();
        $this->command->line("   ▶ Earnings Data:");
        $this->fetchEarnings();
    }

    /**
     * 5. Analyst Data
     */
    private function fetchAnalystData(): void
    {
        $this->command->newLine();
        $this->command->info("👔 [5/7] Fetching ANALYST DATA...");

        // Recommendations
        $this->command->newLine();
        $this->command->line("   ▶ Analyst Recommendations:");
        $this->fetchRecommendations();

        // Price Target
        $this->command->newLine();
        $this->command->line("   ▶ Price Targets:");
        $this->fetchPriceTarget();

        // Analyst Ratings
        $this->command->newLine();
        $this->command->line("   ▶ Recent Analyst Ratings:");
        $this->fetchAnalystRatings();
    }

    /**
     * 6. Corporate Actions
     */
    private function fetchCorporateActions(): void
    {
        $this->command->newLine();
        $this->command->info("📋 [6/7] Fetching CORPORATE ACTIONS...");

        // Dividends
        $this->command->newLine();
        $this->command->line("   ▶ Dividend History:");
        $this->fetchDividends();

        // Splits
        $this->command->newLine();
        $this->command->line("   ▶ Stock Splits:");
        $this->fetchSplits();
    }

    /**
     * 7. Market Data
     */
    private function fetchMarketData(): void
    {
        $this->command->newLine();
        $this->command->info("🌍 [7/7] Fetching MARKET DATA...");

        // Logo
        $this->command->newLine();
        $this->command->line("   ▶ Company Logo:");
        $this->fetchLogo();
    }

    /**
     * Helper: Fetch technical indicator
     */
    private function fetchIndicator(string $indicator, array $params = []): array
    {
        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => "apikey {$this->apiKey}",
            ])->get("{$this->baseUrl}/{$indicator}", array_merge([
                'symbol' => $this->company->symbol,
                'exchange' => $this->company->exchange,
                'interval' => '1day',
                'outputsize' => 3,
            ], $params));

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            Log::debug("Failed to fetch {$indicator}: " . $e->getMessage());
        }

        return [];
    }

    /**
     * Helper: Display indicator data
     */
    private function displayIndicatorData(array $data, array $fields = null): void
    {
        if (empty($data) || empty($data['values'])) {
            $this->command->line("      ✗ No data available");
            return;
        }

        $this->command->line("      ✓ Latest Value:");
        $latest = $data['values'][0];

        if ($fields) {
            foreach ($fields as $field) {
                $value = $latest[$field] ?? 'N/A';
                $label = ucwords(str_replace('_', ' ', $field));
                $this->command->line("         " . $label . ": " . $value);
            }
        } else {
            foreach ($latest as $key => $value) {
                if ($key !== 'datetime') {
                    $label = ucwords(str_replace('_', ' ', $key));
                    $this->command->line("         " . $label . ": " . $value);
                }
            }
        }
        $datetime = $latest['datetime'] ?? 'N/A';
        $this->command->line("         Date: " . $datetime);
    }

    /**
     * Fetch Profile
     */
    private function fetchProfile(): void
    {
        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => "apikey {$this->apiKey}",
            ])->get("{$this->baseUrl}/profile", [
                'symbol' => $this->company->symbol,
                'exchange' => $this->company->exchange,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->command->line("      ✓ Profile Data:");
                $this->command->line("         Name:          " . ($data['name'] ?? 'N/A'));
                $this->command->line("         Sector:        " . ($data['sector'] ?? 'N/A'));
                $this->command->line("         Industry:      " . ($data['industry'] ?? 'N/A'));
                $this->command->line("         CEO:           " . ($data['CEO'] ?? 'N/A'));
                $this->command->line("         Employees:     " . number_format($data['employees'] ?? 0));
                $this->command->line("         Website:       " . ($data['website'] ?? 'N/A'));
                $desc = isset($data['description']) ? substr($data['description'], 0, 100) . "..." : 'N/A';
                $this->command->line("         Description:   " . $desc);
            } else {
                $this->command->line("      ✗ Not available (Status: " . $response->status() . ")");
            }
        } catch (\Exception $e) {
            $this->command->line("      ✗ Error: " . $e->getMessage());
        }
    }

    /**
     * Fetch Statistics
     */
    private function fetchStatistics(): void
    {
        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => "apikey {$this->apiKey}",
            ])->get("{$this->baseUrl}/statistics", [
                'symbol' => $this->company->symbol,
                'exchange' => $this->company->exchange,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $stats = $data['statistics'] ?? [];

                $this->command->line("      ✓ Key Statistics:");
                $this->command->line("         Market Cap:        " . ($stats['market_capitalization'] ?? 'N/A'));
                $this->command->line("         P/E Ratio:         " . ($stats['pe_ratio'] ?? 'N/A'));
                $this->command->line("         Forward P/E:       " . ($stats['forward_pe'] ?? 'N/A'));
                $this->command->line("         PEG Ratio:         " . ($stats['peg_ratio'] ?? 'N/A'));
                $this->command->line("         EPS:               " . ($stats['eps'] ?? 'N/A'));
                $this->command->line("         Beta:              " . ($stats['beta'] ?? 'N/A'));
                $this->command->line("         Dividend Yield:    " . ($stats['dividend_yield'] ?? 'N/A') . "%");
                $this->command->line("         52 Week High:      " . ($stats['52_week_high'] ?? 'N/A'));
                $this->command->line("         52 Week Low:       " . ($stats['52_week_low'] ?? 'N/A'));
                $this->command->line("         ROE:               " . ($stats['return_on_equity'] ?? 'N/A') . "%");
                $this->command->line("         Profit Margin:     " . ($stats['profit_margin'] ?? 'N/A') . "%");
            } else {
                $this->command->line("      ✗ Not available (Status: " . $response->status() . ")");
            }
        } catch (\Exception $e) {
            $this->command->line("      ✗ Error: " . $e->getMessage());
        }
    }

    /**
     * Fetch Income Statement
     */
    private function fetchIncomeStatement(): void
    {
        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => "apikey {$this->apiKey}",
            ])->get("{$this->baseUrl}/income_statement", [
                'symbol' => $this->company->symbol,
                'exchange' => $this->company->exchange,
                'period' => 'annual',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $income = $data['income_statement'][0] ?? [];

                $this->command->line("      ✓ Income Statement:");
                $this->command->line("         Fiscal Date:       " . ($income['fiscal_date'] ?? 'N/A'));
                $this->command->line("         Revenue:           " . number_format($income['revenue'] ?? 0));
                $this->command->line("         Gross Profit:      " . number_format($income['gross_profit'] ?? 0));
                $this->command->line("         Operating Income:  " . number_format($income['operating_income'] ?? 0));
                $this->command->line("         Net Income:        " . number_format($income['net_income'] ?? 0));
                $this->command->line("         EPS:               " . ($income['eps'] ?? 'N/A'));
                $this->command->line("         EBITDA:            " . number_format($income['ebitda'] ?? 0));
            } else {
                $this->command->line("      ✗ Not available (Status: " . $response->status() . ")");
            }
        } catch (\Exception $e) {
            $this->command->line("      ✗ Error: " . $e->getMessage());
        }
    }

    /**
     * Fetch Balance Sheet
     */
    private function fetchBalanceSheet(): void
    {
        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => "apikey {$this->apiKey}",
            ])->get("{$this->baseUrl}/balance_sheet", [
                'symbol' => $this->company->symbol,
                'exchange' => $this->company->exchange,
                'period' => 'annual',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $balance = $data['balance_sheet'][0] ?? [];

                $this->command->line("      ✓ Balance Sheet:");
                $this->command->line("         Fiscal Date:           " . ($balance['fiscal_date'] ?? 'N/A'));
                $this->command->line("         Total Assets:          " . number_format($balance['total_assets'] ?? 0));
                $this->command->line("         Total Liabilities:     " . number_format($balance['total_liabilities'] ?? 0));
                $this->command->line("         Shareholders Equity:   " . number_format($balance['shareholders_equity'] ?? 0));
                $this->command->line("         Current Assets:        " . number_format($balance['current_assets'] ?? 0));
                $this->command->line("         Current Liabilities:   " . number_format($balance['current_liabilities'] ?? 0));
            } else {
                $this->command->line("      ✗ Not available (Status: " . $response->status() . ")");
            }
        } catch (\Exception $e) {
            $this->command->line("      ✗ Error: " . $e->getMessage());
        }
    }

    /**
     * Fetch Earnings
     */
    private function fetchEarnings(): void
    {
        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => "apikey {$this->apiKey}",
            ])->get("{$this->baseUrl}/earnings", [
                'symbol' => $this->company->symbol,
                'exchange' => $this->company->exchange,
                'outputsize' => 3,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $earnings = $data['earnings'] ?? [];

                if (!empty($earnings)) {
                    $this->command->line("      ✓ Recent Earnings:");
                    foreach (array_slice($earnings, 0, 3) as $index => $earning) {
                        $this->command->line("         " . ($index + 1) . ". Date: " . $earning['date']);
                        $this->command->line("            EPS Estimate: " . ($earning['eps_estimate'] ?? 'N/A'));
                        $this->command->line("            EPS Actual:   " . ($earning['eps_actual'] ?? 'N/A'));
                    }
                } else {
                    $this->command->line("      ✗ No earnings data");
                }
            } else {
                $this->command->line("      ✗ Not available (Status: " . $response->status() . ")");
            }
        } catch (\Exception $e) {
            $this->command->line("      ✗ Error: " . $e->getMessage());
        }
    }

    /**
     * Fetch Recommendations
     */
    private function fetchRecommendations(): void
    {
        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => "apikey {$this->apiKey}",
            ])->get("{$this->baseUrl}/recommendations", [
                'symbol' => $this->company->symbol,
                'exchange' => $this->company->exchange,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                $this->command->line("      ✓ Analyst Consensus:");
                $this->command->line("         Strong Buy:    " . ($data['strong_buy'] ?? 0));
                $this->command->line("         Buy:           " . ($data['buy'] ?? 0));
                $this->command->line("         Hold:          " . ($data['hold'] ?? 0));
                $this->command->line("         Sell:          " . ($data['sell'] ?? 0));
                $this->command->line("         Strong Sell:   " . ($data['strong_sell'] ?? 0));
                $this->command->line("         Mean:          " . ($data['recommendation_mean'] ?? 'N/A'));
                $this->command->line("         Recommendation: " . ($data['recommendation_key'] ?? 'N/A'));
            } else {
                $this->command->line("      ✗ Not available (Status: " . $response->status() . ")");
            }
        } catch (\Exception $e) {
            $this->command->line("      ✗ Error: " . $e->getMessage());
        }
    }

    /**
     * Fetch Price Target
     */
    private function fetchPriceTarget(): void
    {
        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => "apikey {$this->apiKey}",
            ])->get("{$this->baseUrl}/price_target", [
                'symbol' => $this->company->symbol,
                'exchange' => $this->company->exchange,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                $this->command->line("      ✓ Price Targets:");
                $this->command->line("         Average:       " . ($data['price_target_average'] ?? 'N/A'));
                $this->command->line("         High:          " . ($data['price_target_high'] ?? 'N/A'));
                $this->command->line("         Low:           " . ($data['price_target_low'] ?? 'N/A'));
                $this->command->line("         Median:        " . ($data['price_target_median'] ?? 'N/A'));
                $this->command->line("         # Analysts:    " . ($data['number_of_analysts'] ?? 0));
            } else {
                $this->command->line("      ✗ Not available (Status: " . $response->status() . ")");
            }
        } catch (\Exception $e) {
            $this->command->line("      ✗ Error: " . $e->getMessage());
        }
    }

    /**
     * Fetch Analyst Ratings
     */
    private function fetchAnalystRatings(): void
    {
        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => "apikey {$this->apiKey}",
            ])->get("{$this->baseUrl}/analyst_ratings", [
                'symbol' => $this->company->symbol,
                'exchange' => $this->company->exchange,
                'outputsize' => 3,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $ratings = $data['analyst_ratings'] ?? [];

                if (!empty($ratings)) {
                    $this->command->line("      ✓ Recent Ratings:");
                    foreach (array_slice($ratings, 0, 3) as $index => $rating) {
                        $this->command->line("         " . ($index + 1) . ". Date: " . $rating['date']);
                        $this->command->line("            Firm:     " . ($rating['analyst_firm'] ?? 'N/A'));
                        $this->command->line("            Analyst:  " . ($rating['analyst_name'] ?? 'N/A'));
                        $this->command->line("            Rating:   " . ($rating['rating'] ?? 'N/A'));
                        $this->command->line("            Action:   " . ($rating['action'] ?? 'N/A'));
                        $this->command->line("            Target:   " . ($rating['price_target'] ?? 'N/A'));
                    }
                } else {
                    $this->command->line("      ✗ No ratings data");
                }
            } else {
                $this->command->line("      ✗ Not available (Status: " . $response->status() . ")");
            }
        } catch (\Exception $e) {
            $this->command->line("      ✗ Error: " . $e->getMessage());
        }
    }

    /**
     * Fetch Dividends
     */
    private function fetchDividends(): void
    {
        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => "apikey {$this->apiKey}",
            ])->get("{$this->baseUrl}/dividends", [
                'symbol' => $this->company->symbol,
                'exchange' => $this->company->exchange,
                'range' => '1y',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $dividends = $data['dividends'] ?? [];

                if (!empty($dividends)) {
                    $this->command->line("      ✓ Recent Dividends:");
                    foreach (array_slice($dividends, 0, 3) as $index => $dividend) {
                        $this->command->line("         " . ($index + 1) . ". Ex-Date: " . $dividend['ex_date']);
                        $this->command->line("            Amount:   " . $dividend['amount'] . " " . $this->company->currency);
                        $this->command->line("            Pay Date: " . ($dividend['payment_date'] ?? 'N/A'));
                    }
                } else {
                    $this->command->line("      ✗ No dividend data");
                }
            } else {
                $this->command->line("      ✗ Not available (Status: " . $response->status() . ")");
            }
        } catch (\Exception $e) {
            $this->command->line("      ✗ Error: " . $e->getMessage());
        }
    }

    /**
     * Fetch Splits
     */
    private function fetchSplits(): void
    {
        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => "apikey {$this->apiKey}",
            ])->get("{$this->baseUrl}/splits", [
                'symbol' => $this->company->symbol,
                'exchange' => $this->company->exchange,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $splits = $data['splits'] ?? [];

                if (!empty($splits)) {
                    $this->command->line("      ✓ Stock Splits:");
                    foreach (array_slice($splits, 0, 3) as $index => $split) {
                        $this->command->line("         " . ($index + 1) . ". Date: " . $split['date']);
                        $this->command->line("            Description: " . ($split['description'] ?? 'N/A'));
                    }
                } else {
                    $this->command->line("      ✗ No split data");
                }
            } else {
                $this->command->line("      ✗ Not available (Status: " . $response->status() . ")");
            }
        } catch (\Exception $e) {
            $this->command->line("      ✗ Error: " . $e->getMessage());
        }
    }

    /**
     * Fetch Logo
     */
    private function fetchLogo(): void
    {
        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => "apikey {$this->apiKey}",
            ])->get("{$this->baseUrl}/logo", [
                'symbol' => $this->company->symbol,
                'exchange' => $this->company->exchange,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->command->line("      ✓ Logo URL: " . ($data['url'] ?? 'N/A'));
            } else {
                $this->command->line("      ✗ Not available (Status: " . $response->status() . ")");
            }
        } catch (\Exception $e) {
            $this->command->line("      ✗ Error: " . $e->getMessage());
        }
    }

    /**
     * Display footer
     */
    private function displayFooter(): void
    {
        $this->command->newLine();
        $this->command->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->command->info("╔════════════════════════════════════════════════════════════════╗");
        $this->command->info("║                     ✅ ANALYSIS COMPLETE                       ║");
        $this->command->info("╚════════════════════════════════════════════════════════════════╝");
        $this->command->newLine();
    }
}
