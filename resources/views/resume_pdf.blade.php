<!DOCTYPE html>
<html lang="{{ $resume->language ?? 'en' }}" dir="{{ in_array($resume->language, ['ar']) ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $resume->first_name }} {{ $resume->last_name }} — CV</title>
    <style>
        @page {
            margin: 0;
            size: a4 portrait;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 8.5pt;
            color: #1e1e2e;
            background: #fff;
            line-height: 1.3;
        }

        /* Table Layout Base */
        table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            table-layout: fixed;
        }

        td {
            vertical-align: top;
            padding: 0;
            border: none;
        }

        /* Header */
        .header {
            background:
                {{ $resume->accent_color ?? '#4f46e5' }}
            ;
            color: white;
            padding: 20px 30px;
        }

        .header h1 {
            font-size: 20pt;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .header .job-title {
            font-size: 10pt;
            opacity: 0.95;
            margin-bottom: 8px;
        }

        /* Contact Table in Header */
        .contact-table td {
            font-size: 7.5pt;
            color: rgba(255, 255, 255, 0.9);
            padding-right: 15px;
        }

        /* Main Layout Table */
        .layout-table {
            width: 100%;
        }

        .sidebar {
            width: 32%;
            background: #f9f9fc;
            padding: 20px 15px;
            border-right: 1px solid #eef;
        }

        .content {
            width: 68%;
            padding: 20px 25px;
        }

        /* RTL Swap for Sidebar Border */
        [dir="rtl"] .sidebar {
            border-right: none;
            border-left: 1px solid #eef;
        }

        /* Sections */
        .section {
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 8pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color:
                {{ $resume->accent_color ?? '#4f46e5' }}
            ;
            border-bottom: 1.5px solid
                {{ $resume->accent_color ?? '#4f46e5' }}
            ;
            padding-bottom: 3px;
            margin-bottom: 8px;
        }

        /* Professional Summary */
        .summary p {
            font-size: 8.5pt;
            color: #374151;
            text-align: justify;
        }

        /* Work/Edu Item Table */
        .item-table {
            width: 100%;
            margin-bottom: 2px;
        }

        .item-title {
            font-weight: 700;
            font-size: 9pt;
            color: #111827;
            width: 75%;
        }

        .item-date {
            font-size: 7.5pt;
            color: #6b7280;
            text-align: right;
            width: 25%;
        }

        [dir="rtl"] .item-date {
            text-align: left;
        }

        .item-subtitle {
            font-size: 8pt;
            color:
                {{ $resume->accent_color ?? '#4f46e5' }}
            ;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .item-desc {
            font-size: 8pt;
            color: #4b5563;
            line-height: 1.4;
        }

        /* Sidebar Skills */
        .skill-item {
            margin-bottom: 6px;
        }

        .skill-name {
            font-size: 8pt;
            color: #374151;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .skill-bar {
            height: 4px;
            background: #eef;
            border-radius: 2px;
        }

        .skill-bar-fill {
            height: 100%;
            background:
                {{ $resume->accent_color ?? '#4f46e5' }}
            ;
            border-radius: 2px;
        }

        /* Sidebar Lists */
        .sidebar-list-item {
            margin-bottom: 5px;
            font-size: 8pt;
        }

        .sidebar-list-label {
            font-weight: 700;
            color: #374151;
            display: block;
        }

        .sidebar-list-value {
            color: #6b7280;
            font-size: 7.5pt;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $resume->first_name }} {{ $resume->last_name }}</h1>
        @if($resume->job_title)
            <div class="job-title">{{ $resume->job_title }}</div>
        @endif

        <table class="contact-table">
            <tr>
                @php $has_contact = false; @endphp
                @if($resume->email)
                    <td>{{ $resume->email }}</td>
                    @php $has_contact = true; @endphp
                @endif
                @if($resume->phone)
                    <td>{{ $resume->phone }}</td>
                    @php $has_contact = true; @endphp
                @endif
                @if($resume->address)
                    <td>{{ $resume->address }}</td>
                    @php $has_contact = true; @endphp
                @endif
            </tr>
        </table>
    </div>

    <!-- Main Content Table -->
    <table class="layout-table">
        <tr>
            <!-- Sidebar TD -->
            <td class="sidebar">
                @if($resume->skills->count())
                    <div class="section">
                        <div class="section-title">Skills</div>
                        @foreach($resume->skills as $skill)
                            <div class="skill-item">
                                <div class="skill-name">{{ $skill->name }}</div>
                                @if($skill->level)
                                    <div class="skill-bar">
                                        <div class="skill-bar-fill"
                                            style="width: {{ ['Beginner' => 25, 'Intermediate' => 50, 'Advanced' => 75, 'Expert' => 100][$skill->level] ?? 60 }}%">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($resume->languages->count())
                    <div class="section">
                        <div class="section-title">Languages</div>
                        @foreach($resume->languages as $lang)
                            <div class="sidebar-list-item">
                                <span class="sidebar-list-label">{{ $lang->name }}</span>
                                <span class="sidebar-list-value">{{ $lang->proficiency }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($resume->certifications->count())
                    <div class="section">
                        <div class="section-title">Certifications</div>
                        @foreach($resume->certifications as $cert)
                            <div class="sidebar-list-item">
                                <div class="sidebar-list-label">{{ $cert->name }}</div>
                                @if($cert->issued_by)
                                    <div class="sidebar-list-value">
                                        {{ $cert->issued_by }}{{ $cert->issue_date ? ' | ' . $cert->issue_date : '' }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </td>

            <!-- Content TD -->
            <td class="content">
                @if($resume->summary)
                    <div class="section">
                        <div class="section-title">Professional Summary</div>
                        <div class="summary">
                            <p>{{ $resume->summary }}</p>
                        </div>
                    </div>
                @endif

                @if($resume->experiences->count())
                    <div class="section">
                        <div class="section-title">Experience</div>
                        @foreach($resume->experiences as $exp)
                            <div class="item">
                                <table class="item-table">
                                    <tr>
                                        <td class="item-title">{{ $exp->job_title }}</td>
                                        <td class="item-date">
                                            {{ $exp->start_date }}{{ $exp->current ? ' – Present' : ($exp->end_date ? ' – ' . $exp->end_date : '') }}
                                        </td>
                                    </tr>
                                </table>
                                <div class="item-subtitle">{{ $exp->company }}</div>
                                @if($exp->description)
                                    <div class="item-desc">{{ $exp->description }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($resume->education->count())
                    <div class="section">
                        <div class="section-title">Education</div>
                        @foreach($resume->education as $edu)
                            <div class="item">
                                <table class="item-table">
                                    <tr>
                                        <td class="item-title">{{ $edu->degree }}</td>
                                        <td class="item-date">
                                            {{ $edu->start_date }}{{ $edu->end_date ? ' – ' . $edu->end_date : '' }}
                                        </td>
                                    </tr>
                                </table>
                                <div class="item-subtitle">{{ $edu->institution }}</div>
                                @if($edu->description)
                                    <div class="item-desc">{{ $edu->description }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($resume->projects->count())
                    <div class="section">
                        <div class="section-title">Projects</div>
                        @foreach($resume->projects as $project)
                            <div class="item">
                                <div class="item-title">{{ $project->title }}</div>
                                @if($project->description)
                                    <div class="item-desc">{{ $project->description }}</div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </td>
        </tr>
    </table>
</body>

</html>