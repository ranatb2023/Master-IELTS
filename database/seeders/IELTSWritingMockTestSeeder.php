<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IELTSWritingMockTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();

            $quizId = DB::table('quizzes')->insertGetId([
                'topic_id' => 14,
                'course_id' => 6,
                'title' => 'IELTS Writing Mock Test',
                'description' => 'Complete IELTS Writing practice test with 20 questions',
                'instructions' => 'Select the most appropriate grammar, vocabulary, and writing strategies.',
                'time_limit' => 60,
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
                ['type' => 2, 'question' => 'Which sentence is grammatically correct?', 'options' => [['text' => 'The data shows that...', 'is_correct' => 0], ['text' => 'The data show that...', 'is_correct' => 1], ['text' => 'The datas show that...', 'is_correct' => 0], ['text' => 'The data showing that...', 'is_correct' => 0]]],
                ['type' => 2, 'question' => 'Which linking word shows contrast?', 'options' => [['text' => 'Furthermore', 'is_correct' => 0], ['text' => 'However', 'is_correct' => 1], ['text' => 'Therefore', 'is_correct' => 0], ['text' => 'Moreover', 'is_correct' => 0]]],
                ['type' => 2, 'question' => 'To introduce an example in formal writing, use...', 'options' => [['text' => 'Like', 'is_correct' => 0], ['text' => 'For instance', 'is_correct' => 1], ['text' => 'Such as stuff', 'is_correct' => 0], ['text' => 'Kind of', 'is_correct' => 0]]],
                ['type' => 2, 'question' => 'Which is the best paraphrase of "many people think"?', 'options' => [['text' => 'A lot of people think', 'is_correct' => 0], ['text' => 'It is widely believed that', 'is_correct' => 1], ['text' => 'Everybody thinks', 'is_correct' => 0], ['text' => 'People think a lot', 'is_correct' => 0]]],
                ['type' => 2, 'question' => 'In Task 1, you should describe trends using...', 'options' => [['text' => 'Personal opinions', 'is_correct' => 0], ['text' => 'Factual descriptions', 'is_correct' => 1], ['text' => 'Predictions', 'is_correct' => 0], ['text' => 'Emotional language', 'is_correct' => 0]]],
                ['type' => 2, 'question' => 'Which phrase introduces a conclusion?', 'options' => [['text' => 'Firstly', 'is_correct' => 0], ['text' => 'In conclusion', 'is_correct' => 1], ['text' => 'Additionally', 'is_correct' => 0], ['text' => 'For example', 'is_correct' => 0]]],
                ['type' => 2, 'question' => 'To show a significant increase, you can say...', 'options' => [['text' => 'went up a bit', 'is_correct' => 0], ['text' => 'rose dramatically', 'is_correct' => 1], ['text' => 'changed', 'is_correct' => 0], ['text' => 'moved', 'is_correct' => 0]]],
                ['type' => 2, 'question' => 'Which is formal vocabulary?', 'options' => [['text' => 'Kids', 'is_correct' => 0], ['text' => 'Children', 'is_correct' => 1], ['text' => 'Youngsters', 'is_correct' => 0], ['text' => 'Little ones', 'is_correct' => 0]]],
                ['type' => 1, 'question' => 'Task 2 essays should be at least 250 words.', 'options' => [['text' => 'True', 'is_correct' => 1], ['text' => 'False', 'is_correct' => 0]]],
                ['type' => 1, 'question' => 'You should use contractions in IELTS Writing.', 'options' => [['text' => 'True', 'is_correct' => 0], ['text' => 'False', 'is_correct' => 1]]],
                ['type' => 1, 'question' => 'Each body paragraph should have a clear topic sentence.', 'options' => [['text' => 'True', 'is_correct' => 1], ['text' => 'False', 'is_correct' => 0]]],
                ['type' => 1, 'question' => 'Using informal language improves your writing score.', 'options' => [['text' => 'True', 'is_correct' => 0], ['text' => 'False', 'is_correct' => 1]]],
                ['type' => 1, 'question' => 'The passive voice can make writing more formal.', 'options' => [['text' => 'True', 'is_correct' => 1], ['text' => 'False', 'is_correct' => 0]]],
                ['type' => 1, 'question' => 'You must express personal opinions in Task 1.', 'options' => [['text' => 'True', 'is_correct' => 0], ['text' => 'False', 'is_correct' => 1]]],
                ['type' => 5, 'question' => 'To show cause and effect, use "_____, therefore..."', 'options' => [['text' => 'Consequently', 'is_correct' => 1]]],
                ['type' => 5, 'question' => 'The introduction should _____ the question in your own words.', 'options' => [['text' => 'paraphrase', 'is_correct' => 1]]],
                ['type' => 5, 'question' => 'Use _____ pronouns to refer back to nouns.', 'options' => [['text' => 'relative', 'is_correct' => 1]]],
                ['type' => 5, 'question' => 'The graph illustrates a _____ increase from 2010 to 2020.', 'options' => [['text' => 'significant', 'is_correct' => 1]]],
                ['type' => 5, 'question' => 'Begin body paragraphs with _____ words like firstly, secondly.', 'options' => [['text' => 'sequencing', 'is_correct' => 1]]],
                ['type' => 5, 'question' => 'The _____ should summarize your main points.', 'options' => [['text' => 'conclusion', 'is_correct' => 1]]],
            ];

            foreach ($questions as $index => $q) {
                $qId = DB::table('questions')->insertGetId(['quiz_id' => $quizId, 'question_type_id' => $q['type'], 'question' => $q['question'], 'description' => null, 'points' => 1.00, 'order' => $index + 1, 'media_type' => 'none', 'media_url' => null, 'explanation' => null, 'difficulty' => 'medium', 'created_at' => now(), 'updated_at' => now()]);
                foreach ($q['options'] as $i => $opt) {
                    DB::table('question_options')->insert(['question_id' => $qId, 'option_text' => $opt['text'], 'is_correct' => $opt['is_correct'], 'order' => $i + 1, 'explanation' => null, 'created_at' => now(), 'updated_at' => now()]);
                }
            }

            DB::commit();
            $this->command->info("✅ IELTS Writing Mock Test created - Quiz ID: $quizId - 20 questions");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("❌ Error: " . $e->getMessage());
        }
    }
}
