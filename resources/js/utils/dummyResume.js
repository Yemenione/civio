// resources/js/utils/dummyResume.js

export const globalDummyResume = {
    first_name: 'Alexandria',
    last_name: 'Chen',
    fullname: 'Alexandria Chen',
    email: 'alex.chen@example.com',
    phone: '+1 (555) 019-8372',
    job_title: 'Senior Full-Stack Developer & Tech Lead',
    jobtitle: 'Senior Full-Stack Developer & Tech Lead',
    city: 'San Francisco',
    country: 'CA, USA',
    address: '123 Innovation Drive, Tech District',
    linkedin: 'linkedin.com/in/alexandriachen',
    github: 'github.com/alexc-dev',
    portfolio: 'alexandriachen.dev',
    website: 'alexandriachen.dev',
    photo: 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=600&auto=format&fit=crop', // High-quality professional portrait
    summary: 'Innovative and results-driven Senior Full-Stack Developer with over 8 years of experience architecting scalable distributed systems and leading cross-functional engineering teams. Expert in Vue.js, Laravel, and cloud-native architecture (AWS/Docker). Proven track record of accelerating product delivery by 40% while maintaining 99.99% system uptime. Passionate about mentoring junior developers, fostering an engineering culture of excellence, and bridging the gap between complex technical solutions and intuitive user experiences.',

    experiences: [
        {
            id: 'exp-1',
            jobtitle: 'Senior Full-Stack Developer & Tech Lead',
            company: 'Nexus Innovations Inc.',
            city: 'San Francisco, CA',
            start_date: 'Mar 2021',
            end_date: 'Present',
            current: true,
            description: `• Spearheaded the architectural design and full-stack development of a highly scalable enterprise SaaS platform serving 50,000+ daily active users, utilizing Vue 3, Inertia.js, and Laravel 10.
• Led a high-performing agile squad of 6 engineers; conducted code reviews, established CI/CD pipelines via GitHub Actions, and reduced deployment time from 2 hours to 15 minutes.
• Optimized complex database queries and implemented Redis caching, slashing API response times by 65% and significantly improving the overall user experience.`
        },
        {
            id: 'exp-2',
            jobtitle: 'Software Engineer II',
            company: 'Skyline FinTech Solutions',
            city: 'New York, NY (Remote)',
            start_date: 'Jun 2018',
            end_date: 'Feb 2021',
            current: false,
            description: `• Developed and launched 3 critical microservices within the core payment gateway using Node.js and PostgreSQL, processing over $2M in daily transaction volume.
• Collaborated closely with the UI/UX team to implement pixel-perfect, highly responsive dashboards using TailwindCSS and React, increasing client retention by 22%.
• Migrated legacy monolithic architecture to containerized services using Docker and Kubernetes, reducing server costs by $15k annually.`
        },
        {
            id: 'exp-3',
            jobtitle: 'Junior Web Developer',
            company: 'Creative Media Agency',
            city: 'Austin, TX',
            start_date: 'Jan 2016',
            end_date: 'May 2018',
            current: false,
            description: `• Built 15+ custom e-commerce applications and corporate websites using PHP, WordPress, and JavaScript.
• Implemented robust SEO strategies and web performance optimizations, improving average Lighthouse scores to 90+ across all client projects.`
        }
    ],

    education: [
        {
            id: 'edu-1',
            degree: 'Master of Science in Computer Science',
            institution: 'Stanford University',
            city: 'Stanford, CA',
            start_date: 'Sep 2014',
            end_date: 'May 2016',
            current: false,
            description: 'Specialization in Artificial Intelligence and Human-Computer Interaction. Graduated with Honors (GPA: 3.9/4.0).'
        },
        {
            id: 'edu-2',
            degree: 'Bachelor of Science in Software Engineering',
            institution: 'University of Texas at Austin',
            city: 'Austin, TX',
            start_date: 'Sep 2010',
            end_date: 'May 2014',
            current: false,
            description: 'Dean’s List (2012-2014). Lead Developer for the University Robotics Club.'
        }
    ],

    skills: [
        { id: 'sk-1', name: 'Vue.js / Nuxt3', level: 'Expert', group: 'Frontend' },
        { id: 'sk-2', name: 'Laravel / PHP', level: 'Expert', group: 'Backend' },
        { id: 'sk-3', name: 'TailwindCSS', level: 'Expert', group: 'Frontend' },
        { id: 'sk-4', name: 'JavaScript / TypeScript', level: 'Expert', group: 'Languages' },
        { id: 'sk-5', name: 'Node.js & Express', level: 'Advanced', group: 'Backend' },
        { id: 'sk-6', name: 'PostgreSQL & MySQL', level: 'Advanced', group: 'Database' },
        { id: 'sk-7', name: 'Docker & Kubernetes', level: 'Advanced', group: 'DevOps' },
        { id: 'sk-8', name: 'AWS Services (EC2, S3, RDS)', level: 'Intermediate', group: 'Cloud' },
        { id: 'sk-9', name: 'System Architecture', level: 'Advanced', group: 'Core' },
        { id: 'sk-10', name: 'Agile & Scrum Leadership', level: 'Expert', group: 'Soft Skills' }
    ],

    projects: [
        {
            id: 'proj-1',
            name: 'Aura UI Component Library',
            link: 'github.com/alexc-dev/aura-ui',
            start_date: '2022',
            end_date: 'Present',
            description: 'Created and maintain an open-source, highly accessible Vue 3 component library utilized by over 500 developers globally. Features comprehensive documentation built with VitePress.'
        },
        {
            id: 'proj-2',
            name: 'Nova AI Dashboard',
            link: 'nova-analytics.io',
            start_date: '2020',
            end_date: '2021',
            description: 'Developed an AI-powered analytics dashboard that aggregates and visualizes complex marketing data in real-time, handling over 10M rows of data seamlessly.'
        }
    ],

    certifications: [
        {
            id: 'cert-1',
            name: 'AWS Certified Solutions Architect – Associate',
            issued_by: 'Amazon Web Services',
            issue_date: 'Oct 2022',
            link: 'aws.amazon.com/verification'
        },
        {
            id: 'cert-2',
            name: 'Certified Laravel Developer',
            issued_by: 'Laravel',
            issue_date: 'Jan 2020'
        }
    ],

    languages: [
        { id: 'lang-1', name: 'English', proficiency: 'Native' },
        { id: 'lang-2', name: 'Mandarin Chinese', proficiency: 'Bilingual' },
        { id: 'lang-3', name: 'Spanish', proficiency: 'Professional Working' }
    ],

    design_options: {
        template: 'classic',
        theme: 'classic', // Fallback for old templates
        accent_color: '#4f46e5', // Indigo-600
        font_family: 'Inter, system-ui, sans-serif',
        font_size: '9.5pt',
        line_height: 1.5,
        letter_spacing: 0,
        section_gap: 22, // Space between major sections
        margins: {
            top: 20,
            bottom: 20,
            left: 20,
            right: 20
        },
        section_order: ['summary', 'experience', 'education', 'skills', 'projects', 'certifications', 'languages'],
        hidden_sections: []
    }
};

/**
 * Returns a cloned instance of the dummy resume formatted for a specific template
 * if there are template-specific overrrides. Otherwise returns the global object.
 * 
 * @param {string} templateName The slug of the template to adapt the data for.
 * @returns {Object} A deep-cloned resume object.
 */
export const getDummyResume = (templateName = 'classic') => {
    // Deep clone to prevent mutations
    const clone = JSON.parse(JSON.stringify(globalDummyResume));

    // Set the template to trigger correct dynamic component rendering
    clone.template = templateName;
    clone.design_options.template = templateName;
    clone.design_options.theme = templateName;

    // Provide visually pleasing default accent colors based on template vibe
    const templateColors = {
        'classic': '#1e293b', // Slate 800
        'modern': '#4f46e5', // Indigo 600
        'minimal': '#0f172a', // Slate 900
        'creative': '#e11d48', // Rose 600
        'executive': '#0369a1', // Sky 700
        'professional': '#047857', // Emerald 700
        'elite': '#b45309', // Amber 700 (Bronze/Gold)
        'arabicpro': '#0f766e', // Teal 700
        'canvalux': '#7c3aed', // Violet 600
        'tech': '#0ea5e9', // Sky 500
        'startup': '#f97316', // Orange 500
        'academic': '#334155', // Slate 700
        'elegant': '#be185d', // Pink 700
    };

    if (templateColors[templateName]) {
        clone.design_options.accent_color = templateColors[templateName];
    }

    // Template-specific tweaks (e.g. Arabic Pro needs RTL data)
    if (templateName === 'arabicpro') {
        clone.fullname = 'أحمد محمد';
        clone.first_name = 'أحمد';
        clone.last_name = 'محمد';
        clone.job_title = 'مطور برمجيات أول وقائد فريق تقني';
        clone.jobtitle = clone.job_title;
        clone.summary = 'مطور برمجيات محترف ومبتكر يتمتع بخبرة تزيد عن 8 سنوات في تصميم الأنظمة القابلة للتوسع وقيادة الفرق الهندسية. خبير في Vue.js و Laravel وتقنيات الحوسبة السحابية. شغوف بتطوير بيئة عمل هندسية متميزة وربط الحلول التقنية المعقدة بتجربة مستخدم بسيطة ومذهلة.';
        clone.city = 'دبي';
        clone.country = 'الإمارات العربية المتحدة';

        clone.experiences[0].jobtitle = 'قائد فريق تقني';
        clone.experiences[0].company = 'شركة التقنية المتقدمة';
        clone.experiences[0].city = 'دبي، الإمارات';
        clone.experiences[0].description = '• قيادة فريق التطوير لبناء منصة سحابية تخدم أكثر من 50,000 مستخدم يومياً.\n• تحسين أداء قواعد البيانات وتقليل وقت استجابة النظام بنسبة 65%.';

        clone.education[0].degree = 'بكالوريوس في علوم الحاسوب';
        clone.education[0].institution = 'جامعة الملك فهد للبترول والمعادن';
        clone.education[0].city = 'الظهران، السعودية';

        clone.languages = [
            { id: 'lang-1', name: 'العربية', proficiency: 'اللغة الأم' },
            { id: 'lang-2', name: 'الإنجليزية', proficiency: 'طلاقة ممتازة' }
        ];
    }

    return clone;
};
