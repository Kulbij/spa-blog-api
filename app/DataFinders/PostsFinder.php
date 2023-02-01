<?php

namespace App\DataFinders;

use App\Models\Blog\Post;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class PostsFinder
 *
 * @package App\DataFinders
 */
class PostsFinder extends BaseFinder
{
    /**
     * PostsFinder constructor.
     */
    public function __construct()
    {
        parent::__construct(app(Post::class));
    }

    /**
     * @param string $id
     *
     * @return mixed
     */
    public function getById(string $id): Post
    {
        return $this->getQuery()
            ->with([
                'tags',
                'category',
            ])
            ->where(Post::getTableName() . '.id', $id)
            ->firstOrFail()
        ;
    }
}
