<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FutureTenseQuizSeeder extends Seeder
{
    public function run()
    {
        try {
            DB::beginTransaction();

            // Insert Quiz
            $quizId = DB::table('quizzes')->insertGetId([
                'topic_id' => 5,
                'course_id' => 3,
                'title' => 'Future Tense Exercise (assignment)',
                'description' => 'Grammar quiz on Future Tense with multiple choice and fill-in-the-blank questions',
                'instructions' => 'Choose the correct answer for each question.',
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
                // MCQ Single Answer Questions (1-5)
                [
                    'type' => 2, // MCQ Single
                    'question' => 'She ___ her homework tomorrow evening.',
                    'options' => [
                        ['text' => 'will do', 'is_correct' => 1],
                        ['text' => 'does', 'is_correct' => 0],
                        ['text' => 'is doing', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'They ___ football at 3 PM next Sunday.',
                    'options' => [
                        ['text' => 'will play', 'is_correct' => 1],
                        ['text' => 'will be playing', 'is_correct' => 0],
                        ['text' => 'are playing', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'By this time next year, we ___ in a new city.',
                    'options' => [
                        ['text' => 'will live', 'is_correct' => 1],
                        ['text' => 'will be living', 'is_correct' => 0],
                        ['text' => 'will have been living', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'He ___ the project by the time the boss arrives.',
                    'options' => [
                        ['text' => 'will complete', 'is_correct' => 0],
                        ['text' => 'will be completing', 'is_correct' => 0],
                        ['text' => 'will have completed', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'By 5 PM, she ___ for three hours.',
                    'options' => [
                        ['text' => 'will study', 'is_correct' => 0],
                        ['text' => 'will have studied', 'is_correct' => 0],
                        ['text' => 'will have been studying', 'is_correct' => 1],
                    ]
                ],
                // Fill in the Blanks Questions (6-10)
                [
                    'type' => 5, // Fill in the Blanks
                    'question' => 'Fill in the blanks: She _____ (travel) to Japan next month.',
                    'options' => [
                        ['text' => 'will travel', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Fill in the blanks: At 7 PM tomorrow, they _____ (watch) the football match.',
                    'options' => [
                        ['text' => 'will be watching', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Fill in the blanks: By 2025, I _____ (finish) my degree.',
                    'options' => [
                        ['text' => 'will have finished', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Fill in the blanks: He _____ (not complete) the task by the deadline if he doesn\'t start now.',
                    'options' => [
                        ['text' => 'won\'t complete', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Fill in the blanks: By the end of this year, we _____ (work) on the project for six months.',
                    'options' => [
                        ['text' => 'will have been working', 'is_correct' => 1],
                    ]
                ],
            ];

            foreach ($questions as $index => $questionData) {
                // Insert question
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

                $this->command->info("Question " . ($index + 1) . " created with ID: $questionId (Type: " . ($questionData['type'] == 2 ? 'MCQ' : 'Fill in the Blanks') . ")");

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
            $this->command->info("Total Questions: " . count($questions) . " (5 MCQ + 5 Fill in the Blanks)");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("❌ Error: " . $e->getMessage());
            $this->command->error($e->getTraceAsString());
        }
    }
}
