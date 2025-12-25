<?php

namespace Database\Seeders;

use App\Models\QuestionType;
use Illuminate\Database\Seeder;

class QuestionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questionTypes = [
            [
                'slug' => 'true_false',
                'name' => 'True/False',
                'description' => 'A question with only two options: True or False',
                'scoring_strategy' => 'auto_exact',
                'is_active' => true,
                'input_schema' => [
                    'correct_answer' => [
                        'type' => 'radio',
                        'label' => 'Correct Answer',
                        'options' => ['true' => 'True', 'false' => 'False'],
                        'required' => true,
                    ],
                ],
                'output_schema' => [
                    'type' => 'string',
                    'enum' => ['true', 'false'],
                    'description' => 'Student answer stored as "true" or "false" string',
                ],
            ],
            [
                'slug' => 'mcq_single',
                'name' => 'Multiple Choice (Single Answer)',
                'description' => 'Multiple choice question where only one option is correct',
                'scoring_strategy' => 'auto_exact',
                'is_active' => true,
                'input_schema' => [
                    'options' => [
                        'type' => 'repeater',
                        'label' => 'Answer Options',
                        'min' => 2,
                        'max' => 10,
                        'fields' => [
                            'text' => [
                                'type' => 'text',
                                'label' => 'Option Text',
                                'required' => true,
                            ],
                            'is_correct' => [
                                'type' => 'checkbox',
                                'label' => 'Is Correct',
                            ],
                        ],
                        'validation' => 'exactly_one_correct',
                    ],
                ],
                'output_schema' => [
                    'type' => 'integer',
                    'description' => 'ID of the selected option from question_options table',
                ],
            ],
            [
                'slug' => 'mcq_multiple',
                'name' => 'Multiple Choice (Multiple Answers)',
                'description' => 'Multiple choice question where multiple options can be correct',
                'scoring_strategy' => 'auto_partial',
                'is_active' => true,
                'input_schema' => [
                    'options' => [
                        'type' => 'repeater',
                        'label' => 'Answer Options',
                        'min' => 2,
                        'max' => 10,
                        'fields' => [
                            'text' => [
                                'type' => 'text',
                                'label' => 'Option Text',
                                'required' => true,
                            ],
                            'is_correct' => [
                                'type' => 'checkbox',
                                'label' => 'Is Correct',
                            ],
                        ],
                        'validation' => 'at_least_one_correct',
                    ],
                    'partial_credit' => [
                        'type' => 'checkbox',
                        'label' => 'Allow Partial Credit',
                        'default' => true,
                        'help' => 'Award points proportionally for partially correct answers',
                    ],
                ],
                'output_schema' => [
                    'type' => 'array',
                    'items' => ['type' => 'integer'],
                    'description' => 'Array of selected option IDs from question_options table',
                ],
            ],
            [
                'slug' => 'essay',
                'name' => 'Open Ended / Essay',
                'description' => 'Long-form written response requiring manual grading',
                'scoring_strategy' => 'manual',
                'is_active' => true,
                'input_schema' => [
                    'min_words' => [
                        'type' => 'number',
                        'label' => 'Minimum Words',
                        'min' => 0,
                        'default' => 0,
                    ],
                    'max_words' => [
                        'type' => 'number',
                        'label' => 'Maximum Words',
                        'min' => 0,
                        'default' => 0,
                        'help' => '0 means no limit',
                    ],
                    'allow_formatting' => [
                        'type' => 'checkbox',
                        'label' => 'Allow Rich Text Formatting',
                        'default' => false,
                    ],
                    'sample_answer' => [
                        'type' => 'textarea',
                        'label' => 'Sample Answer (for grader reference)',
                        'required' => false,
                    ],
                ],
                'output_schema' => [
                    'type' => 'string',
                    'description' => 'Student essay text or HTML if formatting allowed',
                ],
            ],
            [
                'slug' => 'fill_blanks',
                'name' => 'Fill in the Blanks',
                'description' => 'Question with blank spaces to be filled in',
                'scoring_strategy' => 'auto_exact',
                'is_active' => true,
                'input_schema' => [
                    'blanks' => [
                        'type' => 'repeater',
                        'label' => 'Blank Fields',
                        'min' => 1,
                        'max' => 20,
                        'fields' => [
                            'placeholder' => [
                                'type' => 'text',
                                'label' => 'Placeholder Text',
                                'help' => 'Text shown in the blank (e.g., "blank 1")',
                            ],
                            'correct_answers' => [
                                'type' => 'tags',
                                'label' => 'Accepted Answers',
                                'help' => 'Multiple acceptable answers (case-insensitive comparison)',
                                'required' => true,
                            ],
                            'case_sensitive' => [
                                'type' => 'checkbox',
                                'label' => 'Case Sensitive',
                                'default' => false,
                            ],
                        ],
                    ],
                    'question_template' => [
                        'type' => 'textarea',
                        'label' => 'Question Template',
                        'help' => 'Use {{blank_1}}, {{blank_2}}, etc. to mark blank positions',
                        'required' => true,
                    ],
                ],
                'output_schema' => [
                    'type' => 'array',
                    'items' => ['type' => 'string'],
                    'description' => 'Array of student answers for each blank in order',
                ],
            ],
            [
                'slug' => 'short_answer',
                'name' => 'Short Answer',
                'description' => 'Brief text response with keyword matching or manual grading',
                'scoring_strategy' => 'auto_exact',
                'is_active' => true,
                'input_schema' => [
                    'accepted_answers' => [
                        'type' => 'tags',
                        'label' => 'Accepted Answers',
                        'help' => 'Keywords or phrases that count as correct',
                        'required' => true,
                    ],
                    'case_sensitive' => [
                        'type' => 'checkbox',
                        'label' => 'Case Sensitive',
                        'default' => false,
                    ],
                    'exact_match' => [
                        'type' => 'checkbox',
                        'label' => 'Require Exact Match',
                        'default' => true,
                        'help' => 'If unchecked, will check if answer contains any accepted keyword',
                    ],
                    'max_length' => [
                        'type' => 'number',
                        'label' => 'Maximum Characters',
                        'default' => 500,
                    ],
                    'fallback_to_manual' => [
                        'type' => 'checkbox',
                        'label' => 'Manual Review if No Match',
                        'default' => false,
                        'help' => 'Flag for manual grading if automatic match fails',
                    ],
                ],
                'output_schema' => [
                    'type' => 'string',
                    'description' => 'Short text answer from student',
                ],
            ],
            [
                'slug' => 'matching',
                'name' => 'Matching',
                'description' => 'Match items from one list to items in another list',
                'scoring_strategy' => 'auto_partial',
                'is_active' => true,
                'input_schema' => [
                    'left_items' => [
                        'type' => 'repeater',
                        'label' => 'Left Column Items',
                        'min' => 2,
                        'max' => 15,
                        'fields' => [
                            'id' => [
                                'type' => 'auto',
                                'label' => 'Item ID',
                            ],
                            'text' => [
                                'type' => 'text',
                                'label' => 'Item Text',
                                'required' => true,
                            ],
                        ],
                    ],
                    'right_items' => [
                        'type' => 'repeater',
                        'label' => 'Right Column Items',
                        'min' => 2,
                        'max' => 15,
                        'fields' => [
                            'id' => [
                                'type' => 'auto',
                                'label' => 'Item ID',
                            ],
                            'text' => [
                                'type' => 'text',
                                'label' => 'Item Text',
                                'required' => true,
                            ],
                        ],
                    ],
                    'correct_pairs' => [
                        'type' => 'key_value_pairs',
                        'label' => 'Correct Matches',
                        'help' => 'Map left item IDs to right item IDs',
                        'required' => true,
                    ],
                    'allow_partial' => [
                        'type' => 'checkbox',
                        'label' => 'Allow Partial Credit',
                        'default' => true,
                    ],
                ],
                'output_schema' => [
                    'type' => 'object',
                    'description' => 'JSON object mapping left item IDs to right item IDs {"left_1": "right_3", "left_2": "right_1", ...}',
                ],
            ],
            [
                'slug' => 'image_choice',
                'name' => 'Image Selection',
                'description' => 'Question answered by selecting from image options',
                'scoring_strategy' => 'auto_exact',
                'is_active' => true,
                'input_schema' => [
                    'options' => [
                        'type' => 'repeater',
                        'label' => 'Image Options',
                        'min' => 2,
                        'max' => 8,
                        'fields' => [
                            'image' => [
                                'type' => 'image',
                                'label' => 'Image',
                                'required' => true,
                            ],
                            'caption' => [
                                'type' => 'text',
                                'label' => 'Caption (optional)',
                            ],
                            'is_correct' => [
                                'type' => 'checkbox',
                                'label' => 'Is Correct',
                            ],
                        ],
                        'validation' => 'at_least_one_correct',
                    ],
                    'multiple_select' => [
                        'type' => 'checkbox',
                        'label' => 'Allow Multiple Selection',
                        'default' => false,
                    ],
                ],
                'output_schema' => [
                    'type' => 'mixed',
                    'description' => 'Single option ID (integer) if single select, or array of option IDs if multiple select',
                ],
            ],
        ];

        foreach ($questionTypes as $typeData) {
            QuestionType::updateOrCreate(
                ['slug' => $typeData['slug']],
                $typeData
            );
        }
    }
}
