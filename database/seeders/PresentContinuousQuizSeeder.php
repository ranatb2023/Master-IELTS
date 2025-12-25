<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PresentContinuousQuizSeeder extends Seeder
{
    public function run()
    {
        try {
            DB::beginTransaction();

            // Insert Quiz
            $quizId = DB::table('quizzes')->insertGetId([
                'topic_id' => 5,
                'course_id' => 3,
                'title' => 'Present Continuous Tense (assignment)',
                'description' => 'Grammar quiz on Present Continuous tense',
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
                [
                    'question' => 'She ___ her homework right now.',
                    'options' => [
                        ['text' => 'Does', 'is_correct' => 0],
                        ['text' => 'Is doing', 'is_correct' => 1],
                        ['text' => 'Did', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'They ___ TV at the moment.',
                    'options' => [
                        ['text' => 'Watch', 'is_correct' => 0],
                        ['text' => 'Are watching', 'is_correct' => 1],
                        ['text' => 'Is watching', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'I ___ to my favorite song now.',
                    'options' => [
                        ['text' => 'Am listening', 'is_correct' => 1],
                        ['text' => 'Listen', 'is_correct' => 0],
                        ['text' => 'Listened', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'The children ___ in the park this afternoon.',
                    'options' => [
                        ['text' => 'Are playing', 'is_correct' => 1],
                        ['text' => 'Play', 'is_correct' => 0],
                        ['text' => 'Played', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'We ___ dinner together as we speak.',
                    'options' => [
                        ['text' => 'Have', 'is_correct' => 0],
                        ['text' => 'Are having', 'is_correct' => 1],
                        ['text' => 'Had', 'is_correct' => 0],
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
