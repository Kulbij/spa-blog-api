<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Services\CategoryService;
use App\Http\Requests\Category\UpdateCategoryRequest;

/**
 * Class CategoryController
 *
 * @package App\Http\Controllers\Api
 */
class CategoryController extends BaseController
{
    /**
     * @var CategoryService
     */
    private CategoryService $categoryService;

    /**
     * CategoryController constructor.
     *
     * @param  CategoryService  $categoryService
     */
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $categories = $this->categoryService->list();

        return $this->sendResponse($categories);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $categoryId
     *
     * @return JsonResponse
     */
    public function show(string $categoryId): JsonResponse
    {
        $category = $this->categoryService->show($categoryId);

        return $this->sendResponse($category);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateCategoryRequest  $request
     *
     * @return JsonResponse
     */
    public function create(UpdateCategoryRequest $request): JsonResponse
    {
        return $this->sendResponse($this->categoryService->create($request->validated()));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateCategoryRequest  $request
     * @param  string  $categoryId
     *
     * @return JsonResponse
     */
    public function update(UpdateCategoryRequest $request, string $categoryId): JsonResponse
    {
        return $this->sendResponse($this->categoryService->update($categoryId, $request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $categoryId
     *
     * @return JsonResponse
     * @throws AppException
     * @throws ValidationException
     */
    public function delete(int $categoryId): JsonResponse
    {
        $this->categoryService->delete($categoryId);

        return $this->sendResponse(
            ['message' => ('category.delete.success')],
            Response::HTTP_NO_CONTENT
        );
    }
}
