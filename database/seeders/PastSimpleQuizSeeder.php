<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PastSimpleQuizSeeder extends Seeder
{
    public function run()
    {
        try {
            DB::beginTransaction();

            // Insert Quiz
            $quizId = DB::table('quizzes')->insertGetId([
                'topic_id' => 5,
                'course_id' => 3,
                'title' => 'Past Simple Tense Exercise (assignment)',
                'description' => 'Grammar quiz on Past Simple tense with multiple choice and fill-in-the-blank questions',
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
                    'question' => 'She ___ to the park yesterday',
                    'options' => [
                        ['text' => 'Go', 'is_correct' => 0],
                        ['text' => 'Goes', 'is_correct' => 0],
                        ['text' => 'Went', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'They ___ their homework last night.',
                    'options' => [
                        ['text' => 'Finished', 'is_correct' => 1],
                        ['text' => 'Finish', 'is_correct' => 0],
                        ['text' => 'Finishes', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'I ___ a movie with my friends last weekend.',
                    'options' => [
                        ['text' => 'Watch', 'is_correct' => 0],
                        ['text' => 'Watched', 'is_correct' => 1],
                        ['text' => 'Watches', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'The teacher ___ very angry when we didn\'t listen.',
                    'options' => [
                        ['text' => 'Is', 'is_correct' => 0],
                        ['text' => 'Was', 'is_correct' => 1],
                        ['text' => 'Were', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'We ___ the train because we arrived late.',
                    'options' => [
                        ['text' => 'Missed', 'is_correct' => 1],
                        ['text' => 'Miss', 'is_correct' => 0],
                        ['text' => 'Missing', 'is_correct' => 0],
                    ]
                ],
                // Fill in the Blanks Questions (6-10)
                [
                    'type' => 5, // Fill in the Blanks
                    'question' => 'Fill in the blanks: She _____ (buy) a new dress for the party.',
                    'options' => [
                        ['text' => 'Bought', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Fill in the blanks: They _____ (not go) to school because it was raining.',
                    'options' => [
                        ['text' => 'didn\'t go', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Fill in the blanks: I _____(see) a beautiful bird in the garden this morning.',
                    'options' => [
                        ['text' => 'Saw', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Fill in the blanks: We _____ (play) football for two hours yesterday.',
                    'options' => [
                        ['text' => 'Played', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Fill in the blanks: He _____ (not understand) the question, so he didn\'t answer.',
                    'options' => [
                        ['text' => 'didn\'t understand', 'is_correct' => 1],
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
