<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Requests\GeneratePromptRequest;
use App\Http\Resources\ImageGenerationResource;
use App\Http\Controllers\Controller;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PromptGenerationController extends Controller
{
    public function __construct(private GeminiService $geminiService)
    {
        // throw new \Exception('Not implemented');
    }

    /**
     * List Image Generations
     *
     * Retrieve a paginated list of all image generations created by the authenticated user.
     * Supports filtering by generated prompt and sorting by various fields.
     *
     * Query parameters:
     * - search: Search term to filter by generated_prompt field
     * - sort: Field name with optional '-' prefix for descending order
     *   Examples: 'created_at', '-created_at', 'generated_prompt', '-file_size'
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollections
     */
    public function index(Request $request)
    {
        $user = request()->user();
        // $imageGenerations = $request->user()
        //     ->imageGenerations()
        //     ->latest()
        //     ->paginate();

        $query = $user->imageGenerations();

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $query->where('generated_prompt', 'LIKE', '%' . $request->search . '%');
        }

        // Apply sorting
        $allowedSortFilters = ['created_at', 'generated_prompt',
        'original_filename', 'file_size'];
        $sortField = 'created_at';
        $sortDirection = 'desc';

        if ($request->has('sort') && !empty($request->sort)) {
            $sort = $request->sort;
            if (str_starts_with($sort, '-')) {
                $sortField = substr($sort, 1);
                $sortDirection = 'desc';
            } else {
                $sortField = $sort;
                $sortDirection = 'asc';
            }
        }

        // Validate sort field
        if (!in_array($sortField, $allowedSortFilters)) {
            $sortField = 'created_at';
            $sortDirection = 'desc';
        }

        $query->orderBy($sortField, $sortDirection);

        $imageGenerations = $query->paginate($request->get('per_page'));

        return ImageGenerationResource::collection($imageGenerations);
    }

    /**
     * Generate prompt
     *
     * Generate descriptive promtp from image
    */
    public function store(GeneratePromptRequest $request)
    {

        $user = $request->user();
        $image = $request->file('image');

        $originalName = $image->getClientOriginalName();
        $sanitizedName = preg_replace('/[^a-zA-Z0-9]/', '_', pathinfo($originalName,
        PATHINFO_FILENAME));
        $extension = $image->getClientOriginalExtension();
        $safeFilename = $sanitizedName . '_' . Str::random(32) . '_' . $extension;

        $imagePath = $image->storeAs('uploads/images', $safeFilename, 'public');

        $generatedPrompt = $this->geminiService->generatePromptFromImage($image);

        $imageGeneration = $user->imageGenerations()->create([
            'image_path' => $imagePath,
            'generated_prompt' => $generatedPrompt,
            'original_filename' => $originalName,
            'file_size' => $image->getSize(),
            'mime_type' => $image->getMimeType(),
        ]);

        return new ImageGenerationResource($imageGeneration, 201);
    }
}
