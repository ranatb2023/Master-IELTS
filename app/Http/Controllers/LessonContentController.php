<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\VideoContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LessonContentController extends Controller
{
    /**
     * Show the lesson player page with embedded media
     */
    public function show(Request $request, Lesson $lesson)
    {
        // Load relationships
        $lesson->load(['topic.course', 'contentable', 'resources']);

        // Authorization check
        if (!$this->checkAccess($lesson)) {
            abort(403, 'You must be enrolled in this course to access this lesson');
        }

        return view('lessons.player', compact('lesson'));
    }

    /**
     * Stream a protected video file
     * Only accessible to users enrolled in the course or admins/tutors
     */
    public function streamVideo(Request $request, Lesson $lesson)
    {
        // Check if lesson has video content
        if ($lesson->content_type !== 'video' || !$lesson->contentable) {
            abort(404, 'Video content not found');
        }

        $videoContent = $lesson->contentable;

        // If it's a URL-based video, redirect to the URL
        if ($videoContent->source === 'url') {
            if ($videoContent->url) {
                return redirect($videoContent->url);
            }
            abort(404, 'Video URL not found');
        }

        // Check if file exists
        if (!$videoContent->file_path || !Storage::disk('local')->exists($videoContent->file_path)) {
            abort(404, 'Video file not found');
        }

        // Authorization check
        $user = auth()->user();

        // Allow if user is admin or tutor
        if ($user && ($user->hasRole('super_admin') || $user->hasRole('tutor'))) {
            return $this->streamFile($videoContent);
        }

        // Check if lesson is a preview (free access)
        if ($lesson->is_preview && $lesson->is_published) {
            return $this->streamFile($videoContent);
        }

        // For students, check if they are enrolled in the course
        if ($user && $user->hasRole('student')) {
            $course = $lesson->topic->course;
            $isEnrolled = $course->enrollments()
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->exists();

            if ($isEnrolled) {
                return $this->streamFile($videoContent);
            }
        }

        // If not authorized, deny access
        abort(403, 'You must be enrolled in this course to access this video');
    }

    /**
     * Stream the video file with support for range requests (seeking)
     */
    private function streamFile(VideoContent $videoContent): StreamedResponse
    {
        $path = Storage::disk('local')->path($videoContent->file_path);
        $fileSize = Storage::disk('local')->size($videoContent->file_path);
        $mimeType = Storage::disk('local')->mimeType($videoContent->file_path);

        return response()->stream(function () use ($path) {
            $stream = fopen($path, 'rb');
            fpassthru($stream);
            fclose($stream);
        }, 200, [
            'Content-Type' => $mimeType,
            'Content-Length' => $fileSize,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'no-cache, must-revalidate',
        ]);
    }

    /**
     * Stream protected audio file
     */
    public function streamAudio(Request $request, Lesson $lesson)
    {
        // Check if lesson has audio content
        if ($lesson->content_type !== 'audio' || !$lesson->contentable) {
            abort(404, 'Audio content not found');
        }

        $audioContent = $lesson->contentable;

        // Check if file exists
        if (!$audioContent->file_path || !Storage::disk('local')->exists($audioContent->file_path)) {
            abort(404, 'Audio file not found');
        }

        // Authorization check
        if (!$this->checkAccess($lesson)) {
            abort(403, 'You must be enrolled in this course to access this audio');
        }

        return $this->streamPrivateFile($audioContent->file_path, $audioContent->file_name ?? basename($audioContent->file_path));
    }

    /**
     * Stream protected document file
     */
    public function streamDocument(Request $request, Lesson $lesson)
    {
        // Check if lesson has document content
        if ($lesson->content_type !== 'document' || !$lesson->contentable) {
            abort(404, 'Document content not found');
        }

        $documentContent = $lesson->contentable;

        // Check if file exists
        if (!$documentContent->file_path || !Storage::disk('local')->exists($documentContent->file_path)) {
            abort(404, 'Document file not found');
        }

        // Authorization check
        if (!$this->checkAccess($lesson)) {
            abort(403, 'You must be enrolled in this course to access this document');
        }

        return $this->streamPrivateFile($documentContent->file_path, $documentContent->file_name);
    }

    /**
     * Stream protected presentation file
     */
    public function streamPresentation(Request $request, Lesson $lesson)
    {
        // Check if lesson has presentation content
        if ($lesson->content_type !== 'presentation' || !$lesson->contentable) {
            abort(404, 'Presentation content not found');
        }

        $presentationContent = $lesson->contentable;

        // Check if file exists
        if (!$presentationContent->file_path || !Storage::disk('local')->exists($presentationContent->file_path)) {
            abort(404, 'Presentation file not found');
        }

        // Authorization check
        if (!$this->checkAccess($lesson)) {
            abort(403, 'You must be enrolled in this course to access this presentation');
        }

        return $this->streamPrivateFile($presentationContent->file_path, basename($presentationContent->file_path));
    }

    /**
     * Stream file from private (local) disk
     */
    private function streamPrivateFile(string $filePath, string $fileName): BinaryFileResponse
    {
        $path = Storage::disk('local')->path($filePath);
        $mimeType = Storage::disk('local')->mimeType($filePath);

        return response()->file($path, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }

    /**
     * Check if the current user has access to the lesson
     */
    private function checkAccess(Lesson $lesson): bool
    {
        $user = auth()->user();

        // Allow if user is admin or tutor
        if ($user && ($user->hasRole('super_admin') || $user->hasRole('tutor'))) {
            return true;
        }

        // Check if lesson is a preview (free access)
        if ($lesson->is_preview && $lesson->is_published) {
            return true;
        }

        // For students, check if they are enrolled in the course
        if ($user && $user->hasRole('student')) {
            $course = $lesson->topic->course;
            return $course->enrollments()
                ->where('user_id', $user->id)
                ->where('status', 'active')
                ->exists();
        }

        return false;
    }
}
