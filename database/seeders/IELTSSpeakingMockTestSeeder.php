<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IELTSSpeakingMockTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();

            $quizId = DB::table('quizzes')->insertGetId([
                'topic_id' => 13,
                'course_id' => 6,
                'title' => 'IELTS Speaking Mock Test',
                'description' => 'Complete IELTS Speaking practice test with 20 questions',
                'instructions' => 'Select the most appropriate responses and strategies.',
                'time_limit' => 15,
                'passing_score' => 60.00,
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

            $questions = [
                ['type' => 2, 'question' => 'What is the best way to introduce yourself in Part 1?', 'options' => [['text' => 'Hello, my name is...', 'is_correct' => 1], ['text' => 'Hi, I am...', 'is_correct' => 0], ['text' => 'Good morning sir/madam', 'is_correct' => 0], ['text' => 'Yes, I am ready', 'is_correct' => 0]]],
                ['type' => 2, 'question' => 'When describing a person, which detail is most important?', 'options' => [['text' => 'Their appearance', 'is_correct' => 0], ['text' => 'Their personality and qualities', 'is_correct' => 1], ['text' => 'Their age', 'is_correct' => 0], ['text' => 'Their occupation', 'is_correct' => 0]]],
                ['type' => 2, 'question' => 'How should you extend your answer in Part 1?', 'options' => [['text' => 'Give one-word answers', 'is_correct' => 0], ['text' => 'Add examples and reasons', 'is_correct' => 1], ['text' => 'Memorize scripts', 'is_correct' => 0], ['text' => 'Ask questions back', 'is_correct' => 0]]],
                ['type' => 2, 'question' => 'Which phrase is best for expressing opinions?', 'options' => [['text' => 'I think that...', 'is_correct' => 0], ['text' => 'In my opinion...', 'is_correct' => 1], ['text' => 'Maybe...', 'is_correct' => 0], ['text' => 'Everyone knows...', 'is_correct' => 0]]],
                ['type' => 2, 'question' => 'What should you do if you don\'t understand a question?', 'options' => [['text' => 'Stay silent', 'is_correct' => 0], ['text' => 'Guess the answer', 'is_correct' => 0], ['text' => 'Politely ask for clarification', 'is_correct' => 1], ['text' => 'Change the topic', 'is_correct' => 0]]],
                ['type' => 2, 'question' => 'Which time expression is appropriate for past experiences?', 'options' => [['text' => 'Last year', 'is_correct' => 1], ['text' => 'Next week', 'is_correct' => 0], ['text' => 'Usually', 'is_correct' => 0], ['text' => 'Always', 'is_correct' => 0]]],
                ['type' => 1, 'question' => 'Using varied vocabulary improves your speaking score.', 'options' => [['text' => 'True', 'is_correct' => 1], ['text' => 'False', 'is_correct' => 0]]],
                ['type' => 1, 'question' => 'You should memorize answers word-for-word.', 'options' => [['text' => 'True', 'is_correct' => 0], ['text' => 'False', 'is_correct' => 1]]],
                ['type' => 1, 'question' => 'Pausing to think is acceptable during the speaking test.', 'options' => [['text' => 'True', 'is_correct' => 1], ['text' => 'False', 'is_correct' => 0]]],
                ['type' => 1, 'question' => 'Grammar accuracy is more important than fluency.', 'options' => [['text' => 'True', 'is_correct' => 0], ['text' => 'False', 'is_correct' => 1]]],
                ['type' => 1, 'question' => 'You should maintain eye contact with the examiner.', 'options' => [['text' => 'True', 'is_correct' => 1], ['text' => 'False', 'is_correct' => 0]]],
                ['type' => 1, 'question' => 'Part 2 requires you to speak for 5 minutes.', 'options' => [['text' => 'True', 'is_correct' => 0], ['text' => 'False', 'is_correct' => 1]]],
                ['type' => 1, 'question' => 'Using linking words helps organize your speech.', 'options' => [['text' => 'True', 'is_correct' => 1], ['text' => 'False', 'is_correct' => 0]]],
                ['type' => 1, 'question' => 'You can change the topic if it is difficult.', 'options' => [['text' => 'True', 'is_correct' => 0], ['text' => 'False', 'is_correct' => 1]]],
                ['type' => 5, 'question' => 'To express preference, use phrases like "I _____ prefer..."', 'options' => [['text' => 'would', 'is_correct' => 1]]],
                ['type' => 5, 'question' => 'When describing the past, use the _____ tense.', 'options' => [['text' => 'past', 'is_correct' => 1]]],
                ['type' => 5, 'question' => 'To give examples, say "_____ instance..."', 'options' => [['text' => 'For', 'is_correct' => 1]]],
                ['type' => 5, 'question' => 'Part 2 lasts approximately _____ to 2 minutes.', 'options' => [['text' => '1', 'is_correct' => 1]]],
                ['type' => 5, 'question' => 'Use _____ sentences to sound more natural.', 'options' => [['text' => 'complex', 'is_correct' => 1]]],
                ['type' => 5, 'question' => 'To conclude your answer, say "_____ all..."', 'options' => [['text' => 'Overall', 'is_correct' => 1]]],
            ];

            foreach ($questions as $index => $q) {
                $qId = DB::table('questions')->insertGetId(['quiz_id' => $quizId, 'question_type_id' => $q['type'], 'question' => $q['question'], 'description' => null, 'points' => 1.00, 'order' => $index + 1, 'media_type' => 'none', 'media_url' => null, 'explanation' => null, 'difficulty' => 'medium', 'created_at' => now(), 'updated_at' => now()]);
                foreach ($q['options'] as $i => $opt) {
                    DB::table('question_options')->insert(['question_id' => $qId, 'option_text' => $opt['text'], 'is_correct' => $opt['is_correct'], 'order' => $i + 1, 'explanation' => null, 'created_at' => now(), 'updated_at' => now()]);
                }
            }

            DB::commit();
            $this->command->info("✅ IELTS Speaking Mock Test created - Quiz ID: $quizId - 20 questions");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("❌ Error: " . $e->getMessage());
        }
    }
}
