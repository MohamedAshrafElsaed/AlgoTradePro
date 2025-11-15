@php
    $locale = app()->getLocale();
    $isArabic = $locale === 'ar';
    $appName = config('app.name');

    // SEO Content based on language
    $seoData = [
        'ar' => [
            'title' => "$appName - نظام إرسال رسائل واتساب جماعية احترافي",
            'description' => "نظام إرسال رسائل واتساب جماعية احترافي للشركات والمسوقين. قم بتوصيل واتساب، واستيراد جهات الاتصال، وإرسال رسائل مخصصة بشكل جماعي. يدعم حتى 1000 جهاز في وقت واحد، مع ميزات تخصيص الرسائل، والتحكم في معدل الإرسال لحماية حسابك من الحظر.",
            'keywords' => "واتساب جماعي, رسائل واتساب, رسائل جماعية, إدارة جهات الاتصال, حملات تسويقية, واتساب للأعمال, استيراد جهات اتصال, رسائل مخصصة, حملات واتساب, WhatsApp Bulk, واتساب API",
            'og_title' => "$appName - أفضل نظام لإرسال رسائل واتساب الجماعية",
            'og_description' => "أرسل رسائل واتساب جماعية مخصصة بأمان. يدعم 1000 جهاز، استيراد CSV/Excel، وحماية من الحظر. ابدأ مجاناً الآن!",
        ],
        'en' => [
            'title' => "$appName - Professional WhatsApp Bulk Messaging System",
            'description' => "Professional WhatsApp bulk messaging system for businesses and marketers. Connect WhatsApp, import contacts, send personalized bulk messages. Supports up to 1000 devices simultaneously, with message personalization, rate limiting to protect your account from bans.",
            'keywords' => "WhatsApp bulk, bulk messaging, mass messaging, WhatsApp Business, contact management, marketing campaigns, CSV import, personalized messages, WhatsApp API, WhatsApp sender, WhatsApp automation",
            'og_title' => "$appName - Best WhatsApp Bulk Messaging Platform",
            'og_description' => "Send personalized bulk WhatsApp messages safely. Supports 1000 devices, CSV/Excel import, and ban protection. Start free now!",
        ]
    ];
    $seo = $seoData[$locale];
@endphp

    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      dir="{{ $isArabic ? 'rtl' : 'ltr' }}" @class(['dark' => ($appearance ?? 'system') == 'dark'])>
<head>
    <style>
        html {
            background-color: oklch(1 0 0);
        }

        html.dark {
            background-color: oklch(0.145 0 0);
        }
    </style>
    @if(request()->is('login') || request()->is('register') || request()->is('password/*'))
        <meta name="robots" content="noindex, nofollow">
    @else
        <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    @endif
    @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
    @inertiaHead
</head>
<body class="font-sans antialiased">
@inertia
</body>
</html>
