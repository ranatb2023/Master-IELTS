<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PresentPerfectQuizSeeder extends Seeder
{
    public function run()
    {
        try {
            DB::beginTransaction();

            // Insert Quiz
            $quizId = DB::table('quizzes')->insertGetId([
                'topic_id' => 5,
                'course_id' => 3,
                'title' => 'Present Perfect Tense Exercise (Assignment)',
                'description' => 'Grammar quiz on Present Perfect tense with multiple choice and fill-in-the-blank questions',
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
                    'question' => 'She ___ already ___ her homework.',
                    'options' => [
                        ['text' => 'has, finished', 'is_correct' => 1],
                        ['text' => 'is, finishing', 'is_correct' => 0],
                        ['text' => 'have, finished', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'They ___ in this city for five years.',
                    'options' => [
                        ['text' => 'Lived', 'is_correct' => 0],
                        ['text' => 'have lived', 'is_correct' => 1],
                        ['text' => 'are living', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'I ___ never ___ that movie before.',
                    'options' => [
                        ['text' => 'has, seen', 'is_correct' => 0],
                        ['text' => 'have, seen', 'is_correct' => 1],
                        ['text' => 'had, seen', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'He ___ just ___ his breakfast.',
                    'options' => [
                        ['text' => 'has, eaten', 'is_correct' => 1],
                        ['text' => 'have, eaten', 'is_correct' => 0],
                        ['text' => 'is, eating', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'We ___ to that new restaurant yet.',
                    'options' => [
                        ['text' => 'have not gone', 'is_correct' => 1],
                        ['text' => 'did not go', 'is_correct' => 0],
                        ['text' => 'are not going', 'is_correct' => 0],
                    ]
                ],
                // Fill in the Blanks Questions (6-10)
                [
                    'type' => 5, // Fill in the Blanks
                    'question' => 'Fill in the blanks: She _____ (visit) Paris three times.',
                    'options' => [
                        ['text' => 'has visited', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Fill in the blanks: They _____ (not finish) their project yet.',
                    'options' => [
                        ['text' => 'haven\'t finished', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Fill in the blanks: I _____ (already see) this movie twice.',
                    'options' => [
                        ['text' => 'have already seen', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Fill in the blanks: We _____ (never eat) sushi before.',
                    'options' => [
                        ['text' => 'have never eaten', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Fill in the blanks: He _____ (just arrive) at the airport.',
                    'options' => [
                        ['text' => 'has just arrived', 'is_correct' => 1],
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
