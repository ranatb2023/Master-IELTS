<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    /**
     * Display all published packages for public browse
     */
    public function index(Request $request)
    {
        $query = Package::query()
            ->where('status', 'published')
            ->where('is_active', true);

        // Filter by category if provided
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Search by name or description
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Sort options
        $sort = $request->get('sort', 'newest');

        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->withCount('userAccesses')
                    ->orderBy('user_accesses_count', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Get featured packages
        $featuredPackages = Package::where('status', 'published')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->take(3)
            ->get();

        $packages = $query->with('courses')->paginate(12)->withQueryString();

        $categories = Package::select('category')
            ->where('status', 'published')
            ->where('is_active', true)
            ->distinct()
            ->pluck('category')
            ->filter();

        return view('packages.index', compact(
            'packages',
            'featuredPackages',
            'categories'
        ));
    }

    /**
     * Show detailed package information for public viewing
     */
    public function show(Package $package)
    {
        // Check if package is available
        if ($package->status !== 'published' || !$package->is_active) {
            abort(404);
        }

        // Load relationships
        $package->load([
            'courses' => function ($query) {
                $query->where('status', 'published');
            }
        ]);

        // Get related packages (same category, exclude current)
        $relatedPackages = Package::where('status', 'published')
            ->where('is_active', true)
            ->where('category', $package->category)
            ->where('id', '!=', $package->id)
            ->take(3)
            ->get();

        return view('packages.show', compact(
            'package',
            'relatedPackages'
        ));
    }
}
