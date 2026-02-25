<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Template;
use Illuminate\Support\Str;

class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        $uniqueTemplates = [
            // 1. RTL / Arabic Focused
            [
                'name'                => 'Arabic Pro',
                'slug'                => 'arabic-pro',
                'component'           => 'ArabicProTemplate',
                'description'         => "Premium RTL-optimized design specifically crafted for Arabic professionals. Features perfect alignment and beautiful Arabic typography.",
                'category'            => 'rtl',
                'is_premium'          => true,
                'supported_languages' => ['ar', 'en'],
            ],
            [
                'name'                => 'Titan',
                'slug'                => 'titan-rtl',
                'component'           => 'TitanTemplate',
                'description'         => "A bold, structured layout for Arabic leadership profiles.",
                'category'            => 'rtl',
                'is_premium'          => true,
                'supported_languages' => ['ar', 'en'],
            ],

            // 2. Creative
            [
                'name'                => 'Creative Magic',
                'slug'                => 'creative-magic',
                'component'           => 'CreativeTemplate',
                'description'         => "Stand out with a visually striking, modern, and creative layout. Perfect for designers and marketers.",
                'category'            => 'creative',
                'is_premium'          => false,
                'supported_languages' => ['en', 'fr', 'es', 'de', 'ar'],
            ],
            [
                'name'                => 'CanvaLux',
                'slug'                => 'canvalux-premium',
                'component'           => 'CanvaLuxTemplate',
                'description'         => "High-fidelity design inspired by premium Canva aesthetics. Ideal for modern professionals.",
                'category'            => 'creative',
                'is_premium'          => true,
                'supported_languages' => ['en', 'fr', 'ar'],
            ],
            [
                'name'                => 'Lumina',
                'slug'                => 'lumina-studio',
                'component'           => 'LuminaTemplate',
                'description'         => "Soft gradients and glassmorphism elements for a sophisticated creative look.",
                'category'            => 'creative',
                'is_premium'          => true,
                'supported_languages' => ['en', 'fr'],
            ],

            // 3. Modern / Tech
            [
                'name'                => 'Modern Tech',
                'slug'                => 'modern-tech',
                'component'           => 'ModernTemplate',
                'description'         => "A clean, cutting-edge design built for the digital age. ATS-friendly and professional.",
                'category'            => 'standard',
                'is_premium'          => false,
                'supported_languages' => ['en', 'fr', 'ar'],
            ],
            [
                'name'                => 'Tech Pulse',
                'slug'                => 'tech-pulse',
                'component'           => 'TechTemplate',
                'description'         => "Grid-based layout for developers and technical engineers.",
                'category'            => 'standard',
                'is_premium'          => true,
                'supported_languages' => ['en', 'fr'],
            ],
            [
                'name'                => 'Obsidian',
                'slug'                => 'obsidian-dark',
                'component'           => 'ObsidianTemplate',
                'description' => "Minimalist dark-themed layout for the modern technical visionary.",
                'category' => 'standard',
                'is_premium' => true,
                'supported_languages' => ['en', 'fr'],
            ],

            // 4. Classic / Professional
            [
                'name'                => 'Executive Elite',
                'slug'                => 'executive-elite',
                'component'           => 'ExecutiveTemplate',
                'description'         => "The gold standard for leadership roles. Timeless and highly readable.",
                'category'            => 'standard',
                'is_premium'          => true,
                'supported_languages' => ['en', 'fr', 'ar'],
            ],
            [
                'name'                => 'Classic Professional',
                'slug'                => 'classic-pro',
                'component'           => 'ClassicTemplate',
                'description'         => "Standard formal layout trusted by Fortune 500 recruiters.",
                'category'            => 'standard',
                'is_premium'          => false,
                'supported_languages' => ['en', 'fr', 'ar'],
            ],
            [
                'name'                => 'Minimalist Plus',
                'slug'                => 'minimalist-plus',
                'component'           => 'MinimalistPlusTemplate',
                'description'         => "Spacious and clean design focusing on high-impact content.",
                'category'            => 'standard',
                'is_premium'          => false,
                'supported_languages' => ['en', 'fr'],
            ],
            [
                'name'                => 'Royal Scholar',
                'slug'                => 'royal-scholar',
                'component'           => 'AcademicTemplate',
                'description'         => "Academic-focused layout with detailed sections for publications and research.",
                'category'            => 'standard',
                'is_premium'          => true,
                'supported_languages' => ['en', 'fr'],
            ],
        ];

        // Clean slate: Truncate for testing unique templates
        // Only if you want to wipe duplicates. For seeding, updateOrCreate is safer.
        
        foreach ($uniqueTemplates as $index => $template) {
            $template['sort_order'] = $index + 1;
            Template::updateOrCreate(['slug' => $template['slug']], $template);
        }
    }
}
