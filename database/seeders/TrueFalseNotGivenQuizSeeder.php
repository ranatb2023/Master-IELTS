<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrueFalseNotGivenQuizSeeder extends Seeder
{
    public function run()
    {
        try {
            DB::beginTransaction();

            // Insert Quiz
            $quizId = DB::table('quizzes')->insertGetId([
                'topic_id' => 6, // Introduction topic for Reading course
                'course_id' => 4, // Reading course
                'title' => 'True/False/Not Given (assignment)',
                'description' => 'Reading comprehension quiz focusing on True/False/Not Given questions about the history of chocolate',
                'instructions' => 'Read the passage carefully and decide if each statement is True, False, or Not Given based on the information provided.',
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

            // Passage about chocolate history
            $passage = "The History of Chocolate\n\nChocolate has a rich and fascinating history that dates back thousands of years to ancient Mesoamerican civilizations. The Mayans and Aztecs were among the first to cultivate cacao trees and create chocolate beverages. For the Aztecs, chocolate was considered a sacred food, reserved for royalty and religious ceremonies. Cocoa beans were so valuable that they were used as currency in trade.\n\nWhen Spanish conquistadors arrived in the Americas in the 16th century, they brought chocolate back to Europe. Initially, it was consumed as a bitter drink, similar to how it was prepared in Mesoamerica. However, Europeans soon began adding sugar and milk to make it sweeter and more palatable. By the 17th century, chocolate had become popular among European aristocracy, spreading from Spain to other countries including France, England, and the Netherlands.\n\nThe industrial revolution brought significant changes to chocolate production, making it more accessible to the general public. Today, chocolate is enjoyed worldwide in countless forms, from bars and candies to beverages and desserts.";

            // Questions and their options
            $questions = [
                [
                    'question' => 'The Aztecs considered chocolate a sacred food.',
                    'description' => $passage,
                    'options' => [
                        ['text' => 'True', 'is_correct' => 1],
                        ['text' => 'False', 'is_correct' => 0],
                        ['text' => 'Not Given', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'Cocoa beans were used as a form of money in ancient Mesoamerican civilizations.',
                    'description' => $passage,
                    'options' => [
                        ['text' => 'True', 'is_correct' => 1],
                        ['text' => 'False', 'is_correct' => 0],
                        ['text' => 'Not Given', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'Chocolate became popular in Europe during the 14th century.',
                    'description' => $passage,
                    'options' => [
                        ['text' => 'True', 'is_correct' => 0],
                        ['text' => 'False', 'is_correct' => 1],
                        ['text' => 'Not Given', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'The Mayans only used chocolate as a drink.',
                    'description' => $passage,
                    'options' => [
                        ['text' => 'True', 'is_correct' => 0],
                        ['text' => 'False', 'is_correct' => 0],
                        ['text' => 'Not Given', 'is_correct' => 1],
                    ]
                ],
                [
                    'question' => 'Sugar was added to chocolate after it arrived in Europe.',
                    'description' => $passage,
                    'options' => [
                        ['text' => 'True', 'is_correct' => 1],
                        ['text' => 'False', 'is_correct' => 0],
                        ['text' => 'Not Given', 'is_correct' => 0],
                    ]
                ],
            ];

            foreach ($questions as $index => $questionData) {
                // Insert question
                $questionId = DB::table('questions')->insertGetId([
                    'quiz_id' => $quizId,
                    'question_type_id' => 2, // MCQ Single Answer
                    'question' => $questionData['question'],
                    'description' => $questionData['description'],
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
