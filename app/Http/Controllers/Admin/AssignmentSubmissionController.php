<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssignmentSubmission;
use App\Models\AssignmentFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssignmentSubmissionController extends Controller
{
    /**
     * Display a listing of all submissions.
     */
    public function index(Request $request)
    {
        $query = AssignmentSubmission::with(['assignment.topic', 'user']);

        // Search filter (by student name or email)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Assignment filter
        if ($request->filled('assignment_id')) {
            $query->where('assignment_id', $request->input('assignment_id'));
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Late submission filter
        if ($request->filled('is_late')) {
            $query->where('is_late', $request->input('is_late') === '1');
        }

        $submissions = $query->orderBy('submitted_at', 'desc')->paginate(20);

        // Get all assignments for filter dropdown
        $assignments = \App\Models\Assignment::with('topic')->orderBy('title')->get();

        // Calculate statistics
        $stats = [
            'total' => AssignmentSubmission::count(),
            'pending' => AssignmentSubmission::where('status', 'submitted')->count(),
            'graded' => AssignmentSubmission::where('status', 'graded')->count(),
            'avg_score' => round(AssignmentSubmission::whereNotNull('score')->avg('score') ?? 0, 1),
        ];

        return view('admin.assignment-submissions.index', compact('submissions', 'assignments', 'stats'));
    }

    /**
     * Display the specified submission.
     */
    public function show(AssignmentSubmission $submission)
    {
        $submission->load([
            'assignment.topic.course',
            'user',
            'grader',
            'assignmentFiles',
            'rubricScores.rubric'
        ]);

        return view('admin.assignment-submissions.show', compact('submission'));
    }

    /**
     * Show the form for grading a submission.
     */
    public function grade(AssignmentSubmission $submission)
    {
        $submission->load([
            'assignment.topic.course',
            'assignment.rubrics',
            'user',
            'assignmentFiles',
            'rubricScores'
        ]);

        return view('admin.assignment-submissions.grade', compact('submission'));
    }

    /**
     * Submit grade for a submission.
     */
    public function submitGrade(Request $request, AssignmentSubmission $submission)
    {
        $validated = $request->validate([
            'score' => 'required|numeric|min:0|max:' . $submission->assignment->max_points,
            'feedback' => 'nullable|string',
            'status' => 'required|in:graded,returned',
            'passed' => 'nullable|boolean',
            'rubric_scores' => 'nullable|array',
            'rubric_scores.*.rubric_id' => 'required|exists:assignment_rubrics,id',
            'rubric_scores.*.points' => 'required|numeric|min:0',
            'rubric_scores.*.feedback' => 'nullable|string',
        ]);

        // Update submission
        $submission->update([
            'score' => $validated['score'],
            'feedback' => $validated['feedback'] ?? null,
            'status' => $validated['status'],
            'passed' => $validated['passed'] ?? ($validated['score'] >= $submission->assignment->passing_points),
            'graded_at' => now(),
            'graded_by' => auth()->id(),
        ]);

        // Update rubric scores if provided
        if (isset($validated['rubric_scores'])) {
            foreach ($validated['rubric_scores'] as $rubricScore) {
                $submission->rubricScores()->updateOrCreate(
                    [
                        'submission_id' => $submission->id,
                        'rubric_id' => $rubricScore['rubric_id'],
                    ],
                    [
                        'points' => $rubricScore['points'],
                        'feedback' => $rubricScore['feedback'] ?? null,
                    ]
                );
            }
        }

        return redirect()->route('admin.assignment-submissions.show', $submission)
            ->with('success', 'Submission graded successfully!');
    }

    /**
     * View/preview a specific file from a submission.
     */
    public function viewFile(AssignmentFile $file)
    {
        $path = storage_path('app/private/' . $file->file_path);

        if (!file_exists($path)) {
            abort(404, 'File not found');
        }

        return response()->file($path, [
            'Content-Type' => $file->mime_type ?? 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . ($file->original_filename ?? $file->file_name) . '"'
        ]);
    }

    /**
     * Download a specific file from a submission.
     */
    public function downloadFile(AssignmentFile $file)
    {
        if (!Storage::disk('private')->exists($file->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('private')->download($file->file_path, $file->original_filename ?? $file->file_name);
    }

    /**
     * Download all files from a submission as a zip.
     */
    public function downloadAll(AssignmentSubmission $submission)
    {
        $files = $submission->assignmentFiles;

        if ($files->isEmpty()) {
            return back()->with('error', 'No files to download');
        }

        $zip = new \ZipArchive();
        $zipFileName = 'submission_' . $submission->id . '_files.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Create temp directory if it doesn't exist
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
            foreach ($files as $file) {
                if (Storage::disk('private')->exists($file->file_path)) {
                    $zip->addFile(Storage::disk('private')->path($file->file_path), $file->original_filename ?? $file->file_name);
                }
            }
            $zip->close();

            return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
        }

        return back()->with('error', 'Failed to create zip file');
    }

    /**
     * Remove the specified submission.
     */
    public function destroy(AssignmentSubmission $submission)
    {
        try {
            // Delete associated files from storage
            foreach ($submission->assignmentFiles as $file) {
                if (Storage::disk('private')->exists($file->file_path)) {
                    Storage::disk('private')->delete($file->file_path);
                }
            }

            $assignmentId = $submission->assignment_id;
            $submission->delete();

            return redirect()->route('admin.assignments.show', $assignmentId)
                ->with('success', 'Submission deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete submission: ' . $e->getMessage());
        }
    }
}
