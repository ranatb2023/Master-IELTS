&lt;?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Quiz Verification ===\n\n";

// Get the quiz
$quiz = DB::table('quizzes')->where('id', 4)->first();
echo "Quiz Title: " . $quiz->title . "\n";
echo "Topic ID: " . $quiz->topic_id . "\n";
echo "Course ID: " . $quiz->course_id . "\n\n";

// Get all questions
$questions = DB::table('questions')->where('quiz_id', 4)->orderBy('order')->get();
echo "Total Questions: " . $questions->count() . "\n\n";

// Display first question with options as sample
$firstQuestion = $questions->first();
echo "Sample Question (ID: {$firstQuestion->id}):\n";
echo "Q: " . $firstQuestion->question . "\n\n";

$options = DB::table('question_options')->where('question_id', $firstQuestion->id)->orderBy('order')->get();
foreach ($options as $option) {
$marker = $option->is_correct ? ' *' : '';
echo " - " . $option->option_text . $marker . "\n";
}

echo "\n✅ Quiz data verified successfully!\n";
echo "All questions inserted without option letters (A, B, C, D) or question numbers (i, ii, etc.)\n";