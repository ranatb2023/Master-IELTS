<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PastPerfectQuizSeeder extends Seeder
{
    public function run()
    {
        try {
            DB::beginTransaction();

            // Insert Quiz
            $quizId = DB::table('quizzes')->insertGetId([
                'topic_id' => 5,
                'course_id' => 3,
                'title' => 'Past Perfect Tense Exercise (assignment)',
                'description' => 'Grammar quiz on Past Perfect tense with multiple choice and fill-in-the-blank questions',
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
                    'question' => 'She ___ the book before the library closed.',
                    'options' => [
                        ['text' => 'had returned', 'is_correct' => 1],
                        ['text' => 'returned', 'is_correct' => 0],
                        ['text' => 'was returning', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'By the time we arrived, they ___ already ___ dinner.',
                    'options' => [
                        ['text' => 'had, finished', 'is_correct' => 1],
                        ['text' => 'have, finished', 'is_correct' => 0],
                        ['text' => 'finished', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'I ___ the report before the deadline.',
                    'options' => [
                        ['text' => 'Completed', 'is_correct' => 0],
                        ['text' => 'had completed', 'is_correct' => 1],
                        ['text' => 'was completing', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'He ___ the movie because he had already seen it.',
                    'options' => [
                        ['text' => 'didn\'t enjoy', 'is_correct' => 1],
                        ['text' => 'hadn\'t enjoyed', 'is_correct' => 0],
                        ['text' => 'had enjoyed', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'They ___ their homework before the teacher collected it.',
                    'options' => [
                        ['text' => 'had done', 'is_correct' => 1],
                        ['text' => 'did', 'is_correct' => 0],
                        ['text' => 'have done', 'is_correct' => 0],
                    ]
                ],
                // Fill in the Blanks Questions (6-10)
                [
                    'type' => 5, // Fill in the Blanks
                    'question' => 'Fill in the blanks: She _____ (finish) her homework before dinner.',
                    'options' => [
                        ['text' => 'had finished', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Fill in the blanks: They _____ (not leave) the house when the storm started.',
                    'options' => [
                        ['text' => 'hadn\'t left', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Fill in the blanks: I _____ (already read) the book before I watched the movie.',
                    'options' => [
                        ['text' => 'had already read', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Fill in the blanks: We _____ (meet) her several times before she moved to another city.',
                    'options' => [
                        ['text' => 'had met', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Fill in the blanks: He _____ (not clean) the kitchen before his mom arrived.',
                    'options' => [
                        ['text' => 'hadn\'t cleaned', 'is_correct' => 1],
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
