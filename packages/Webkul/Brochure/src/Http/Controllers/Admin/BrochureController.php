<?php

namespace Webkul\Brochure\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Brochure\DataGrids\BrochureDataGrid;
use Webkul\Brochure\Http\Requests\Admin\BrochureRequest;
use Webkul\Brochure\Repositories\BrochureRepository;

class BrochureController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(protected BrochureRepository $brochureRepository) {}

    /**
     * Display a listing of brochures (DataGrid for AJAX, view for normal request).
     */
    public function index(): mixed
    {
        if (request()->ajax()) {
            return datagrid(BrochureDataGrid::class)->process();
        }

        return view('brochure::admin.index');
    }

    /**
     * Show the form for creating a new brochure.
     */
    public function create(): View
    {
        return view('brochure::admin.create');
    }

    /**
     * Store a newly created brochure in storage.
     */
    public function store(BrochureRequest $request): mixed
    {
        try {
            $data = $request->only([
                'title',
                'type',
                'status',
                'sort_order',
                'meta_title',
                'meta_description',
            ]);

            $pdfFile        = $request->hasFile('pdf_file')     ? $request->file('pdf_file')     : null;
            $pageImages     = $request->hasFile('page_images')  ? $request->file('page_images')  : [];
            $coverImageFile = $request->hasFile('cover_image')  ? $request->file('cover_image')  : null;

            $this->brochureRepository->createWithUpload($data, $pdfFile, $pageImages, $coverImageFile);

            session()->flash('success', trans('brochure::app.admin.create-success'));

            return redirect()->route('admin.brochure.index');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Show the form for editing a brochure.
     */
    public function edit(int $id): View
    {
        $brochure = $this->brochureRepository->findOrFail($id);

        return view('brochure::admin.edit', compact('brochure'));
    }

    /**
     * Update an existing brochure in storage.
     */
    public function update(BrochureRequest $request, int $id): mixed
    {
        try {
            $brochure = $this->brochureRepository->findOrFail($id);

            $data = $request->only([
                'title',
                'type',
                'status',
                'sort_order',
                'meta_title',
                'meta_description',
            ]);

            $pdfFile        = $request->hasFile('pdf_file')    ? $request->file('pdf_file')    : null;
            $pageImages     = $request->hasFile('page_images') ? $request->file('page_images') : [];
            $coverImageFile = $request->hasFile('cover_image') ? $request->file('cover_image') : null;

            $this->brochureRepository->updateWithUpload($brochure, $data, $pdfFile, $pageImages, $coverImageFile);

            session()->flash('success', trans('brochure::app.admin.update-success'));

            return redirect()->route('admin.brochure.index');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());

            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove a brochure and its associated files.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->brochureRepository->deleteWithFiles($id);

            return new JsonResponse([
                'message' => trans('brochure::app.admin.delete-success'),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mass-delete selected brochures.
     */
    public function massDestroy(Request $request): JsonResponse
    {
        $indices = $request->input('indices', []);

        try {
            foreach ($indices as $id) {
                $this->brochureRepository->deleteWithFiles((int) $id);
            }

            return new JsonResponse([
                'message' => trans('brochure::app.admin.mass-delete-success'),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
