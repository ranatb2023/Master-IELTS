<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateTemplate;
use Illuminate\Http\Request;

class CertificateTemplateController extends Controller
{
    /**
     * Display a listing of certificate templates.
     */
    public function index()
    {
        $templates = CertificateTemplate::latest()->paginate(12);

        $stats = [
            'total' => CertificateTemplate::count(),
            'active' => CertificateTemplate::where('is_active', true)->count(),
            'default' => CertificateTemplate::where('is_default', true)->count(),
        ];

        return view('admin.certificate-templates.index', compact('templates', 'stats'));
    }

    /**
     * Show the form for creating a new template.
     */
    public function create()
    {
        return view('admin.certificate-templates.create');
    }

    /**
     * Store a newly created template.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'orientation' => 'required|in:landscape,portrait',
            'page_size' => 'required|string|max:50',
            'background_image' => 'nullable|image|max:5120', // 5MB max
            'design' => 'nullable|json',
            'fields' => 'nullable|json',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Handle background image upload
        if ($request->hasFile('background_image')) {
            $path = $request->file('background_image')->store('certificate-backgrounds', 'public');
            $validated['background_image'] = $path;
        }

        // Decode JSON fields
        $validated['design'] = $validated['design'] ?? '{}';
        $validated['fields'] = $validated['fields'] ?? '{}';
        $validated['design'] = json_decode($validated['design'], true);
        $validated['fields'] = json_decode($validated['fields'], true);

        // If this is set as default, unset other defaults
        if ($validated['is_default'] ?? false) {
            CertificateTemplate::where('is_default', true)->update(['is_default' => false]);
        }

        $template = CertificateTemplate::create($validated);

        return redirect()->route('admin.certificate-templates.show', $template)
            ->with('success', 'Certificate template created successfully!');
    }

    /**
     * Display the specified template.
     */
    public function show(CertificateTemplate $certificateTemplate)
    {
        $certificateTemplate->load([
            'certificates' => function ($query) {
                $query->latest()->limit(5);
            }
        ]);

        return view('admin.certificate-templates.show', compact('certificateTemplate'));
    }

    /**
     * Show the form for editing the specified template.
     */
    public function edit(CertificateTemplate $certificateTemplate)
    {
        return view('admin.certificate-templates.edit', compact('certificateTemplate'));
    }

    /**
     * Update the specified template.
     */
    public function update(Request $request, CertificateTemplate $certificateTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'orientation' => 'required|in:landscape,portrait',
            'page_size' => 'required|string|max:50',
            'background_image' => 'nullable|image|max:5120',
            'design' => 'nullable|json',
            'fields' => 'nullable|json',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Handle background image upload
        if ($request->hasFile('background_image')) {
            // Delete old image
            if ($certificateTemplate->background_image) {
                \Storage::disk('public')->delete($certificateTemplate->background_image);
            }
            $path = $request->file('background_image')->store('certificate-backgrounds', 'public');
            $validated['background_image'] = $path;
        }

        // Decode JSON fields
        if (isset($validated['design'])) {
            $validated['design'] = json_decode($validated['design'], true);
        }
        if (isset($validated['fields'])) {
            $validated['fields'] = json_decode($validated['fields'], true);
        }

        // If this is set as default, unset other defaults
        if ($validated['is_default'] ?? false) {
            CertificateTemplate::where('id', '!=', $certificateTemplate->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $certificateTemplate->update($validated);

        return redirect()->route('admin.certificate-templates.show', $certificateTemplate)
            ->with('success', 'Template updated successfully!');
    }

    /**
     * Remove the specified template.
     */
    public function destroy(CertificateTemplate $certificateTemplate)
    {
        // Check if template is in use
        if ($certificateTemplate->certificates()->count() > 0) {
            return back()->withErrors([
                'error' => 'Cannot delete template that is in use by certificates.'
            ]);
        }

        // Delete background image
        if ($certificateTemplate->background_image) {
            \Storage::disk('public')->delete($certificateTemplate->background_image);
        }

        $certificateTemplate->delete();

        return redirect()->route('admin.certificate-templates.index')
            ->with('success', 'Template deleted successfully.');
    }

    /**
     * Preview template with sample data.
     */
    public function preview(CertificateTemplate $certificateTemplate)
    {
        // Sample data for preview
        $sampleData = [
            'student_name' => 'John Doe',
            'course_name' => 'IELTS Preparation Course',
            'completion_date' => now()->format('F j, Y'),
            'certificate_number' => 'CERT-SAMPLE-' . now()->year,
            'verification_url' => route('certificates.verify'),
            'instructor_name' => 'Jane Smith',
            'course_duration' => '12 weeks',
            'platform_name' => config('app.name', 'Master IELTS'),
        ];

        return view('admin.certificate-templates.preview', compact('certificateTemplate', 'sampleData'));
    }

    /**
     * Duplicate an existing template.
     */
    public function duplicate(CertificateTemplate $certificateTemplate)
    {
        $newTemplate = $certificateTemplate->replicate();
        $newTemplate->name = $certificateTemplate->name . ' (Copy)';
        $newTemplate->is_default = false;
        $newTemplate->save();

        // Copy background image if exists
        if ($certificateTemplate->background_image) {
            $oldPath = $certificateTemplate->background_image;
            $extension = pathinfo($oldPath, PATHINFO_EXTENSION);
            $newPath = 'certificate-backgrounds/' . uniqid() . '.' . $extension;

            if (\Storage::disk('public')->exists($oldPath)) {
                \Storage::disk('public')->copy($oldPath, $newPath);
                $newTemplate->background_image = $newPath;
                $newTemplate->save();
            }
        }

        return redirect()->route('admin.certificate-templates.edit', $newTemplate)
            ->with('success', 'Template duplicated successfully!');
    }

    /**
     * Set template as default.
     */
    public function setDefault(CertificateTemplate $certificateTemplate)
    {
        // Unset all other defaults
        CertificateTemplate::where('is_default', true)->update(['is_default' => false]);

        // Set this as default
        $certificateTemplate->update(['is_default' => true]);

        return back()->with('success', 'Template set as default successfully.');
    }
}
