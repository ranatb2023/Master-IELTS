<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PresentPerfectContinuousQuizSeeder extends Seeder
{
    public function run()
    {
        try {
            DB::beginTransaction();

            // Insert Quiz
            $quizId = DB::table('quizzes')->insertGetId([
                'topic_id' => 5,
                'course_id' => 3,
                'title' => 'Present Perfect Continuous Tense Exercise (assignment)',
                'description' => 'Grammar quiz on Present Perfect Continuous tense with multiple choice and fill-in-the-blank questions',
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
                    'question' => 'She ___ for the exam since morning.',
                    'options' => [
                        ['text' => 'has been studying', 'is_correct' => 1],
                        ['text' => 'is studying', 'is_correct' => 0],
                        ['text' => 'has studied', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'They ___ in the garden for two hours.',
                    'options' => [
                        ['text' => 'have worked', 'is_correct' => 0],
                        ['text' => 'have been working', 'is_correct' => 1],
                        ['text' => 'worked', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'I ___ to learn Spanish for three months.',
                    'options' => [
                        ['text' => 'am trying', 'is_correct' => 0],
                        ['text' => 'have been trying', 'is_correct' => 1],
                        ['text' => 'have tried', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'He ___ the guitar since he was 10 years old.',
                    'options' => [
                        ['text' => 'has been playing', 'is_correct' => 1],
                        ['text' => 'is playing', 'is_correct' => 0],
                        ['text' => 'plays', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'We ___ at the same company for many years.',
                    'options' => [
                        ['text' => 'have been working', 'is_correct' => 1],
                        ['text' => 'worked', 'is_correct' => 0],
                        ['text' => 'work', 'is_correct' => 0],
                    ]
                ],
                // Fill in the Blanks Questions (6-10)
                [
                    'type' => 5, // Fill in the Blanks
                    'question' => 'Fill in the blanks: She _____ (practice) the piano for an hour.',
                    'options' => [
                        ['text' => 'has been practicing', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Fill in the blanks: They _____ (not work) on the project since Monday.',
                    'options' => [
                        ['text' => 'haven\'t been working', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Fill in the blanks: I _____ (read) this novel for the past week.',
                    'options' => [
                        ['text' => 'have been reading', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Fill in the blanks: We _____ (wait) for the bus for 20 minutes.',
                    'options' => [
                        ['text' => 'have been waiting', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Fill in the blanks: He _____ (live) in this city since 2015.',
                    'options' => [
                        ['text' => 'has been living', 'is_correct' => 1],
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
