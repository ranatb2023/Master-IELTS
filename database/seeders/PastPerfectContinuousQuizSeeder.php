<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PastPerfectContinuousQuizSeeder extends Seeder
{
    public function run()
    {
        try {
            DB::beginTransaction();

            // Insert Quiz
            $quizId = DB::table('quizzes')->insertGetId([
                'topic_id' => 5,
                'course_id' => 3,
                'title' => 'Past Perfect Continuous Tense Exercise (assignment)',
                'description' => 'Grammar quiz on Past Perfect Continuous tense with multiple choice and fill-in-the-blank questions',
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
                    'question' => 'She ___ for two hours before she finally found the address.',
                    'options' => [
                        ['text' => 'was looking', 'is_correct' => 0],
                        ['text' => 'had been looking', 'is_correct' => 1],
                        ['text' => 'had looked', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'They ___ in the park for 30 minutes when it started raining.',
                    'options' => [
                        ['text' => 'had been walking', 'is_correct' => 1],
                        ['text' => 'were walking', 'is_correct' => 0],
                        ['text' => 'walked', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'I ___ to call you, but my phone had no signal.',
                    'options' => [
                        ['text' => 'had been trying', 'is_correct' => 1],
                        ['text' => 'tried', 'is_correct' => 0],
                        ['text' => 'was trying', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'He ___ all day, so he was extremely tired in the evening.',
                    'options' => [
                        ['text' => 'had been working', 'is_correct' => 1],
                        ['text' => 'worked', 'is_correct' => 0],
                        ['text' => 'was working', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'We ___ about the project for hours before we finally agreed on a plan.',
                    'options' => [
                        ['text' => 'were talking', 'is_correct' => 0],
                        ['text' => 'had been talking', 'is_correct' => 1],
                        ['text' => 'talked', 'is_correct' => 0],
                    ]
                ],
                // Fill in the Blanks Questions (6-10)
                [
                    'type' => 5, // Fill in the Blanks
                    'question' => 'Fill in the blanks: She _____ (wait) for the bus for 20 minutes when it arrived.',
                    'options' => [
                        ['text' => 'had been waiting', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Fill in the blanks: They _____ (not study) long before the teacher entered the room.',
                    'options' => [
                        ['text' => 'hadn\'t been studying', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Fill in the blanks: I _____ (write) my essay for hours when the power went out.',
                    'options' => [
                        ['text' => 'had been writing', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Fill in the blanks: We _____ (not exercise) enough, so we felt tired during the hike.',
                    'options' => [
                        ['text' => 'hadn\'t been exercising', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Fill in the blanks: He _____ (practice) the piano for months before the recital.',
                    'options' => [
                        ['text' => 'had been practicing', 'is_correct' => 1],
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
