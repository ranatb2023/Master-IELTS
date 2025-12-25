<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PresentSimpleQuizSeeder extends Seeder
{
    public function run()
    {
        try {
            DB::beginTransaction();

            // Insert Quiz
            $quizId = DB::table('quizzes')->insertGetId([
                'topic_id' => 5,
                'course_id' => 3,
                'title' => 'Present Simple (assignment)',
                'description' => 'Grammar quiz on Present Simple tense',
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
                    'question' => 'She ______ to work every day.',
                    'options' => [
                        ['text' => 'Walk', 'is_correct' => 0],
                        ['text' => 'Walks', 'is_correct' => 1],
                        ['text' => 'Walking', 'is_correct' => 0],
                        ['text' => 'Walked', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'They ______ football on Sundays.',
                    'options' => [
                        ['text' => 'play', 'is_correct' => 1],
                        ['text' => 'plays', 'is_correct' => 0],
                        ['text' => 'playing', 'is_correct' => 0],
                        ['text' => 'played', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'The sun ______ in the east.',
                    'options' => [
                        ['text' => 'Rise', 'is_correct' => 0],
                        ['text' => 'Rises', 'is_correct' => 1],
                        ['text' => 'Rising', 'is_correct' => 0],
                        ['text' => 'Rised', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'My brother ______ in a bank.',
                    'options' => [
                        ['text' => 'Work', 'is_correct' => 0],
                        ['text' => 'Works', 'is_correct' => 1],
                        ['text' => 'working', 'is_correct' => 0],
                        ['text' => 'worked', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'We ______ to the gym every morning.',
                    'options' => [
                        ['text' => 'Goes', 'is_correct' => 0],
                        ['text' => 'Go', 'is_correct' => 1],
                        ['text' => 'Going', 'is_correct' => 0],
                        ['text' => 'Gone', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'The train ______ at 8:00 PM every day.',
                    'options' => [
                        ['text' => 'Leave', 'is_correct' => 0],
                        ['text' => 'Leaves', 'is_correct' => 1],
                        ['text' => 'Leaving', 'is_correct' => 0],
                        ['text' => 'Leaved', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'She always ______ her homework after school.',
                    'options' => [
                        ['text' => 'Do', 'is_correct' => 0],
                        ['text' => 'Does', 'is_correct' => 1],
                        ['text' => 'Doing', 'is_correct' => 0],
                        ['text' => 'Did', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'She goes to school by bus every day, but today she is walking.',
                    'options' => [
                        ['text' => 'She goes to school by bus every day, but today she is walking.', 'is_correct' => 1],
                        ['text' => 'She go to school by bus every day, but today she is walking.', 'is_correct' => 0],
                        ['text' => 'She is going to school by bus every day, but today she is walking.', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'He don\'t plays video games after dinner.',
                    'options' => [
                        ['text' => 'He doesn\'t play video games after dinner.', 'is_correct' => 1],
                        ['text' => 'He don\'t plays video games after dinner.', 'is_correct' => 0],
                        ['text' => 'He doesn\'t plays video games after dinner.', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'Does he plays tennis on weekends?',
                    'options' => [
                        ['text' => 'Does he play tennis on weekends?', 'is_correct' => 1],
                        ['text' => 'Does he plays tennis on weekends?', 'is_correct' => 0],
                        ['text' => 'Does he played tennis on weekends?', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'They doesn\'t live in this city anymore.',
                    'options' => [
                        ['text' => 'They don\'t live in this city anymore.', 'is_correct' => 1],
                        ['text' => 'They doesn\'t live in this city anymore.', 'is_correct' => 0],
                        ['text' => 'They don\'t lives in this city anymore.', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'He reads the newspaper every morning.',
                    'description' => 'Negative Sentences: Change the following sentences to the negative form using "do not" or "does not."',
                    'options' => [
                        ['text' => 'He does not reads the newspaper every morning.', 'is_correct' => 0],
                        ['text' => 'He does not read the newspaper every morning.', 'is_correct' => 1],
                        ['text' => 'He do not read the newspaper every morning.', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'They travel to Europe every summer.',
                    'options' => [
                        ['text' => 'They does not travel to Europe every summer.', 'is_correct' => 0],
                        ['text' => 'They do not travels to Europe every summer.', 'is_correct' => 0],
                        ['text' => 'They do not travel to Europe every summer.', 'is_correct' => 1],
                    ]
                ],
                [
                    'question' => 'You watch TV in the evening.',
                    'description' => 'Turn the following sentences into questions using "do" or "does."',
                    'options' => [
                        ['text' => 'Do you watch TV in the evening?', 'is_correct' => 1],
                        ['text' => 'Does you watch TV in the evening?', 'is_correct' => 0],
                        ['text' => 'Do you watches TV in the evening?', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'She enjoys cooking.',
                    'options' => [
                        ['text' => 'Do she enjoys cooking?', 'is_correct' => 0],
                        ['text' => 'Does she enjoy cooking?', 'is_correct' => 1],
                        ['text' => 'Does she enjoys cooking?', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'They play football every weekend.',
                    'options' => [
                        ['text' => 'Do they play football every weekend?', 'is_correct' => 1],
                        ['text' => 'Does they play football every weekend?', 'is_correct' => 0],
                        ['text' => 'Do they plays football every weekend?', 'is_correct' => 0],
                    ]
                ],
            ];

            foreach ($questions as $index => $questionData) {
                // Insert question
                $questionId = DB::table('questions')->insertGetId([
                    'quiz_id' => $quizId,
                    'question_type_id' => 2, // MCQ Single Answer
                    'question' => $questionData['question'],
                    'description' => $questionData['description'] ?? null,
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
