import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

// ==================== User & Authentication ====================



// If you need to extend the existing InertiaPageProps
declare module '@inertiajs/core' {
    interface PageProps {
        auth: {
            user: User | null;
        };
        locale: string;
        translations: Record<string, any>;
        name: string;
        quote: {
            message: string;
            author: string;
        };
        sidebarOpen: boolean;
    }
}
export interface User {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    mobile_number: string;
    country_id: number;
    industry_id: number;
    locale: string;
    email_verified_at: string | null;
    mobile_verified_at: string | null;
    created_at: string;
    updated_at: string;
    deleted_at?: string | null;

    // Computed attributes (when available)
    full_name?: string;
    formatted_mobile?: string;
    subscription_status?: 'none' | 'active' | 'trial' | 'expired' | 'cancelled';

    // Relationships (optional - when loaded)
    country?: Country;
    industry?: Industry;
    devices?: UserDevice[];
    subscription?: UserSubscription;
    subscriptions?: UserSubscription[];
    transactions?: Transaction[];
    usage?: UserUsage;
}

export interface Auth {
    user: User;
}

// ==================== Location & Demographics ====================

export interface Country {
    id: number;
    iso_code: string;
    iso3_code: string;
    phone_code: string;
    name_en: string;
    name_ar: string;
    is_active: boolean;
    created_at: string;
    updated_at: string;

    // Computed attribute (when using locale)
    name?: string;
}

export interface Industry {
    id: number;
    slug: string;
    name_en: string;
    name_ar: string;
    is_active: boolean;
    sort_order: number;
    created_at: string;
    updated_at: string;

    // Computed attribute (when using locale)
    name?: string;
}

// ==================== Subscriptions & Packages ====================

export interface Package {
    id: number;
    name_en: string;
    name_ar: string;
    slug: string;
    price: number;
    billing_cycle: string;
    features: Record<string, any>;
    limits: Record<string, any>;
    sort_order: number;
    is_active: boolean;
    is_popular: boolean;
    is_best_value: boolean;
    color: string;
    created_at: string;
    updated_at: string;
    deleted_at?: string | null;

    // Computed attributes
    name?: string;
    formatted_price?: string;
    is_current?: boolean;
}

export interface UserSubscription {
    id: number;
    user_id: number;
    package_id: number;
    status: 'active' | 'trial' | 'expired' | 'cancelled';
    trial_ends_at: string | null;
    starts_at: string;
    ends_at: string;
    cancelled_at: string | null;
    auto_renew: boolean;
    created_at: string;
    updated_at: string;
    deleted_at?: string | null;

    // Relationships (when loaded)
    user?: User;
    package?: Package;
}

export interface SubscriptionSummary {
    has_subscription: boolean;
    status: 'none' | 'active' | 'trial' | 'expired' | 'cancelled';
    package?: string;
    days_remaining?: number;
    ends_at?: string;
    is_trial?: boolean;
    usage?: {
        messages_sent: number;
        messages_limit: number | string;
        contacts_validated: number;
        contacts_limit: number | string;
    };
}

// ==================== Usage Tracking ====================

export interface UserUsage {
    id: number;
    user_id: number;
    period_start: string;
    period_end: string;
    messages_sent: number;
    contacts_validated: number;
    connected_numbers_count: number;
    templates_created: number;
    created_at: string;
    updated_at: string;

    // Relationships (when loaded)
    user?: User;
}

// ==================== Payments & Transactions ====================

export interface PaymentMethod {
    id: number;
    name_en: string;
    name_ar: string;
    slug: string;
    gateway: string;
    is_active: boolean;
    config: Record<string, any>;
    sort_order: number;
    icon: string | null;
    created_at: string;
    updated_at: string;
    deleted_at?: string | null;

    // Computed attribute (when using locale)
    name?: string;
}

export interface Transaction {
    id: number;
    user_id: number;
    package_id: number;
    payment_method_id: number | null;
    transaction_id: string;
    amount: number;
    currency: string;
    status: 'pending' | 'completed' | 'failed' | 'refunded';
    payment_gateway: string;
    gateway_response: Record<string, any> | null;
    paid_at: string | null;
    refunded_at: string | null;
    notes: string | null;
    created_at: string;
    updated_at: string;
    deleted_at?: string | null;

    // Relationships (when loaded)
    user?: User;
    package?: Package;
    payment_method?: PaymentMethod;
}

// ==================== Device Management ====================

export interface UserDevice {
    id: number;
    user_id: number;
    device_id: string;
    device_type: 'mobile' | 'desktop' | 'tablet';
    platform: string;
    platform_version: string | null;
    browser: string;
    browser_version: string | null;
    device_name: string | null;
    is_robot: boolean;
    ip_address: string;
    country_code: string | null;
    city: string | null;
    region: string | null;
    postal_code: string | null;
    latitude: number | null;
    longitude: number | null;
    timezone: string | null;
    isp: string | null;
    connection_type: string | null;
    cf_ray: string | null;
    cf_connecting_ip: string | null;
    cf_is_tor: boolean;
    cf_threat_score: number | null;
    user_agent: string;
    accept_language: string | null;
    last_seen_at: string;
    is_trusted: boolean;
    is_active: boolean;
    created_at: string;
    updated_at: string;

    // Relationships (when loaded)
    user?: User;
}

// ==================== Navigation & UI ====================

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type BreadcrumbItemType = BreadcrumbItem;

// ==================== Page Props ====================

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User | null;
    };
    locale: string;
    translations: Record<string, any>;
    name: string;
    quote: {
        message: string;
        author: string;
    };
    sidebarOpen: boolean;
};

export interface WhatsAppSession {
    id: string;
    session_name: string;
    status: 'pending' | 'qr_ready' | 'connected' | 'disconnected' | 'failed';
    phone_number?: string;
    jid?: string;
    push_name?: string;
    platform?: string;
    connected_at?: string;
    disconnected_at?: string;
    last_seen?: string;
    device_info?: Record<string, any>;
    is_business_account?: boolean;
    is_active: boolean;
    created_at: string;
    updated_at: string;
}

export interface WhatsAppSessionSummary {
    total_sessions: number;
    connected: number;
    pending: number;
    max_devices: number;
    available_slots: number | string;
}

export interface OnboardingData {
    tour_completed?: boolean;
    whatsapp_connected?: boolean;
    contacts_imported?: boolean;
    template_created?: boolean;
    campaign_sent?: boolean;
    completed_at?: string;
}

// Add these types to your existing resources/js/types/index.d.ts file
// ==================== Companies & Trading ====================

export interface CompanyType {
    id: number;
    name_en: string;
    name_ar: string;
    slug: string;
    created_at: string;
    updated_at: string;
}

export interface CompanyStatistic {
    id: number;
    company_id: number;
    market_cap: string | null;
    value_today: string | null;
    adtv_6m: string | null;
    eps: string | null;
    pe_ratio: string | null;
    dividend_yield: string | null;
    week_52_high: string | null;
    week_52_low: string | null;
    enterprise_value: string | null;
    price_to_sales_ratio: string | null;
    price_to_book_ratio: string | null;
    forward_pe: string | null;
    peg_ratio: string | null;
    ev_to_revenue: string | null;
    ev_to_ebitda: string | null;
    profit_margin: string | null;
    operating_margin: string | null;
    return_on_assets: string | null;
    return_on_equity: string | null;
    revenue: string | null;
    revenue_per_share: string | null;
    quarterly_revenue_growth: string | null;
    gross_profit: string | null;
    ebitda: string | null;
    net_income_to_common: string | null;
    trailing_eps: string | null;
    forward_eps: string | null;
    quarterly_earnings_growth: string | null;
    beta: string | null;
    '52_week_change': string | null;
    sp500_52_week_change: string | null;
    shares_outstanding: number | null;
    shares_float: number | null;
    percent_held_by_insiders: string | null;
    percent_held_by_institutions: string | null;
    shares_short: number | null;
    short_ratio: string | null;
    short_percent_of_float: string | null;
    payout_ratio: string | null;
    dividend_date: string | null;
    ex_dividend_date: string | null;
    last_split_date: string | null;
    last_split_factor: string | null;
    total_cash: string | null;
    total_cash_per_share: string | null;
    total_debt: string | null;
    debt_to_equity: string | null;
    current_ratio: string | null;
    book_value_per_share: string | null;
    operating_cash_flow: string | null;
    levered_free_cash_flow: string | null;
    created_at: string;
    updated_at: string;
}

export interface CompanyNews {
    id: number;
    company_id: number;
    title_en: string;
    title_ar: string;
    source: string;
    url: string | null;
    published_at: string;
    created_at: string;
    updated_at: string;
}

export interface CompanyTimeSeries {
    id: number;
    company_id: number;
    date: string;
    interval: string;
    open: string | null;
    high: string | null;
    low: string | null;
    close: string | null;
    volume: number | null;
    created_at: string;
    updated_at: string;
}

export interface CompanyTechnicalIndicator {
    id: number;
    company_id: number;
    date: string;
    interval: string;
    sma_20: string | null;
    sma_50: string | null;
    sma_200: string | null;
    ema_12: string | null;
    ema_26: string | null;
    macd: string | null;
    macd_signal: string | null;
    macd_hist: string | null;
    bb_upper: string | null;
    bb_middle: string | null;
    bb_lower: string | null;
    rsi_14: string | null;
    stoch_k: string | null;
    stoch_d: string | null;
    cci: string | null;
    roc: string | null;
    momentum: string | null;
    obv: string | null;
    ad: string | null;
    adosc: string | null;
    created_at: string;
    updated_at: string;
}

export interface CompanyFinancial {
    id: number;
    company_id: number;
    fiscal_date: string;
    period: 'annual' | 'quarterly';
    statement_type: 'income' | 'balance' | 'cash_flow';
    revenue: string | null;
    cost_of_revenue: string | null;
    gross_profit: string | null;
    operating_expense: string | null;
    operating_income: string | null;
    ebitda: string | null;
    ebit: string | null;
    interest_expense: string | null;
    income_before_tax: string | null;
    income_tax_expense: string | null;
    net_income: string | null;
    eps: string | null;
    eps_diluted: string | null;
    weighted_average_shares: number | null;
    weighted_average_shares_diluted: number | null;
    total_assets: string | null;
    current_assets: string | null;
    cash_and_equivalents: string | null;
    cash_and_short_term_investments: string | null;
    accounts_receivable: string | null;
    inventory: string | null;
    non_current_assets: string | null;
    property_plant_equipment: string | null;
    intangible_assets: string | null;
    goodwill: string | null;
    total_liabilities: string | null;
    current_liabilities: string | null;
    accounts_payable: string | null;
    short_term_debt: string | null;
    non_current_liabilities: string | null;
    long_term_debt: string | null;
    shareholders_equity: string | null;
    retained_earnings: string | null;
    operating_cash_flow: string | null;
    capital_expenditure: string | null;
    free_cash_flow: string | null;
    investing_cash_flow: string | null;
    financing_cash_flow: string | null;
    dividend_payments: string | null;
    stock_repurchase: string | null;
    net_change_in_cash: string | null;
    created_at: string;
    updated_at: string;
}

export interface CompanyRecommendation {
    id: number;
    company_id: number;
    strong_buy_count: number;
    buy_count: number;
    hold_count: number;
    sell_count: number;
    strong_sell_count: number;
    recommendation_mean: string | null;
    recommendation_key: string | null;
    price_target_average: string | null;
    price_target_high: string | null;
    price_target_low: string | null;
    price_target_median: string | null;
    number_of_analysts: number;
    ai_recommendation: 'STRONG_BUY' | 'BUY' | 'HOLD' | 'SELL' | 'STRONG_SELL' | null;
    ai_confidence: string | null;
    ai_reasoning: string | null;
    last_updated: string | null;
    created_at: string;
    updated_at: string;
}

export interface CompanyAnalystRating {
    id: number;
    company_id: number;
    rating_date: string;
    analyst_name: string | null;
    analyst_firm: string | null;
    rating: string | null;
    previous_rating: string | null;
    action: 'Maintains' | 'Upgrade' | 'Downgrade' | 'Initiates' | 'Reiterates' | null;
    price_target: string | null;
    created_at: string;
    updated_at: string;
}

export interface CompanyEarning {
    id: number;
    company_id: number;
    earnings_date: string;
    time: 'Before Hours' | 'After Hours' | 'Time Not Supplied' | null;
    eps_estimate: string | null;
    eps_actual: string | null;
    revenue_estimate: string | null;
    revenue_actual: string | null;
    fiscal_date_ending: string | null;
    period: 'Q1' | 'Q2' | 'Q3' | 'Q4' | 'Annual' | null;
    created_at: string;
    updated_at: string;
}

export interface CompanyDividend {
    id: number;
    company_id: number;
    ex_date: string;
    payment_date: string | null;
    record_date: string | null;
    declaration_date: string | null;
    amount: string;
    adjusted_amount: string | null;
    currency: string | null;
    dividend_type: string | null;
    created_at: string;
    updated_at: string;
}

export interface CompanySplit {
    id: number;
    company_id: number;
    split_date: string;
    description: string | null;
    split_ratio: string | null;
    from_factor: number | null;
    to_factor: number | null;
    created_at: string;
    updated_at: string;
}

export interface CompanySubscription {
    id: number;
    user_id: number;
    company_id: number;
    notify_recommendations: boolean;
    notify_updates: boolean;
    notify_news: boolean;
    notify_price_alerts: boolean;
    created_at: string;
    updated_at: string;
}

export interface Company {
    id: number;
    company_type_id: number;
    symbol: string;
    name_en: string;
    name_ar: string;
    currency: string | null;
    exchange: string | null;
    mic_code: string | null;
    country: string | null;
    figi_code: string | null;
    current_price: string | null;
    price_change: string | null;
    change_percentage: string | null;
    description_en: string | null;
    description_ar: string | null;
    ceo: string | null;
    headquarter_en: string | null;
    headquarter_ar: string | null;
    last_updated: string | null;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;

    // Computed attributes
    is_favorited?: boolean;
    is_subscribed?: boolean;

    // Relationships
    type?: CompanyType;
    statistics?: CompanyStatistic;
    news?: CompanyNews[];
    time_series?: CompanyTimeSeries[];
    technical_indicators?: CompanyTechnicalIndicator[];
    financials?: CompanyFinancial[];
    recommendation?: CompanyRecommendation;
    analyst_ratings?: CompanyAnalystRating[];
    earnings?: CompanyEarning[];
    dividends?: CompanyDividend[];
    splits?: CompanySplit[];
}

export interface CompanyFilters {
    search?: string;
    type?: number;
}
