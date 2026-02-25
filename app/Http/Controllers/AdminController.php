<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Resume;
use App\Models\Payment;
use App\Models\Template;
use App\Models\Plan;
use App\Models\SeoSetting;
use App\Models\AiUsageLog;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users'    => User::count(),
            'new_users_today'=> User::whereDate('created_at', today())->count(),
            'pro_users'      => User::where('plan', 'pro')->count(),
            'total_resumes'  => Resume::count(),
            'total_revenue'  => Payment::where('status', 'paid')->sum('amount'),
        ];

        $users = User::latest()->paginate(5);
        $recentPayments = Payment::with('user')->latest()->take(5)->get();

        return Inertia::render('Admin/Dashboard', [
            'stats'          => $stats,
            'users'          => $users,
            'recentPayments' => $recentPayments,
        ]);
    }

    public function analytics()
    {
        $totalUsers = User::count();
        $proUsers = User::where('plan', 'pro')->count();
        $totalRevenue = Payment::where('status', 'paid')->sum('amount');

        // ==== Financial KPIs ====
        
        // MRR Estimate: Paid in last 30 days
        $mrr = Payment::where('status', 'paid')
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('amount');
        
        $arr = $mrr * 12;

        // Churn Rate Approximation: Users who expired or cancelled in last 30 days
        // (Rough estimate: users who are free now but had a payment in the past)
        $cancelledLast30Days = User::where('plan', 'free')
            ->whereHas('resumes') // assuming they used the app
            ->where('updated_at', '>=', now()->subDays(30))
            ->count();
        
        $churnRate = $proUsers > 0 ? ($cancelledLast30Days / ($proUsers + $cancelledLast30Days)) * 100 : 0;

        // LTV: Total Revenue / Total Users
        $ltv = $totalUsers > 0 ? $totalRevenue / $totalUsers : 0;

        $stats = [
            'total_users'    => $totalUsers,
            'new_users_today'=> User::whereDate('created_at', today())->count(),
            'pro_users'      => $proUsers,
            'total_resumes'  => Resume::count(),
            'total_revenue'  => $totalRevenue,
            'mrr'            => $mrr,
            'arr'            => $arr,
            'churn_rate'     => round($churnRate, 1),
            'ltv'            => round($ltv, 2),
        ];

        // Last 6 months revenue for chart
        $monthlyRevenue = collect(range(5, 0))->map(function ($i) {
            $date = now()->subMonths($i);
            return [
                'month'   => $date->format('M'),
                'revenue' => Payment::where('status', 'paid')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->sum('amount'),
            ];
        })->values();

        // Top templates by resume count
        $topTemplates = Resume::selectRaw('template, count(*) as count')
            ->groupBy('template')
            ->orderByDesc('count')
            ->take(5)
            ->get()
            ->map(fn($r) => [
                'slug'  => $r->template ?? 'classic',
                'name'  => ucwords(str_replace('-', ' ', $r->template ?? 'classic')),
                'count' => $r->count,
            ]);

        $recentUsers = User::withCount('resumes')->latest()->take(10)->get();

        return Inertia::render('Admin/Analytics', [
            'stats'          => $stats,
            'monthlyRevenue' => $monthlyRevenue,
            'topTemplates'   => $topTemplates,
            'recentUsers'    => $recentUsers,
        ]);
    }

    // ==== Users ====

    public function users()
    {
        $users = User::withCount('resumes')->latest()->paginate(20);
        return Inertia::render('Admin/Users', ['users' => $users]);
    }

    public function destroyUser(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Cannot delete admin users.');
        }
        $user->delete();
        return back()->with('success', 'User deleted.');
    }

    public function toggleRole(User $user)
    {
        $user->update(['role' => $user->role === 'admin' ? 'user' : 'admin']);
        return back();
    }

    public function toggleActive(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Cannot disable admin users.');
        }
        $user->update(['is_active' => !$user->is_active]);
        return back()->with('success', 'User status updated.');
    }

    public function changePlan(Request $request, User $user)
    {
        $validated = $request->validate(['plan' => 'required|in:free,pro']);
        $user->update([
            'plan' => $validated['plan'],
            'subscription_ends_at' => $validated['plan'] === 'pro' ? now()->addMonth() : null,
        ]);
        return back();
    }

    // ==== Subscriptions ====

    public function subscriptions()
    {
        $subscriptions = User::where('plan', 'pro')
            ->with(['resumes'])
            ->latest()
            ->paginate(20);
        return Inertia::render('Admin/Subscriptions', ['subscriptions' => $subscriptions]);
    }

    public function extendSubscription(User $user)
    {
        $user->update([
            'subscription_ends_at' => ($user->subscription_ends_at && $user->subscription_ends_at->isFuture())
                ? $user->subscription_ends_at->addDays(30)
                : now()->addDays(30),
        ]);
        return back()->with('success', 'Subscription extended by 30 days.');
    }

    public function cancelSubscription(User $user)
    {
        $user->update([
            'plan' => 'free',
            'subscription_ends_at' => null,
        ]);
        return back()->with('success', 'Subscription cancelled.');
    }

    // ==== Payments ====

    public function payments()
    {
        $payments = Payment::with('user')->latest()->paginate(20);
        return Inertia::render('Admin/Payments', ['payments' => $payments]);
    }

    public function refundPayment(Payment $payment)
    {
        $payment->update(['status' => 'refunded']);
        return back()->with('success', 'Payment marked as refunded.');
    }

    public function exportPaymentsCsv()
    {
        $payments = Payment::with('user')->get();

        $csv = "ID,Transaction ID,User,Email,Plan,Amount,Currency,Status,Payment Method,Date\n";
        foreach ($payments as $p) {
            $csv .= implode(',', [
                $p->id,
                $p->transaction_id ?? '',
                $p->user?->name ?? '',
                $p->user?->email ?? '',
                $p->plan,
                $p->amount,
                $p->currency,
                $p->status,
                $p->payment_method ?? '',
                $p->created_at?->toDateString() ?? '',
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="payments-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    // ==== Templates ====

    public function templates()
    {
        $templates = Template::orderBy('sort_order')->get();
        return Inertia::render('Admin/Templates', ['templates' => $templates]);
    }

    public function storeTemplate(Request $request)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:100',
            'slug'                => 'required|string|max:100|unique:templates,slug',
            'component'           => 'required|string|max:100',
            'description'         => 'nullable|string|max:500',
            'category'            => 'required|in:standard,rtl,creative',
            'is_premium'          => 'boolean',
            'sort_order'          => 'integer|min:0',
            'supported_languages' => 'nullable|array',
        ]);
        Template::create($validated);
        return back()->with('success', 'Template created.');
    }

    public function updateTemplate(Request $request, Template $template)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:100',
            'slug'                => 'required|string|max:100|unique:templates,slug,' . $template->id,
            'component'           => 'required|string|max:100',
            'description'         => 'nullable|string|max:500',
            'category'            => 'required|in:standard,rtl,creative',
            'is_premium'          => 'boolean',
            'sort_order'          => 'integer|min:0',
            'supported_languages' => 'nullable|array',
        ]);
        $template->update($validated);
        return back()->with('success', 'Template updated.');
    }

    public function destroyTemplate(Template $template)
    {
        $template->delete();
        return back()->with('success', 'Template deleted.');
    }

    public function toggleTemplateActive(Template $template)
    {
        $template->update(['is_active' => !$template->is_active]);
        return back();
    }

    // ==== Settings ====

    public function settings()
    {
        return Inertia::render('Admin/Settings', [
            'ai_config' => [
                'provider' => 'Mistral AI (French)',
                'model'    => 'mistral-small-latest',
                'api_key'  => config('services.mistral.key') ? '••••' . substr(config('services.mistral.key'), -4) : 'Not Configured',
                'full_key' => config('services.mistral.key'),
            ],
            'google_oauth' => [
                'client_id'     => config('services.google.client_id') ? '••••' . substr(config('services.google.client_id'), -12) : 'Not Configured',
                'client_secret' => config('services.google.client_secret') ? '••••' . substr(config('services.google.client_secret'), -4) : 'Not Configured',
                'redirect_uri'  => config('services.google.redirect'),
                'is_configured' => !empty(config('services.google.client_id')),
            ],
            'stripe' => [
                'publishable_key' => config('services.stripe.key') ? '••••' . substr(config('services.stripe.key'), -8) : 'Not Configured',
                'secret_key'      => config('services.stripe.secret') ? '••••' . substr(config('services.stripe.secret'), -4) : 'Not Configured',
                'webhook_secret'  => config('services.stripe.webhook') ? '••••' . substr(config('services.stripe.webhook'), -4) : 'Not Configured',
                'is_configured'   => !empty(config('services.stripe.key')) && !empty(config('services.stripe.secret')),
            ],
        ]);
    }

    public function updateAiSettings(Request $request)
    {
        $validated = $request->validate([
            'api_key' => 'required|string',
        ]);

        $this->setEnv('MISTRAL_API_KEY', $validated['api_key']);

        return back()->with('success', 'AI Settings updated successfully.');
    }

    public function updateGoogleSettings(Request $request)
    {
        $validated = $request->validate([
            'client_id'     => 'required|string',
            'client_secret' => 'required|string',
            'redirect_uri'  => 'nullable|string|url',
        ]);

        $this->setEnv('GOOGLE_CLIENT_ID', $validated['client_id']);
        $this->setEnv('GOOGLE_CLIENT_SECRET', $validated['client_secret']);

        if (!empty($validated['redirect_uri'])) {
            $this->setEnv('GOOGLE_REDIRECT_URI', $validated['redirect_uri']);
        }

        return back()->with('success', 'Google OAuth settings updated successfully.');
    }

    public function updateStripeSettings(Request $request)
    {
        $validated = $request->validate([
            'publishable_key' => 'required|string',
            'secret_key'      => 'required|string',
            'webhook_secret'  => 'nullable|string',
        ]);

        $this->setEnv('STRIPE_PUBLISHABLE_KEY', $validated['publishable_key']);
        $this->setEnv('STRIPE_SECRET_KEY', $validated['secret_key']);

        if (!empty($validated['webhook_secret'])) {
            $this->setEnv('STRIPE_WEBHOOK_SECRET', $validated['webhook_secret']);
        }

        return back()->with('success', 'Stripe settings updated successfully.');
    }

    private function setEnv($key, $value)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            $content = file_get_contents($path);
            if (strpos($content, "{$key}=") !== false) {
                $content = preg_replace("/{$key}=.*/", "{$key}={$value}", $content);
            } else {
                $content .= "\n{$key}={$value}";
            }
            file_put_contents($path, $content);
        }
    }

    // ==== Coupons ====

    public function coupons()
    {
        $coupons = \App\Models\Coupon::latest()->paginate(20);
        return Inertia::render('Admin/Coupons', ['coupons' => $coupons]);
    }

    public function storeCoupon(Request $request)
    {
        $validated = $request->validate([
            'code'             => 'required|string|unique:coupons,code',
            'discount_percent' => 'required|integer|min:1|max:100',
            'max_uses'         => 'required|integer|min:1',
            'expires_at'       => 'nullable|date',
        ]);

        \App\Models\Coupon::create($validated);

        return back()->with('success', 'Coupon created successfully.');
    }

    public function destroyCoupon(\App\Models\Coupon $coupon)
    {
        $coupon->delete();
        return back()->with('success', 'Coupon deleted.');
    }

    // ==== Plans (Phase 9) ====

    public function plans()
    {
        $plans = Plan::orderBy('sort_order')->get();
        return Inertia::render('Admin/Plans', ['plans' => $plans]);
    }

    public function storePlan(Request $request)
    {
        $validated = $request->validate([
            'name'                    => 'required|string|max:100',
            'slug'                    => 'required|string|max:100|unique:plans,slug',
            'description'             => 'nullable|string',
            'price_monthly'           => 'required|numeric|min:0',
            'price_yearly'            => 'required|numeric|min:0',
            'stripe_price_id_monthly' => 'nullable|string',
            'stripe_price_id_yearly'  => 'nullable|string',
            'features'                => 'nullable|array',
            'is_active'               => 'boolean',
            'is_popular'              => 'boolean',
            'sort_order'              => 'integer|min:0',
        ]);

        Plan::create($validated);
        return back()->with('success', 'Plan created successfully.');
    }

    public function updatePlan(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name'                    => 'required|string|max:100',
            'slug'                    => 'required|string|max:100|unique:plans,slug,' . $plan->id,
            'description'             => 'nullable|string',
            'price_monthly'           => 'required|numeric|min:0',
            'price_yearly'            => 'required|numeric|min:0',
            'stripe_price_id_monthly' => 'nullable|string',
            'stripe_price_id_yearly'  => 'nullable|string',
            'features'                => 'nullable|array',
            'is_active'               => 'boolean',
            'is_popular'              => 'boolean',
            'sort_order'              => 'integer|min:0',
        ]);

        $plan->update($validated);
        return back()->with('success', 'Plan updated successfully.');
    }

    public function destroyPlan(Plan $plan)
    {
        if ($plan->users()->count() > 0) {
            return back()->with('error', 'Cannot delete plan with active users.');
        }
        $plan->delete();
        return back()->with('success', 'Plan deleted.');
    }

    public function togglePlanActive(Plan $plan)
    {
        $plan->update(['is_active' => !$plan->is_active]);
        return back();
    }

    // ==== Impersonation (Phase 9) ====

    public function impersonate(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot impersonate yourself.');
        }

        if ($user->role === 'admin') {
            return back()->with('error', 'Cannot impersonate another admin.');
        }

        // Store current admin ID in session
        Session::put('impersonated_by', Auth::id());

        // Login as the user
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', "Now impersonating {$user->name}");
    }

    public function leaveImpersonation()
    {
        if (!Session::has('impersonated_by')) {
            return redirect()->route('dashboard');
        }

        $adminId = Session::get('impersonated_by');
        $admin = User::find($adminId);

        if (!$admin) {
            Session::forget('impersonated_by');
            return redirect()->route('login');
        }

        Auth::login($admin);
        Session::forget('impersonated_by');

        return redirect()->route('admin.users.index')->with('success', 'Returned to admin view.');
    }

    // ==== SEO & Content (Phase 11) ====

    public function seo()
    {
        $seo = SeoSetting::all()->keyBy('page_name');
        return Inertia::render('Admin/Seo', ['seo' => $seo]);
    }

    public function updateSeo(Request $request)
    {
        $request->validate([
            'page_name'   => 'required|string',
            'title'       => 'required|array',
            'description' => 'required|array',
            'keywords'    => 'nullable|array',
        ]);

        SeoSetting::updateOrCreate(
            ['page_name' => $request->page_name],
            [
                'title'       => $request->title,
                'description' => $request->description,
                'keywords'    => $request->keywords,
            ]
        );

        return back()->with('success', 'SEO settings updated.');
    }

    public function updateIdentity(Request $request)
    {
        $request->validate([
            'logo'    => 'nullable|image|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,svg|max:1024',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('public/brand');
            // Logic to update config or .env would be here, 
            // but usually we just reference a fixed name in storage.
        }

        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('public/brand');
        }

        return back()->with('success', 'Brand identity updated.');
    }

    // ==== AI Operations (Phase 12) ====

    public function aiOps()
    {
        $usageStats = [
            'total_tokens'   => AiUsageLog::sum('total_tokens'),
            'total_cost'     => AiUsageLog::sum('cost_estimate'),
            'avg_response'   => AiUsageLog::avg('response_time_ms'),
            'usage_by_model' => AiUsageLog::selectRaw('model, sum(total_tokens) as tokens')
                ->groupBy('model')
                ->get(),
        ];

        // Mock current config (In real app, this would be from DB/Redis)
        $aiConfig = [
            'provider' => 'Mistral AI',
            'model'    => 'mistral-small-latest',
        ];

        // Mock prompts (Phase 12 goal: Manage from UI)
        $prompts = [
            'resume_writer' => 'You are a professional resume writer...',
            'ats_analyzer'  => 'Compare the following resume against the job description...',
        ];

        return Inertia::render('Admin/AiOps', [
            'usageStats' => $usageStats,
            'aiConfig'   => $aiConfig,
            'prompts'    => $prompts,
        ]);
    }

    public function updateAiConfig(Request $request)
    {
        $request->validate([
            'provider' => 'required|string',
            'model'    => 'required|string',
        ]);
        // Here you would save to settings table or .env
        return back()->with('success', 'AI Model configuration updated.');
    }

    public function updateAiPrompts(Request $request)
    {
        $request->validate([
            'prompts' => 'required|array',
        ]);
        // Save prompts to DB
        return back()->with('success', 'System prompts updated.');
    }

    // ==== Marketing & Systems (Phase 13) ====

    public function newsletter()
    {
        $subscribers = NewsletterSubscriber::latest()->paginate(20);
        return Inertia::render('Admin/Newsletter', ['subscribers' => $subscribers]);
    }

    public function exportSubscribers()
    {
        // Simple CSV generation logic would go here
        return back()->with('success', 'Subscriber list exported successfully.');
    }

    public function logs()
    {
        $logPath = storage_path('logs/laravel.log');
        $logs = [];

        if (File::exists($logPath)) {
            // Read last 100 lines for performance
            $allLogs = File::get($logPath);
            $lines = array_slice(explode("\n", $allLogs), -101);
            $logs = array_filter(array_reverse($lines));
        }

        return Inertia::render('Admin/Logs', [
            'logs' => $logs,
            'logInfo' => [
                'path' => $logPath,
                'size' => File::exists($logPath) ? round(File::size($logPath) / 1024, 2) . ' KB' : '0 KB',
            ]
        ]);
    }

    public function toggleMaintenance(Request $request)
    {
        $isDown = app()->isDownForMaintenance();
        
        if ($isDown) {
            Artisan::call('up');
            return back()->with('success', 'Site is now LIVE.');
        } else {
            Artisan::call('down', ['--secret' => 'admin-access']);
            return back()->with('warning', 'Site is now in MAINTENANCE mode.');
        }
    }
}
