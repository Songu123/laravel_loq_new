<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get the first teacher or admin user
        $teacher = User::whereIn('role', ['teacher', 'admin'])->first();
        
        if (!$teacher) {
            // Create a default teacher if none exists
            $teacher = User::create([
                'name' => 'Teacher Demo',
                'email' => 'teacher@example.com',
                'password' => bcrypt('password'),
                'role' => 'teacher',
                'email_verified_at' => now(),
            ]);
        }

        // Get categories
        $mathCategory = Category::where('name', 'Toán học')->first();
        $englishCategory = Category::where('name', 'Tiếng Anh')->first();
        
        if (!$mathCategory || !$englishCategory) {
            $this->command->info('Please run CategorySeeder first!');
            return;
        }

        // Create sample exams
        $exams = [
            [
                'title' => 'Kiểm tra Toán học lớp 10 - Chương 1',
                'description' => 'Bài kiểm tra 45 phút về các phép toán cơ bản và hàm số',
                'category_id' => $mathCategory->id,
                'duration_minutes' => 45,
                'difficulty_level' => 'easy',
                'is_active' => true,
                'is_public' => true,
                'questions' => [
                    [
                        'question_text' => 'Giá trị của biểu thức 2x + 3 khi x = 2 là:',
                        'question_type' => 'multiple_choice',
                        'marks' => 1,
                        'is_required' => true,
                        'explanation' => 'Thay x = 2 vào biểu thức: 2(2) + 3 = 4 + 3 = 7',
                        'answers' => [
                            ['answer_text' => '5', 'is_correct' => false],
                            ['answer_text' => '7', 'is_correct' => true],
                            ['answer_text' => '9', 'is_correct' => false],
                            ['answer_text' => '11', 'is_correct' => false],
                        ]
                    ],
                    [
                        'question_text' => 'Hàm số y = 2x + 1 là hàm số bậc nhất.',
                        'question_type' => 'true_false',
                        'marks' => 1,
                        'is_required' => true,
                        'explanation' => 'Hàm số y = 2x + 1 có dạng y = ax + b với a ≠ 0, nên đây là hàm số bậc nhất',
                        'answers' => [
                            ['answer_text' => 'Đúng', 'is_correct' => true],
                            ['answer_text' => 'Sai', 'is_correct' => false],
                        ]
                    ],
                    [
                        'question_text' => 'Tìm nghiệm của phương trình x + 5 = 12',
                        'question_type' => 'short_answer',
                        'marks' => 2,
                        'is_required' => true,
                        'explanation' => 'x + 5 = 12 => x = 12 - 5 = 7',
                    ]
                ]
            ],
            [
                'title' => 'English Grammar Test - Present Tense',
                'description' => 'Test your knowledge of present simple and present continuous tenses',
                'category_id' => $englishCategory->id,
                'duration_minutes' => 30,
                'difficulty_level' => 'medium',
                'is_active' => true,
                'is_public' => true,
                'questions' => [
                    [
                        'question_text' => 'Choose the correct form: She _____ to school every day.',
                        'question_type' => 'multiple_choice',
                        'marks' => 1,
                        'is_required' => true,
                        'explanation' => 'Present simple tense is used for daily routines',
                        'answers' => [
                            ['answer_text' => 'go', 'is_correct' => false],
                            ['answer_text' => 'goes', 'is_correct' => true],
                            ['answer_text' => 'going', 'is_correct' => false],
                            ['answer_text' => 'gone', 'is_correct' => false],
                        ]
                    ],
                    [
                        'question_text' => 'Present continuous tense is used for actions happening now.',
                        'question_type' => 'true_false',
                        'marks' => 1,
                        'is_required' => true,
                        'explanation' => 'Present continuous (is/am/are + V-ing) is used for actions happening at the moment of speaking',
                        'answers' => [
                            ['answer_text' => 'Đúng', 'is_correct' => true],
                            ['answer_text' => 'Sai', 'is_correct' => false],
                        ]
                    ],
                    [
                        'question_text' => 'Write a sentence about your daily routine using present simple tense.',
                        'question_type' => 'essay',
                        'marks' => 3,
                        'is_required' => false,
                        'explanation' => 'Example: I wake up at 6 AM every morning.',
                    ]
                ]
            ]
        ];

        foreach ($exams as $examData) {
            $questions = $examData['questions'];
            unset($examData['questions']);
            
            // Create exam
            $exam = Exam::create([
                ...$examData,
                'created_by' => $teacher->id,
                'total_questions' => count($questions),
                'total_marks' => array_sum(array_column($questions, 'marks')),
            ]);

            // Create questions and answers
            foreach ($questions as $questionData) {
                $answers = $questionData['answers'] ?? [];
                unset($questionData['answers']);
                
                $question = Question::create([
                    ...$questionData,
                    'exam_id' => $exam->id,
                ]);

                // Create answers if they exist
                foreach ($answers as $answerData) {
                    Answer::create([
                        ...$answerData,
                        'question_id' => $question->id,
                    ]);
                }
            }
        }

        $this->command->info('Sample exams created successfully!');
    }
}
