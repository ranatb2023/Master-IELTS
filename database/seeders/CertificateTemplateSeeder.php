<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CertificateTemplate;

class CertificateTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'IELTS Professional Certificate',
                'description' => 'Professional certificate template for IELTS course completion',
                'orientation' => 'landscape',
                'page_size' => 'A4',
                'is_default' => true,
                'is_active' => true,
                'design' => [
                    'background_color' => '#ffffff',
                    'border' => [
                        'enabled' => true,
                        'color' => '#1e3a8a',
                        'width' => '10px',
                        'style' => 'double'
                    ],
                    'header' => [
                        'logo' => true,
                        'title' => 'Certificate of Completion',
                        'title_font_size' => '36px',
                        'title_color' => '#1e3a8a',
                        'subtitle' => 'This certifies that',
                        'subtitle_font_size' => '18px'
                    ],
                    'body' => [
                        'student_name_font_size' => '32px',
                        'student_name_color' => '#000000',
                        'student_name_font_weight' => 'bold',
                        'course_name_font_size' => '24px',
                        'course_name_color' => '#1e3a8a',
                        'description_text' => 'has successfully completed the course',
                        'description_font_size' => '16px'
                    ],
                    'footer' => [
                        'date_format' => 'F j, Y',
                        'certificate_number_position' => 'bottom-left',
                        'verification_url_position' => 'bottom-right',
                        'signature_enabled' => true
                    ]
                ],
                'fields' => [
                    'student_name' => '{{student_name}}',
                    'course_name' => '{{course_name}}',
                    'completion_date' => '{{completion_date}}',
                    'certificate_number' => '{{certificate_number}}',
                    'verification_url' => '{{verification_url}}',
                    'instructor_name' => '{{instructor_name}}',
                    'course_duration' => '{{course_duration}}'
                ]
            ],
            [
                'name' => 'Modern Minimalist',
                'description' => 'Clean and modern certificate design with minimal elements',
                'orientation' => 'landscape',
                'page_size' => 'A4',
                'is_default' => false,
                'is_active' => true,
                'design' => [
                    'background_color' => '#f8f9fa',
                    'border' => [
                        'enabled' => true,
                        'color' => '#6366f1',
                        'width' => '3px',
                        'style' => 'solid'
                    ],
                    'header' => [
                        'logo' => true,
                        'title' => 'Certificate',
                        'title_font_size' => '48px',
                        'title_color' => '#6366f1',
                        'subtitle' => 'OF ACHIEVEMENT',
                        'subtitle_font_size' => '14px'
                    ],
                    'body' => [
                        'student_name_font_size' => '36px',
                        'student_name_color' => '#1f2937',
                        'student_name_font_weight' => 'bold',
                        'course_name_font_size' => '22px',
                        'course_name_color' => '#6366f1',
                        'description_text' => 'successfully completed',
                        'description_font_size' => '16px'
                    ],
                    'footer' => [
                        'date_format' => 'M d, Y',
                        'certificate_number_position' => 'bottom-center',
                        'verification_url_position' => 'bottom-center',
                        'signature_enabled' => false
                    ]
                ],
                'fields' => [
                    'student_name' => '{{student_name}}',
                    'course_name' => '{{course_name}}',
                    'completion_date' => '{{completion_date}}',
                    'certificate_number' => '{{certificate_number}}',
                    'verification_url' => '{{verification_url}}'
                ]
            ],
            [
                'name' => 'Classic Formal',
                'description' => 'Traditional formal certificate with elegant styling',
                'orientation' => 'portrait',
                'page_size' => 'A4',
                'is_default' => false,
                'is_active' => true,
                'design' => [
                    'background_color' => '#fffef7',
                    'border' => [
                        'enabled' => true,
                        'color' => '#8b4513',
                        'width' => '8px',
                        'style' => 'solid'
                    ],
                    'header' => [
                        'logo' => true,
                        'title' => 'Certificate of Excellence',
                        'title_font_size' => '32px',
                        'title_color' => '#8b4513',
                        'subtitle' => 'This is to certify that',
                        'subtitle_font_size' => '16px'
                    ],
                    'body' => [
                        'student_name_font_size' => '28px',
                        'student_name_color' => '#000000',
                        'student_name_font_weight' => 'bold',
                        'course_name_font_size' => '20px',
                        'course_name_color' => '#8b4513',
                        'description_text' => 'has demonstrated proficiency in',
                        'description_font_size' => '14px'
                    ],
                    'footer' => [
                        'date_format' => 'F j, Y',
                        'certificate_number_position' => 'bottom-left',
                        'verification_url_position' => 'bottom-right',
                        'signature_enabled' => true
                    ]
                ],
                'fields' => [
                    'student_name' => '{{student_name}}',
                    'course_name' => '{{course_name}}',
                    'completion_date' => '{{completion_date}}',
                    'certificate_number' => '{{certificate_number}}',
                    'verification_url' => '{{verification_url}}',
                    'instructor_name' => '{{instructor_name}}'
                ]
            ]
        ];

        foreach ($templates as $template) {
            CertificateTemplate::create($template);
        }
    }
}