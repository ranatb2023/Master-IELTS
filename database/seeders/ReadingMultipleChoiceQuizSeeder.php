<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReadingMultipleChoiceQuizSeeder extends Seeder
{
    public function run()
    {
        try {
            DB::beginTransaction();

            // Insert Quiz
            $quizId = DB::table('quizzes')->insertGetId([
                'topic_id' => 6, // Introduction topic for Reading course
                'course_id' => 4, // Reading course
                'title' => 'Multiple Choice Questions (assignment)',
                'description' => 'Reading comprehension quiz with passages on various topics',
                'instructions' => 'Read each passage carefully and choose the correct answer based on the information provided.',
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

            // Passages
            $passage1 = "Passage 1: The Amazon Rainforest\nThe Amazon Rainforest is one of the most biodiverse regions on Earth, home to millions of species of plants, animals, and insects. Stretching across nine countries, it produces 20% of the world's oxygen. Despite its ecological importance, deforestation has become a growing issue, with vast areas being cleared for agriculture and logging. This destruction not only threatens wildlife but also contributes to global climate change.";

            $passage2 = "Passage 2: The Impact of Social Media\nThe rise of social media has drastically changed the way we communicate and share information. While it has connected people from around the world, it has also led to issues such as cyberbullying and the spread of misinformation. Despite these challenges, many believe that social media plays a crucial role in modern society by promoting free expression and global awareness.";

            $passage3 = "Passage 3: The Evolution of Transportation\nOver the last century, transportation has evolved rapidly, from the invention of the automobile to the rise of electric vehicles. Today, cities around the world are looking for sustainable transportation solutions to reduce carbon emissions. Electric buses, bicycles, and even scooters are becoming more popular as governments try to combat pollution and traffic congestion.";

            $passage4 = "Passage 4: Renewable Energy Sources\nRenewable energy sources such as solar, wind, and hydroelectric power are considered essential for the future of energy production. These methods not only reduce reliance on fossil fuels but also help combat climate change. Solar energy, for example, is rapidly becoming more affordable and efficient, making it a popular choice for homeowners and businesses. Wind energy, often used in coastal areas, is another growing industry, though it can be less predictable than solar power.";

            // Questions and their options
            $questions = [
                [
                    'question' => 'What percentage of the world\'s oxygen is produced by the Amazon Rainforest?',
                    'description' => $passage1,
                    'options' => [
                        ['text' => '10%', 'is_correct' => 0],
                        ['text' => '15%', 'is_correct' => 0],
                        ['text' => '20%', 'is_correct' => 1],
                        ['text' => '25%', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'What is the main cause of deforestation in the Amazon Rainforest?',
                    'description' => $passage1,
                    'options' => [
                        ['text' => 'Urban development', 'is_correct' => 0],
                        ['text' => 'Agriculture and logging', 'is_correct' => 1],
                        ['text' => 'Mining activities', 'is_correct' => 0],
                        ['text' => 'Wildfires', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'What is a significant global effect of deforestation in the Amazon?',
                    'description' => $passage1,
                    'options' => [
                        ['text' => 'Air pollution', 'is_correct' => 0],
                        ['text' => 'Climate change', 'is_correct' => 1],
                        ['text' => 'Water scarcity', 'is_correct' => 0],
                        ['text' => 'Species extinction', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'What is the author\'s opinion of social media?',
                    'description' => $passage2,
                    'options' => [
                        ['text' => 'It is harmful and should be restricted.', 'is_correct' => 0],
                        ['text' => 'It connects people but has significant drawbacks.', 'is_correct' => 1],
                        ['text' => 'It is more harmful than beneficial.', 'is_correct' => 0],
                        ['text' => 'It is necessary for businesses but irrelevant for individuals.', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'What does the author suggest is a benefit of social media?',
                    'description' => $passage2,
                    'options' => [
                        ['text' => 'It reduces communication costs.', 'is_correct' => 0],
                        ['text' => 'It promotes freedom of expression.', 'is_correct' => 1],
                        ['text' => 'It improves technological development.', 'is_correct' => 0],
                        ['text' => 'It prevents misinformation.', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'What has been the focus of recent transportation developments?',
                    'description' => $passage3,
                    'options' => [
                        ['text' => 'Creating faster vehicles', 'is_correct' => 0],
                        ['text' => 'Reducing costs of public transportation', 'is_correct' => 0],
                        ['text' => 'Minimising environmental impact', 'is_correct' => 1],
                        ['text' => 'Improving comfort for passengers', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'Why are electric vehicles gaining popularity?',
                    'description' => $passage3,
                    'options' => [
                        ['text' => 'They are cheaper to produce.', 'is_correct' => 0],
                        ['text' => 'They help reduce pollution and traffic.', 'is_correct' => 1],
                        ['text' => 'They offer faster travel times.', 'is_correct' => 0],
                        ['text' => 'They are more luxurious than traditional vehicles.', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'Which energy source is becoming more cost-effective and efficient?',
                    'description' => $passage4,
                    'options' => [
                        ['text' => 'Wind energy', 'is_correct' => 0],
                        ['text' => 'Solar energy', 'is_correct' => 1],
                        ['text' => 'Hydroelectric power', 'is_correct' => 0],
                        ['text' => 'Fossil fuels', 'is_correct' => 0],
                    ]
                ],
                [
                    'question' => 'Where is wind energy commonly used?',
                    'description' => $passage4,
                    'options' => [
                        ['text' => 'Mountain regions', 'is_correct' => 0],
                        ['text' => 'Desert areas', 'is_correct' => 0],
                        ['text' => 'Coastal regions', 'is_correct' => 1],
                        ['text' => 'Urban areas', 'is_correct' => 0],
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
