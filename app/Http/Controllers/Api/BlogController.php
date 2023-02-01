<?php

namespace App\Http\Controllers\Api;

use App\Services\BlogService;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Post\UpdatePostRequest;

/**
 * Class BlogController
 *
 * @package App\Http\Controllers\Api
 */
class BlogController extends BaseController
{
    /**
     * @var BlogService
     */
    private BlogService $blogService;

    /**
     * BlogController constructor.
     *
     * @param  BlogService  $blogService
     */
    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $posts = $this->blogService->list();

        return $this->sendResponse($posts);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $postId
     *
     * @return JsonResponse
     */
    public function show(string $postId): JsonResponse
    {
        $post = $this->blogService->show($postId);

        return $this->sendResponse($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdatePostRequest  $request
     *
     * @return JsonResponse
     */
    public function create(UpdatePostRequest $request): JsonResponse
    {
        return $this->sendResponse($this->blogService->create($request->validated()));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdatePostRequest  $request
     * @param  string  $postId
     *
     * @return JsonResponse
     */
    public function update(UpdatePostRequest $request, string $postId): JsonResponse
    {
        return $this->sendResponse($this->blogService->update($postId, $request->validated()));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $postId
     *
     * @return JsonResponse
     * @throws AppException
     * @throws ValidationException
     */
    public function delete(int $postId): JsonResponse
    {
        $this->blogService->delete($postId);

        return $this->sendResponse(
            ['message' => ('post.delete.success')],
            Response::HTTP_NO_CONTENT
        );
    }
}
