<?php

namespace App\DataFinders;

use App\Models\Blog\Category;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CategoriesFinder
 *
 * @package App\DataFinders
 */
class CategoriesFinder extends BaseFinder
{
    /**
     * CategoriesFinder constructor.
     */
    public function __construct()
    {
        parent::__construct(app(Category::class));
    }

    /**
     * @param string $id
     *
     * @return mixed
     */
    public function getById(string $id): Category
    {
        return $this->getQuery()
            ->with([
                'posts'
            ])
            ->where(Category::getTableName() . '.id', $id)
            ->firstOrFail()
        ;
    }
}
