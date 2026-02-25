<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Template;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TemplateController extends Controller
{
    public function index()
    {
        $templates = Template::orderBy('sort_order')->get();
        return Inertia::render('Admin/Templates', ['templates' => $templates]);
    }

    public function store(Request $request)
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

    public function update(Request $request, Template $template)
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

    public function destroy(Template $template)
    {
        $template->delete();
        return back()->with('success', 'Template deleted.');
    }

    public function toggle(Template $template)
    {
        $template->update(['is_active' => !$template->is_active]);
        return back();
    }
}
