1. High-level requirements

We need a quiz system where:

A quiz has multiple questions.

A question can have multiple answers (depending on its type).

Questions support multiple question types, and question types MUST NOT be hard-coded in the codebase.

Each question type has its own input and output behaviour (how it is displayed in the UI and how answers are stored/marked).

Supported Question Types

These are the question types we need to support:

True/False

Multiple Choice

Open Ended / Essay

Fill in the Blanks

Short Answer

Matching

Image Answering (question is answered using an image or based on an image)

We want this to be extendable so that new question types can be added later just by inserting records in the DB, not changing code.

2. Database: Existing Tutor LMS tables

These tables are already created by the Tutor LMS plugin. We must work with them and extend behaviour without breaking existing logic.

2.1 wp_tutor_quiz_questions
question_id          bigint(20) PK AUTO_INCREMENT
quiz_id              bigint(20) NULL
question_title       text
question_description longtext
answer_explanation   longtext       DEFAULT ''
question_type        varchar(50)    NULL
question_mark        decimal(9,2)   NULL
question_settings    longtext       NULL
question_order       int(11)        NULL

2.2 wp_tutor_quiz_question_answers
answer_id             bigint(20) PK AUTO_INCREMENT
belongs_question_id   bigint(20) NULL
belongs_question_type varchar(250) NULL
answer_title          text
is_correct            tinyint(4)    NULL
image_id              bigint(20)    NULL
answer_two_gap_match  text
answer_view_format    varchar(250) NULL
answer_settings       text
answer_order          int(11)       DEFAULT 0

2.3 wp_tutor_quiz_attempts
attempt_id             bigint(20) PK AUTO_INCREMENT
course_id              bigint(20) NULL
quiz_id                bigint(20) NULL
user_id                bigint(20) NULL
total_questions        int(11)     NULL
total_answered_questions int(11)   NULL
total_marks            decimal(9,2) NULL
earned_marks           decimal(9,2) NULL
attempt_info           text        NULL
attempt_status         varchar(50) NULL
attempt_ip             varchar(250) NULL
attempt_started_at     datetime    NULL
attempt_ended_at       datetime    NULL
is_manually_reviewed   int(1)      NULL
manually_reviewed_at   datetime    NULL

2.4 wp_tutor_quiz_attempt_answers
attempt_answer_id  bigint(20) PK AUTO_INCREMENT
user_id            bigint(20) NULL
quiz_id            bigint(20) NULL
question_id        bigint(20) NULL
quiz_attempt_id    bigint(20) NULL
given_answer       longtext   NULL
question_mark      decimal(8,2) NULL
achieved_mark      decimal(8,2) NULL
minus_mark         decimal(8,2) NULL
is_correct         tinyint(4) NULL

3. New requirement: Question Types table (no hard-coding)

Right now Tutor LMS stores question_type as a varchar(50) directly on wp_tutor_quiz_questions. We do not want to hard-code these types in code.

Create a new table to manage question types:

3.1 New table: quiz_question_types (name can be adjusted to WP naming style)
question_type_id   bigint(20) PK AUTO_INCREMENT
slug               varchar(50) UNIQUE  -- e.g. 'true_false', 'mcq', 'short_answer'
name               varchar(100)       -- Human readable name
description        text               -- Optional description
input_schema       longtext           -- JSON describing expected input (frontend form)
output_schema      longtext           -- JSON describing how answers are stored in 'given_answer'
scoring_strategy   varchar(50)        -- e.g. 'auto_exact', 'auto_partial', 'manual'
is_active          tinyint(1) DEFAULT 1
created_at         datetime NULL
updated_at         datetime NULL


Important behaviour:

Code should use this table to determine:

How to render the question form.

How to validate and store given_answer.

How to auto-mark (if applicable).

Existing wp_tutor_quiz_questions.question_type can:

Either be kept as a string that maps to quiz_question_types.slug, OR

We can add a new column question_type_id to wp_tutor_quiz_questions and gradually migrate, if possible.

For now, assume we will map by slug (question_type -> quiz_question_types.slug).

4. Behaviour per question type

Describe expected input/output for each type, so the code can interpret question_settings, answer_settings, and given_answer.

4.1 True/False

Input (author side):

Question text.

Correct answer: true or false.

Storage:

Options stored in wp_tutor_quiz_question_answers (two records: True, False) or driven purely by type.

Correct option marked with is_correct = 1.

Student answer:

given_answer is either:

"true" / "false" or

The selected answer_id.

Marking: fully auto; is_correct set accordingly.

4.2 Multiple Choice

Input:

Question text.

List of answer options.

Single-select or multi-select flag.

For each option: title, is_correct.

Storage:

Options in wp_tutor_quiz_question_answers.

Single/multiple select flag in question_settings JSON.

Student answer:

given_answer = list of selected answer_ids (as JSON) or value depending on existing Tutor format.

Marking:

Auto:

Single-select: correct if selected_id == correct_id.

Multi-select: may be exact match or partial based on scoring_strategy.

4.3 Open Ended / Essay

Input:

Question text.

Word limit or guideline (optional).

Storage:

No predefined answers in wp_tutor_quiz_question_answers (or just one for structure).

Student answer:

given_answer = free text.

Marking:

Manual: is_manually_reviewed and manually_reviewed_at in wp_tutor_quiz_attempts used.

achieved_mark is set by reviewer.

4.4 Fill in the Blanks

Input:

Question text with placeholders or list of gaps.

Correct value for each gap.

Storage:

Correct values stored either in wp_tutor_quiz_question_answers or in question_settings JSON.

answer_two_gap_match can be used for secondary matches if needed.

Student answer:

given_answer = JSON array of gap answers in order.

Marking:

Auto:

Exact/trimmed case-insensitive match, or regex based on input_schema/scoring_strategy.

4.5 Short Answer

Input:

Question text.

One or more accepted correct short answers.

Storage:

Accepted answers stored in wp_tutor_quiz_question_answers or JSON in question_settings.

Student answer:

given_answer = short text.

Marking:

Auto: compare against accepted answers (case folding, trimming).

Optionally fallback to manual review if “uncertain”.

4.6 Matching

Input:

Two lists: left items and right items.

Correct pairs mapping.

Storage:

Each option row in wp_tutor_quiz_question_answers.

answer_two_gap_match can store the matched pair key/value.

Student answer:

given_answer = JSON representing user’s mapping (e.g. {left_id: right_id}).

Marking:

Auto: compare mapping with correct mapping and compute score (full or partial).

4.7 Image Answering

This can mean either “question is answered by selecting an image” or “question is based on an image”.

Input:

Question text.

One or more images (via image_id).

For selecting: mark which image is correct.

Storage:

Images referenced via image_id in wp_tutor_quiz_question_answers or in question itself.

Student answer:

Selected answer_id or uploaded image info stored in given_answer.

Marking:

If choosing from images → auto.

If uploading their own image/explanation → manual.