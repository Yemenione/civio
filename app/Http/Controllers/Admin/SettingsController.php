<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingsController extends Controller
{
    public function index()
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

    public function updateAi(Request $request)
    {
        $validated = $request->validate([
            'api_key' => 'required|string',
        ]);

        $this->setEnv('MISTRAL_API_KEY', $validated['api_key']);

        return back()->with('success', 'AI Settings updated successfully.');
    }

    public function updateGoogle(Request $request)
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

    public function updateStripe(Request $request)
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

    public function updateIdentity(Request $request)
    {
        $request->validate([
            'logo'    => 'nullable|image|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,svg|max:1024',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('public/brand');
        }

        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('public/brand');
        }

        return back()->with('success', 'Brand identity updated.');
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
}
