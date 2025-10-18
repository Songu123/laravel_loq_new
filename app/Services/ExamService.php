<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExamService
{
    /**
     * Create a new exam with questions and answers
     */
    public function createExam(array $data): Exam
    {
        return DB::transaction(function () use ($data) {
            // Extract questions data
            $questionsData = $data['questions'] ?? [];
            unset($data['questions']);

            // Create exam
            $exam = Exam::create([
                'title' => $data['title'],
                'slug' => Str::slug($data['title']),
                'description' => $data['description'] ?? null,
                'category_id' => $data['category_id'],
                'created_by' => auth()->id(),
                'duration_minutes' => $data['duration_minutes'] ?? 60,
                'difficulty_level' => $data['difficulty_level'] ?? 'medium',
                'is_active' => $data['is_active'] ?? true,
                'is_public' => $data['is_public'] ?? false,
                'start_time' => $data['start_time'] ?? null,
                'end_time' => $data['end_time'] ?? null,
                'settings' => json_encode([
                    'randomize_questions' => $data['randomize_questions'] ?? false,
                    'show_results' => $data['show_results'] ?? true,
                ]),
            ]);

            // Create questions and answers
            $this->createQuestionsAndAnswers($exam, $questionsData);

            // Update totals
            $this->updateExamTotals($exam);

            return $exam->fresh(['questions.answers']);
        });
    }

    /**
     * Update existing exam
     */
    public function updateExam(Exam $exam, array $data): Exam
    {
        return DB::transaction(function () use ($exam, $data) {
            // Extract questions data
            $questionsData = $data['questions'] ?? [];
            unset($data['questions']);

            // Update exam
            $exam->update([
                'title' => $data['title'],
                'slug' => Str::slug($data['title']),
                'description' => $data['description'] ?? null,
                'category_id' => $data['category_id'],
                'duration_minutes' => $data['duration_minutes'] ?? 60,
                'difficulty_level' => $data['difficulty_level'] ?? 'medium',
                'is_active' => $data['is_active'] ?? true,
                'is_public' => $data['is_public'] ?? false,
                'start_time' => $data['start_time'] ?? null,
                'end_time' => $data['end_time'] ?? null,
                'settings' => json_encode([
                    'randomize_questions' => $data['randomize_questions'] ?? false,
                    'show_results' => $data['show_results'] ?? true,
                ]),
            ]);

            // Delete old questions and answers
            $exam->questions()->delete();

            // Create new questions and answers
            $this->createQuestionsAndAnswers($exam, $questionsData);

            // Update totals
            $this->updateExamTotals($exam);

            return $exam->fresh(['questions.answers']);
        });
    }

    /**
     * Create questions and answers for an exam
     */
    protected function createQuestionsAndAnswers(Exam $exam, array $questionsData): void
    {
        foreach ($questionsData as $index => $questionData) {
            $question = Question::create([
                'exam_id' => $exam->id,
                'question_text' => $questionData['question_text'],
                'question_type' => $questionData['question_type'],
                'marks' => $questionData['marks'] ?? 1,
                'is_required' => $questionData['is_required'] ?? true,
                'explanation' => $questionData['explanation'] ?? null,
                'order' => $index,
            ]);

            // Create answers for multiple choice and true/false questions
            if (in_array($questionData['question_type'], ['multiple_choice', 'true_false'])) {
                $answers = $questionData['answers'] ?? [];
                $correctAnswerIndex = $questionData['correct_answer'] ?? 0;

                foreach ($answers as $answerIndex => $answerData) {
                    if (!empty($answerData['answer_text'])) {
                        Answer::create([
                            'question_id' => $question->id,
                            'answer_text' => $answerData['answer_text'],
                            'is_correct' => $answerIndex == $correctAnswerIndex,
                            'order' => $answerIndex,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Update exam totals (questions count and marks)
     */
    protected function updateExamTotals(Exam $exam): void
    {
        $totalQuestions = $exam->questions()->count();
        $totalMarks = $exam->questions()->sum('marks');

        $exam->update([
            'total_questions' => $totalQuestions,
            'total_marks' => $totalMarks,
        ]);
    }

    /**
     * Duplicate an exam
     */
    public function duplicateExam(Exam $exam): Exam
    {
        return DB::transaction(function () use ($exam) {
            // Create new exam
            $newExam = $exam->replicate();
            $newExam->title = $exam->title . ' (Copy)';
            $newExam->slug = Str::slug($newExam->title) . '-' . time();
            $newExam->is_active = false;
            $newExam->created_by = auth()->id();
            $newExam->save();

            // Copy questions
            foreach ($exam->questions as $question) {
                $newQuestion = $question->replicate();
                $newQuestion->exam_id = $newExam->id;
                $newQuestion->save();

                // Copy answers
                foreach ($question->answers as $answer) {
                    $newAnswer = $answer->replicate();
                    $newAnswer->question_id = $newQuestion->id;
                    $newAnswer->save();
                }
            }

            return $newExam->fresh(['questions.answers']);
        });
    }

    /**
     * Delete exam with all related data
     */
    public function deleteExam(Exam $exam): bool
    {
        return DB::transaction(function () use ($exam) {
            // Delete all answers
            foreach ($exam->questions as $question) {
                $question->answers()->delete();
            }

            // Delete all questions
            $exam->questions()->delete();

            // Delete exam
            return $exam->delete();
        });
    }

    /**
     * Toggle exam status
     */
    public function toggleStatus(Exam $exam): bool
    {
        return $exam->update(['is_active' => !$exam->is_active]);
    }

    /**
     * Get exam statistics
     */
    public function getExamStats(Exam $exam): array
    {
        $questionTypes = $exam->questions()
            ->select('question_type', DB::raw('count(*) as count'))
            ->groupBy('question_type')
            ->pluck('count', 'question_type')
            ->toArray();

        return [
            'total_questions' => $exam->total_questions,
            'total_marks' => $exam->total_marks,
            'question_types' => $questionTypes,
            'average_marks_per_question' => $exam->total_questions > 0 
                ? round($exam->total_marks / $exam->total_questions, 2) 
                : 0,
        ];
    }
}