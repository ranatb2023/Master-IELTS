<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IELTSReadingMockTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            DB::beginTransaction();

            $quizId = DB::table('quizzes')->insertGetId([
                'topic_id' => 12,
                'course_id' => 6,
                'title' => 'IELTS Reading Mock Test',
                'description' => 'Complete IELTS Reading practice test with 20 questions',
                'instructions' => 'Read the passages carefully and answer all questions.',
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
                ['type' => 2, 'question' => 'What is the main topic of the passage?', 'options' => [['text' => 'Climate change effects', 'is_correct' => 1], ['text' => 'Renewable energy', 'is_correct' => 0], ['text' => 'Industrial revolution', 'is_correct' => 0], ['text' => 'Urban development', 'is_correct' => 0]]],
                ['type' => 2, 'question' => 'According to the text, when did the research begin?', 'options' => [['text' => '2010', 'is_correct' => 0], ['text' => '2015', 'is_correct' => 1], ['text' => '2018', 'is_correct' => 0], ['text' => '2020', 'is_correct' => 0]]],
                ['type' => 2, 'question' => 'The author suggests that technology will...', 'options' => [['text' => 'replace human workers', 'is_correct' => 0], ['text' => 'enhance productivity', 'is_correct' => 1], ['text' => 'remain unchanged', 'is_correct' => 0], ['text' => 'become obsolete', 'is_correct' => 0]]],
                ['type' => 2, 'question' => 'Which statement best describes the conclusion?', 'options' => [['text' => 'More research is needed', 'is_correct' => 1], ['text' => 'The problem is solved', 'is_correct' => 0], ['text' => 'Nothing can be done', 'is_correct' => 0], ['text' => 'Results are inconclusive', 'is_correct' => 0]]],
                ['type' => 2, 'question' => 'The word "sustainable" in paragraph 2 most likely means...', 'options' => [['text' => 'expensive', 'is_correct' => 0], ['text' => 'long-lasting', 'is_correct' => 1], ['text' => 'temporary', 'is_correct' => 0], ['text' => 'modern', 'is_correct' => 0]]],
                ['type' => 2, 'question' => 'What does the author imply about education?', 'options' => [['text' => 'It is changing rapidly', 'is_correct' => 1], ['text' => 'It remains traditional', 'is_correct' => 0], ['text' => 'It is becoming cheaper', 'is_correct' => 0], ['text' => 'It is less important', 'is_correct' => 0]]],
                ['type' => 2, 'question' => 'The passage primarily focuses on...', 'options' => [['text' => 'historical events', 'is_correct' => 0], ['text' => 'current trends', 'is_correct' => 1], ['text' => 'future predictions', 'is_correct' => 0], ['text' => 'scientific theories', 'is_correct' => 0]]],
                ['type' => 1, 'question' => 'The study was conducted over a five-year period.', 'options' => [['text' => 'True', 'is_correct' => 1], ['text' => 'False', 'is_correct' => 0]]],
                ['type' => 1, 'question' => 'Researchers found no significant correlation between the variables.', 'options' => [['text' => 'True', 'is_correct' => 0], ['text' => 'False', 'is_correct' => 1]]],
                ['type' => 1, 'question' => 'The new policy was implemented in all countries simultaneously.', 'options' => [['text' => 'True', 'is_correct' => 0], ['text' => 'False', 'is_correct' => 1]]],
                ['type' => 1, 'question' => ' Global temperatures have risen by 1.5 degrees Celsius.', 'options' => [['text' => 'True', 'is_correct' => 1], ['text' => 'False', 'is_correct' => 0]]],
                ['type' => 1, 'question' => 'The author supports theuse of fossil fuels.', 'options' => [['text' => 'True', 'is_correct' => 0], ['text' => 'False', 'is_correct' => 1]]],
                ['type' => 1, 'question' => 'Online learning platforms are mentioned asbeneficial.', 'options' => [['text' => 'True', 'is_correct' => 1], ['text' => 'False', 'is_correct' => 0]]],
                ['type' => 1, 'question' => 'The experiment was repeated three times for accuracy.', 'options' => [['text' => 'True', 'is_correct' => 1], ['text' => 'False', 'is_correct' => 0]]],
                ['type' => 5, 'question' => 'The research was funded by the _____ Foundation.', 'options' => [['text' => 'National Science', 'is_correct' => 1]]],
                ['type' => 5, 'question' => 'Carbon emissions must be reduced by _____ percent by 2030.', 'options' => [['text' => '45', 'is_correct' => 1]]],
                ['type' => 5, 'question' => 'The most affected region is _____ Asia.', 'options' => [['text' => 'Southeast', 'is_correct' => 1]]],
                ['type' => 5, 'question' => 'Renewable energy sources include solar, wind, and _____.', 'options' => [['text' => 'hydroelectric', 'is_correct' => 1]]],
                ['type' => 5, 'question' => 'The survey included _____ thousand participants.', 'options' => [['text' => '10', 'is_correct' => 1]]],
                ['type' => 5, 'question' => 'Dr. _____ led the international research team.', 'options' => [['text' => 'Martinez', 'is_correct' => 1]]],
            ];

            foreach ($questions as $index => $q) {
                $qId = DB::table('questions')->insertGetId(['quiz_id' => $quizId, 'question_type_id' => $q['type'], 'question' => $q['question'], 'description' => null, 'points' => 1.00, 'order' => $index + 1, 'media_type' => 'none', 'media_url' => null, 'explanation' => null, 'difficulty' => 'medium', 'created_at' => now(), 'updated_at' => now()]);
                foreach ($q['options'] as $i => $opt) {
                    DB::table('question_options')->insert(['question_id' => $qId, 'option_text' => $opt['text'], 'is_correct' => $opt['is_correct'], 'order' => $i + 1, 'explanation' => null, 'created_at' => now(), 'updated_at' => now()]);
                }
            }

            DB::commit();
            $this->command->info("✅ IELTS Reading Mock Test created - Quiz ID: $quizId - 20 questions");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("❌ Error: " . $e->getMessage());
        }
    }
}
