<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormalVsInformalQuizSeeder extends Seeder
{
    public function run()
    {
        try {
            DB::beginTransaction();

            // Insert Quiz
            $quizId = DB::table('quizzes')->insertGetId([
                'topic_id' => 4,
                'course_id' => 3,
                'title' => 'Formal vs Informal',
                'description' => 'Quiz on identifying formal and informal language',
                'instructions' => 'Please select the correct answer for each question.',
                'time_limit' => null,
                'passing_score' => 70.00,
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

            $this->command->info("Quiz created with ID: $quizId");

            // Questions and their options
            $questions = [
                [
                    'question' => 'Which of the following greetings is formal?',
                    'options' => [
                        ['text' => 'Hey (Name),', 'is_correct' => 0],
                        ['text' => 'Dear Mr.Smith,', 'is_correct' => 1],
                        ['text' => 'Hi, there,', 'is_correct' => 0],
                        ['text' => 'What\'s up?', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'Which phrase would be more suitable for an informal letter?',
                    'options' => [
                        ['text' => 'I would like to inform you that…', 'is_correct' => 0],
                        ['text' => 'I\'m really excited about…', 'is_correct' => 1],
                        ['text' => 'I would appreciate your prompt response…', 'is_correct' => 0],
                        ['text' => 'It has come to my attention that…', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'In a formal letter, how should you close the letter?',
                    'options' => [
                        ['text' => 'Cheers,', 'is_correct' => 0],
                        ['text' => 'See you soon,', 'is_correct' => 0],
                        ['text' => 'Yours sincerely,', 'is_correct' => 1],
                        ['text' => 'Best wishes,', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'Which of the following sentences is formal?',
                    'options' => [
                        ['text' => 'Please let me know if you can attend the meeting.', 'is_correct' => 1],
                        ['text' => 'Could you drop me a line when you get a chance?', 'is_correct' => 0],
                        ['text' => 'Just give me a call whenever you\'re free.', 'is_correct' => 0],
                        ['text' => 'Can\'t wait to hear from you!', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'Which expression is best for a formal complaint letter?',
                    'options' => [
                        ['text' => 'I hope you can fix this soon.', 'is_correct' => 0],
                        ['text' => 'This is really frustrating.', 'is_correct' => 0],
                        ['text' => 'I am writing to express my dissatisfaction with…', 'is_correct' => 1],
                        ['text' => 'Thanks for sorting this out.', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'I am looking forward to hearing from you at your earliest convenience.',
                    'options' => [
                        ['text' => 'Formal', 'is_correct' => 1],
                        ['text' => 'Informal', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'I can\'t wait to see you next weekend!',
                    'options' => [
                        ['text' => 'Formal', 'is_correct' => 0],
                        ['text' => 'Informal', 'is_correct' => 1],
                    ]
                ],
                [
                    'question' => 'I would like to request further information regarding the course.',
                    'options' => [
                        ['text' => 'Formal', 'is_correct' => 1],
                        ['text' => 'Informal', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'Thanks for your help. You\'re the best!',
                    'options' => [
                        ['text' => 'Formal', 'is_correct' => 0],
                        ['text' => 'Informal', 'is_correct' => 1],
                    ]
                ],
                [
                    'question' => 'Could you kindly assist me with the arrangements?',
                    'options' => [
                        ['text' => 'Informal', 'is_correct' => 0],
                        ['text' => 'Formal', 'is_correct' => 1],
                    ]
                ],
            ];

            foreach ($questions as $index => $questionData) {
                // Insert question
                $questionId = DB::table('questions')->insertGetId([
                    'quiz_id' => $quizId,
                    'question_type_id' => 2, // MCQ Single Answer
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

                $this->command->info("Question " . ($index + 1) . " created with ID: $questionId");

                // Insert options
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
            $this->command->info("\n✅ Successfully inserted quiz with all questions and options!");
            $this->command->info("Quiz ID: $quizId");
            $this->command->info("Total Questions: " . count($questions));

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("❌ Error: " . $e->getMessage());
            $this->command->error($e->getTraceAsString());
        }
    }
}
