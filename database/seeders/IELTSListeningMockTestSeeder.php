<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IELTSListeningMockTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();

            // Create IELTS Listening Mock Test Quiz
            $quizId = DB::table('quizzes')->insertGetId([
                'topic_id' => 11,
                'course_id' => 6,
                'title' => 'IELTS Listening Mock Test',
                'description' => 'Complete IELTS Listening practice test with 20 questions',
                'instructions' => 'Listen carefully to each audio clip and answer the questions.',
                'time_limit' => 30,
                'passing_score' => 60.00,
                'max_attempts' => null,
                'shuffle_questions' => 0,
                'shuffle_answers' => 0,
                'show_answers' => 'after_submission',
                'show_correct_answers' => 1,
                'require_passing' => 0,
                'certificate_eligible' => 0,
                'order' => 0,
                'is_published' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $questions = [
                [
                    'type' => 2,
                    'question' => 'What is the main purpose of the phone call?',
                    'options' => [
                        ['text' => 'To make a hotel reservation', 'is_correct' => 1],
                        ['text' => 'To cancel a booking', 'is_correct' => 0],
                        ['text' => 'To complain about service', 'is_correct' => 0],
                        ['text' => 'To ask for directions', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'When does the conference start?',
                    'options' => [
                        ['text' => 'Monday morning', 'is_correct' => 0],
                        ['text' => 'Tuesday afternoon', 'is_correct' => 1],
                        ['text' => 'Wednesday evening', 'is_correct' => 0],
                        ['text' => 'Thursday morning', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'How much does the standard room cost per night?',
                    'options' => [
                        ['text' => '£75', 'is_correct' => 0],
                        ['text' => '£85', 'is_correct' => 1],
                        ['text' => '£95', 'is_correct' => 0],
                        ['text' => '£105', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'What does the speaker recommend for breakfast?',
                    'options' => [
                        ['text' => 'Continental breakfast', 'is_correct' => 0],
                        ['text' => 'Full English breakfast', 'is_correct' => 1],
                        ['text' => 'Room service', 'is_correct' => 0],
                        ['text' => 'The café next door', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'Which facility is NOT available at the hotel?',
                    'options' => [
                        ['text' => 'Swimming pool', 'is_correct' => 0],
                        ['text' => 'Gym', 'is_correct' => 0],
                        ['text' => 'Tennis court', 'is_correct' => 1],
                        ['text' => 'Restaurant', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'What time is check-out?',
                    'options' => [
                        ['text' => '10:00 AM', 'is_correct' => 0],
                        ['text' => '11:00 AM', 'is_correct' => 1],
                        ['text' => '12:00 PM', 'is_correct' => 0],
                        ['text' => '1:00 PM', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'Where is the nearest bus stop located?',
                    'options' => [
                        ['text' => 'Opposite the hotel entrance', 'is_correct' => 1],
                        ['text' => 'Two blocks away', 'is_correct' => 0],
                        ['text' => 'Behind the hotel', 'is_correct' => 0],
                        ['text' => 'At the train station', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'What is the cancellation policy?',
                    'options' => [
                        ['text' => '24 hours notice required', 'is_correct' => 0],
                        ['text' => '48 hours notice required', 'is_correct' => 1],
                        ['text' => '72 hours notice required', 'is_correct' => 0],
                        ['text' => 'No cancellation allowed', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 1,
                    'question' => 'The library opens at 8:00 AM on weekdays.',
                    'options' => [
                        ['text' => 'True', 'is_correct' => 1],
                        ['text' => 'False', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 1,
                    'question' => 'Students can borrow up to 10 books at a time.',
                    'options' => [
                        ['text' => 'True', 'is_correct' => 0],
                        ['text' => 'False', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 1,
                    'question' => 'The museum is closed on Mondays.',
                    'options' => [
                        ['text' => 'True', 'is_correct' => 1],
                        ['text' => 'False', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 1,
                    'question' => 'Free WiFi is available throughout the building.',
                    'options' => [
                        ['text' => 'True', 'is_correct' => 1],
                        ['text' => 'False', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 1,
                    'question' => 'The parking lot charges £5 per hour.',
                    'options' => [
                        ['text' => 'True', 'is_correct' => 0],
                        ['text' => 'False', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 1,
                    'question' => 'Photography is permitted in all exhibition rooms.',
                    'options' => [
                        ['text' => 'True', 'is_correct' => 0],
                        ['text' => 'False', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'The speaker\'s name is _____ Johnson.',
                    'options' => [
                        ['text' => 'Michael', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'The course starts on _____ September.',
                    'options' => [
                        ['text' => '15th', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'The meeting room is located on the _____ floor.',
                    'options' => [
                        ['text' => 'third', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Please bring your _____ and a notepad to the session.',
                    'options' => [
                        ['text' => 'laptop', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'The registration fee is _____ pounds.',
                    'options' => [
                        ['text' => '250', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'For more information, visit www._____.com',
                    'options' => [
                        ['text' => 'ielts-prep', 'is_correct' => 1],
                    ]
                ],
            ];

            foreach ($questions as $index => $questionData) {
                $questionId = DB::table('questions')->insertGetId([
                    'quiz_id' => $quizId,
                    'question_type_id' => $questionData['type'],
                    'question' => $questionData['question'],
                    'description' => null,
                    'points' => 1.00,
                    'order' => $index + 1,
                    'media_type' => 'none',
                    'media_url' => null,
                    'explanation' => null,
                    'difficulty' => 'medium',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach ($questionData['options'] as $optionIndex => $option) {
                    DB::table('question_options')->insert([
                        'question_id' => $questionId,
                        'option_text' => $option['text'],
                        'is_correct' => $option['is_correct'],
                        'order' => $optionIndex + 1,
                        'explanation' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            DB::commit();
            $this->command->info("✅ IELTS Listening Mock Test created - Quiz ID: $quizId - 20 questions");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("❌ Error: " . $e->getMessage());
        }
    }
}
