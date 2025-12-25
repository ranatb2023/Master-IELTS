<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PastContinuousQuizSeeder extends Seeder
{
    public function run()
    {
        try {
            DB::beginTransaction();

            // Insert Quiz
            $quizId = DB::table('quizzes')->insertGetId([
                'topic_id' => 5,
                'course_id' => 3,
                'title' => 'Past Continuous Tense Exercise (assignment)',
                'description' => 'Grammar quiz on Past Continuous tense with multiple choice and fill-in-the-blank questions',
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
                    'question' => 'She ___ to music when I called her.',
                    'options' => [
                        ['text' => 'was listening', 'is_correct' => 1],
                        ['text' => 'listened', 'is_correct' => 0],
                        ['text' => 'is listening', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'They ___ in the park while it started to rain.',
                    'options' => [
                        ['text' => 'are playing', 'is_correct' => 0],
                        ['text' => 'were playing', 'is_correct' => 1],
                        ['text' => 'played', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'I ___ my homework at 8 PM yesterday.',
                    'options' => [
                        ['text' => 'was doing', 'is_correct' => 1],
                        ['text' => 'did', 'is_correct' => 0],
                        ['text' => 'have been doing', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'He ___ his car when the accident happened.',
                    'options' => [
                        ['text' => 'was driving', 'is_correct' => 1],
                        ['text' => 'drove', 'is_correct' => 0],
                        ['text' => 'drives', 'is_correct' => 0],
                    ]
                ],
                [
                    'type' => 2,
                    'question' => 'We ___ TV all evening last night.',
                    'options' => [
                        ['text' => 'Watched', 'is_correct' => 0],
                        ['text' => 'were watching', 'is_correct' => 1],
                        ['text' => 'are watching', 'is_correct' => 0],
                    ]
                ],
                // Fill in the Blanks Questions (6-10)
                [
                    'type' => 5, // Fill in the Blanks
                    'question' => 'Fill in the blanks: She _____ (study) for her exam when the lights went out.',
                    'options' => [
                        ['text' => 'was studying', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Fill in the blanks: They _____ (not sleep) when the alarm rang.',
                    'options' => [
                        ['text' => 'weren\'t sleeping', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Fill in the blanks: I _____ (work) on my project while my friends were watching TV.',
                    'options' => [
                        ['text' => 'was working', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Fill in the blanks: We _____ (wait) for the bus when it started to rain.',
                    'options' => [
                        ['text' => 'were waiting', 'is_correct' => 1],
                    ]
                ],
                [
                    'type' => 5,
                    'question' => 'Fill in the blanks: He _____ (not listen) to the teacher during the lesson.',
                    'options' => [
                        ['text' => 'wasn\'t listening', 'is_correct' => 1],
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
