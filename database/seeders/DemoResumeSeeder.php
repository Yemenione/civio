<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Resume;
use App\Models\Experience;
use App\Models\Education;
use App\Models\Skill;
use App\Models\Language;
use Illuminate\Support\Str;

class DemoResumeSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (!$user) return;

        // 1. Creative Masterpiece for a Designer
        $creative = Resume::create([
            'user_id' => $user->id,
            'title' => 'Creative Director Portfolio',
            'first_name' => 'Julian',
            'last_name' => 'Aura',
            'email' => 'julian@aura.design',
            'phone' => '+1 (555) 000-1111',
            'city' => 'Los Angeles',
            'country' => 'USA',
            'job_title' => 'Creative Director',
            'summary' => 'Visionary Creative Director with 10+ years of experience in high-fidelity product design and brand evolution. Specialized in creating immersive digital masterpieces that blend aesthetics with technical precision.',
            'template' => 'creative',
            'is_public' => true,
            'share_token' => Str::random(32),
            'design_options' => [
                'accent_color' => '#7c3aed',
                'font_family' => '"Outfit", sans-serif',
                'font_size' => '10pt',
                'line_height' => 1.5,
                'letter_spacing' => 1.5,
                'section_gap' => 30,
            ]
        ]);

        Experience::create([
            'resume_id' => $creative->id,
            'job_title' => 'Senior Art Director',
            'company' => 'Starlight Media',
            'start_date' => '2020-01',
            'end_date' => 'Present',
            'current' => true,
            'description' => 'Leading the design vision for next-generation media platforms. Orchestrating a team of 15 designers to deliver premium brand experiences across 5 global markets.'
        ]);

        Skill::create(['resume_id' => $creative->id, 'name' => 'Product Design', 'level' => 'Expert']);
        Skill::create(['resume_id' => $creative->id, 'name' => 'Brand Strategy', 'level' => 'Expert']);
        Skill::create(['resume_id' => $creative->id, 'name' => '3D Rendering', 'level' => 'Advanced']);

        // 2. Arabic Pro for a Tech Executive
        $arabic = Resume::create([
            'user_id' => $user->id,
            'title' => 'مدير تقني - سيرة ذاتية احترافية',
            'first_name' => 'أحمد',
            'last_name' => 'المنصور',
            'email' => 'ahmed@tech.sa',
            'phone' => '+966 50 000 0000',
            'city' => 'الرياض',
            'country' => 'السعودية',
            'job_title' => 'مدير تقني تنفيذي (CTO)',
            'summary' => 'قائد تقني ذو رؤية استراتيجية، متخصص في بناء الأنظمة المعقدة وقيادة الفرق التقنية نحو الابتكار. خبرة واسعة في التحول الرقمي وتطوير الحلول السحابية فائقة الأداء.',
            'template' => 'arabicpro',
            'is_public' => true,
            'share_token' => Str::random(32),
            'design_options' => [
                'accent_color' => '#065f46',
                'font_family' => '"Inter", sans-serif',
                'font_size' => '11pt',
                'language' => 'Arabic',
                'tone' => 'formal',
                'section_gap' => 25,
            ]
        ]);

        Experience::create([
            'resume_id' => $arabic->id,
            'job_title' => 'مدير تقني',
            'company' => 'نجم السحاب للتقنية',
            'start_date' => '2015-06',
            'end_date' => 'Present',
            'current' => true,
            'description' => 'قيادة استراتيجية التكنولوجيا وتطوير البنية التحتية السحابية للشركة. الإشراف على إطلاق أكثر من 20 تطبيقاً ناجحاً في السوق الخليجي.'
        ]);

        Skill::create(['resume_id' => $arabic->id, 'name' => 'Cloud Architecture', 'level' => 'Expert']);
        Skill::create(['resume_id' => $arabic->id, 'name' => 'Team Leadership', 'level' => 'Expert']);

        // 3. Modern Edge for a Product Manager
        $modern = Resume::create([
            'user_id' => $user->id,
            'title' => 'Modern Product Manager',
            'first_name' => 'Sarah',
            'last_name' => 'Prism',
            'email' => 'sarah@prism.io',
            'phone' => '+44 20 7946 0000',
            'city' => 'London',
            'country' => 'UK',
            'job_title' => 'Senior Product Manager',
            'summary' => 'Data-driven Product Manager with a track record of scaling SaaS platforms from zero to millions of users. Obsessed with user experience and technical excellence.',
            'template' => 'modern',
            'is_public' => false,
            'share_token' => Str::random(32),
            'design_options' => [
                'accent_color' => '#4f46e5',
                'font_family' => '"Inter", sans-serif',
                'letter_spacing' => 0.5,
                'section_gap' => 20,
            ]
        ]);

        Experience::create([
            'resume_id' => $modern->id,
            'job_title' => 'Lead Product Manager',
            'company' => 'ScaleUp AI',
            'start_date' => '2019-03',
            'end_date' => '2023-12',
            'current' => false,
            'description' => 'Owned the core product roadmap and delivered 300% growth in active user base over 2 years.'
        ]);
        
        Skill::create(['resume_id' => $modern->id, 'name' => 'Agile/Scrum', 'level' => 'Expert']);
        Skill::create(['resume_id' => $modern->id, 'name' => 'Data Analytics', 'level' => 'Advanced']);

        Language::create(['resume_id' => $modern->id, 'name' => 'English', 'proficiency' => 'Native', 'sort_order' => 0]);
        Language::create(['resume_id' => $modern->id, 'name' => 'French', 'proficiency' => 'Professional', 'sort_order' => 1]);
    }
}
