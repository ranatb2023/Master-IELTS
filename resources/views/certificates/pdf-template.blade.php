<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate - {{ $data['certificate_number'] }}</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Georgia', serif;
            background: {{ $template->design['background_color'] ?? '#ffffff' }};
        }

        .certificate-container {
            width: 100%;
            height: 100%;
            padding: 40px;
            box-sizing: border-box;
            position: relative;
        }

        @if(isset($template->design['border']['enabled']) && $template->design['border']['enabled'])
        .certificate-container {
            border: {{ $template->design['border']['width'] ?? '10px' }} 
                    {{ $template->design['border']['style'] ?? 'double' }} 
                    {{ $template->design['border']['color'] ?? '#1e3a8a' }};
        }
        @endif

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header-title {
            font-size: {{ $template->design['header']['title_font_size'] ?? '36px' }};
            color: {{ $template->design['header']['title_color'] ?? '#1e3a8a' }};
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin: 0;
        }

        .header-subtitle {
            font-size: {{ $template->design['header']['subtitle_font_size'] ?? '18px' }};
            color: #666;
            margin-top: 10px;
        }

        .body-content {
            text-align: center;
            padding: 60px 40px;
        }

        .student-name {
            font-size: {{ $template->design['body']['student_name_font_size'] ?? '32px' }};
            color: {{ $template->design['body']['student_name_color'] ?? '#000000' }};
            font-weight: {{ $template->design['body']['student_name_font_weight'] ?? 'bold' }};
            margin: 20px 0;
            text-decoration: underline;
            text-decoration-color: {{ $template->design['header']['title_color'] ?? '#1e3a8a' }};
        }

        .description {
            font-size: {{ $template->design['body']['description_font_size'] ?? '16px' }};
            color: #666;
            margin: 15px 0;
        }

        .course-name {
            font-size: {{ $template->design['body']['course_name_font_size'] ?? '24px' }};
            color: {{ $template->design['body']['course_name_color'] ?? '#1e3a8a' }};
            font-weight: bold;
            margin: 20px 0;
        }

        .footer {
            position: absolute;
            bottom: 40px;
            width: calc(100% - 120px);
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .footer-left {
            text-align: left;
            font-size: 12px;
            color: #666;
        }

        .footer-center {
            text-align: center;
            font-size: 14px;
            color: #666;
        }

        .footer-right {
            text-align: right;
        }

        .certificate-number {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            color: #999;
            margin-top: 5px;
        }

        .qr-code {
            width: 100px;
            height: 100px;
        }

        .signature-line {
            width: 200px;
            border-top: 2px solid #000;
            margin: 10px auto 5px;
        }

        .signature-label {
            font-size: 12px;
            color: #666;
        }

        .date {
            font-size: 16px;
            color: #666;
            margin: 20px 0;
        }

        .platform-name {
            font-size: 14px;
            color: #999;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <!-- Header -->
        <div class="header">
            <h1 class="header-title">{{ $template->design['header']['title'] ?? 'Certificate of Completion' }}</h1>
            <p class="header-subtitle">{{ $template->design['header']['subtitle'] ?? 'This certifies that' }}</p>
        </div>

        <!-- Body -->
        <div class="body-content">
            <div class="student-name">{{ $data['student_name'] }}</div>
            
            <p class="description">
                {{ $template->design['body']['description_text'] ?? 'has successfully completed the course' }}
            </p>
            
            <div class="course-name">{{ $data['course_name'] }}</div>
            
            <p class="date">on {{ $data['completion_date'] }}</p>

            @if(isset($template->design['footer']['signature_enabled']) && $template->design['footer']['signature_enabled'])
                <div style="margin-top: 60px;">
                    <div class="signature-line"></div>
                    <p class="signature-label">{{ $data['instructor_name'] }}</p>
                    <p class="signature-label">Course Instructor</p>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            @php
                $certNumberPosition = $template->design['footer']['certificate_number_position'] ?? 'bottom-left';
                $verificationPosition = $template->design['footer']['verification_url_position'] ?? 'bottom-right';
            @endphp

            @if($certNumberPosition === 'bottom-left')
                <div class="footer-left">
                    <div class="certificate-number">Certificate ID: {{ $data['certificate_number'] }}</div>
                    <div class="certificate-number">Issued: {{ $data['issue_date'] }}</div>
                    <div class="platform-name">{{ $data['platform_name'] }}</div>
                </div>
            @endif

            @if($certNumberPosition === 'bottom-center' || $verificationPosition === 'bottom-center')
                <div class="footer-center">
                    @if($certNumberPosition === 'bottom-center')
                        <div class="certificate-number">{{ $data['certificate_number'] }}</div>
                    @endif
                    @if($verificationPosition === 'bottom-center')
                        <div class="certificate-number" style="font-size: 10px; word-break: break-all;">
                            Verify at: {{ $data['verification_url'] }}
                        </div>
                    @endif
                </div>
            @endif

            @if($verificationPosition === 'bottom-right')
                <div class="footer-right">
                    <div class="qr-code">{!! $data['qr_code'] !!}</div>
                    <div class="certificate-number" style="text-align: center;">Scan to Verify</div>
                </div>
            @endif
        </div>
    </div>
</body>
</html>
