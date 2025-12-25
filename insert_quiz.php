&lt;?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
DB::beginTransaction();

// Insert Quiz
$quizId = DB::table('quizzes')->insertGetId([
'topic_id' => 4,
'course_id' => 3,
'title' => '3- Formal and Informal Video Quiz',
'description' => 'Quiz on formal and informal writing differences',
'instructions' => 'Please answer all questions to test your understanding of formal and informal writing.',
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

echo "Quiz created with ID: $quizId\n";

// Questions and their options
$questions = [
[
'question' => 'What is a key difference between formal and informal writing?',
'options' => [
['text' => 'Formal writing uses slang and abbreviations', 'is_correct' => 0],
['text' => 'Informal writing avoids contractions', 'is_correct' => 0],
['text' => 'Formal writing is personal and emotional', 'is_correct' => 0],
['text' => 'Informal writing is more relaxed and conversational', 'is_correct' => 1],
]
],
[
'question' => 'Which of the following is appropriate in formal writing?',
'options' => [
['text' => '"Hey, what\'s up?"', 'is_correct' => 0],
['text' => '"Thanks a ton for the invite!"', 'is_correct' => 0],
['text' => '"I look forward to hearing from you."', 'is_correct' => 1],
['text' => '"Catch you later!"', 'is_correct' => 0],
]
],
[
'question' => 'How is vocabulary typically different in formal writing compared to informal writing?',
'options' => [
['text' => 'It uses casual expressions and contractions', 'is_correct' => 0],
['text' => 'It includes more slang and colloquialisms', 'is_correct' => 0],
['text' => 'It is precise, avoids slang, and uses longer words', 'is_correct' => 1],
['text' => 'It skips technical terms', 'is_correct' => 0],
]
],
[
'question' => 'Which pronoun usage is most common in informal writing?',
'options' => [
['text' => 'Third person (he, she, they)', 'is_correct' => 0],
['text' => 'First person (I, we) and second person (you)', 'is_correct' => 1],
['text' => 'No pronouns are used', 'is_correct' => 0],
['text' => 'Only passive voice is used', 'is_correct' => 0],
]
],
[
'question' => 'Which of the following is true about sentence structure in formal writing?',
'options' => [
['text' => 'Broken or incomplete sentences are acceptable', 'is_correct' => 0],
['text' => 'Short and choppy sentences are preferred', 'is_correct' => 0],
['text' => 'Grammar and punctuation are optional', 'is_correct' => 0],
['text' => 'Sentences are complete, structured, and grammatically correct', 'is_correct' => 1],
]
],
[
'question' => 'What is the tone of formal writing?',
'options' => [
['text' => 'Humorous and friendly', 'is_correct' => 0],
['text' => 'Professional and impersonal', 'is_correct' => 1],
['text' => 'Sarcastic and casual', 'is_correct' => 0],
['text' => 'Emotional and expressive', 'is_correct' => 0],
]
],
[
'question' => 'Which scenario would typically require informal writing?',
'options' => [
['text' => 'Writing a complaint to a company', 'is_correct' => 0],
['text' => 'Applying for a job', 'is_correct' => 0],
['text' => 'Writing to your friend about a trip', 'is_correct' => 1],
['text' => 'Making a formal request to an institution', 'is_correct' => 0],
]
],
[
'question' => 'What is typically included in formal letters but not in informal ones?',
'options' => [
['text' => 'Slang and emojis', 'is_correct' => 0],
['text' => 'A friendly closing like "Cheers"', 'is_correct' => 0],
['text' => 'Titles and full names (e.g., Mr. Smith)', 'is_correct' => 1],
['text' => 'Short, broken sentences', 'is_correct' => 0],
]
],
[
'question' => 'How is organisation different in formal writing?',
'options' => [
['text' => 'It avoids paragraphs for a more casual flow', 'is_correct' => 0],
['text' => 'It may include headings and a structured format', 'is_correct' => 1],
['text' => 'It jumps between ideas freely', 'is_correct' => 0],
['text' => 'It doesn\'t need an introduction or conclusion', 'is_correct' => 0],
]
],
[
'question' => 'What is the main purpose of informal letters?',
'options' => [
['text' => 'To file complaints to organizations', 'is_correct' => 0],
['text' => 'To communicate in academic or professional settings', 'is_correct' => 0],
['text' => 'To express personal thoughts or keep in touch with friends', 'is_correct' => 1],
['text' => 'To apply for official positions', 'is_correct' => 0],
]
],
];

foreach ($questions as $index => $questionData) {
// Insert question
$questionId = DB::table('questions')->insertGetId([
'quiz_id' => $quizId,
'question_type_id' => 2, // MCQ Single Answer
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

echo "Question " . ($index + 1) . " created with ID: $questionId\n";

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
echo "\n✅ Successfully inserted quiz with all questions and options!\n";
echo "Quiz ID: $quizId\n";
echo "Total Questions: " . count($questions) . "\n";

} catch (\Exception $e) {
DB::rollBack();
echo "❌ Error: " . $e->getMessage() . "\n";
echo $e->getTraceAsString() . "\n";
}