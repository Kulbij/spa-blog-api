<?php

namespace App\Services;

use App\Traits\LogTrait;
use App\Models\Blog\Post;
use App\DataFinders\PostsFinder;
use App\Exceptions\AppException;
use App\Http\Resources\Blog\PostResource;
use App\Http\Resources\Blog\PostCollection;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class BlogService
 *
 * @package App\Services
 */
class BlogService
{
    use LogTrait;

    /**
     * @var PostsFinder
     */
    private PostsFinder $finder;

    /**
     * CompanyHandler constructor.
     *
     * @param PostsFinder $finder
     */
    public function __construct(PostsFinder $finder)
    {
        $this->finder = $finder;

        $this->setCode('POST');
    }

    /**
     * @return PostCollection
     */
    public function list(): PostCollection
    {
        $posts = $this->finder->getQuery()->get();

        return new PostCollection($posts);
    }

    /**
     * @param  string  $postId
     *
     * @return PostResource
     */
    public function show(string $postId): PostResource
    {
        $post = $this->finder->getById($postId);;

        return new PostResource($post);
    }

    /**
     * @param $data
     *
     * @return null|User
     */
    public function create($data): ?Post
    {
        try {
            return Post::create($data);
        } catch (\Exception $exception) {
            LogService::exception($exception->getMessage(), $exception, 'E_POST_CREATE');

            return null;
        }
    }

    /**
     * @param string $postId
     * @param array $data
     *
     * @return PostResource
     * @throws ValidationException
     */
    public function update(string $postId, array $data): PostResource
    {
        $post = $this->finder->getById($postId);;

        $post->fill($data);

        try {
            $post->save();
        } catch (\Exception $exception) {
            LogService::exception($exception->getMessage(), $exception, 'E_COMPANY_UPDATE');
        }

        LogService::info('Post update', __CLASS__ . ':' . __FUNCTION__, 'I_POST_UPDATE', [
            'postId' => $post->id,
        ]);

        return new PostResource($post);
    }

    /**
     * @param  int  $postId
     *
     * @return void
     * @throws AppException
     * @throws ValidationException
     */
    public function delete(int $postId): void
    {
        $post = $this->finder->getById($postId);

        if (null === $post) {
            throw new UnprocessableEntityHttpException();
        }

        try {
            $isDeleted = $post->delete();
        } catch (Exception $exception) {
            throw new AppException($exception->getMessage(), 500);
        }

        if ($isDeleted) {
            $this->info('Post delete', 'delete', ['postId' => $postId]);
        } else {
            $this->warning('Post delete', 'delete', ['postId' => $postId]);
            throw new AppException(('post.delete.error'), 500);
        }
    }
}
