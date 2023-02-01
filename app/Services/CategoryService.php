<?php

namespace App\Services;

use App\Traits\LogTrait;
use App\Models\Blog\Category;
use App\Exceptions\AppException;
use App\DataFinders\CategoriesFinder;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Category\CategoryCollection;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * Class CategoryService
 *
 * @package App\Services
 */
class CategoryService
{
    use LogTrait;

    /**
     * @var CategoriesFinder
     */
    private CategoriesFinder $finder;

    /**
     * CompanyHandler constructor.
     *
     * @param CategoriesFinder $finder
     */
    public function __construct(CategoriesFinder $finder)
    {
        $this->finder = $finder;

        $this->setCode('CATEGORY');
    }

    /**
     * @return CategoryCollection
     */
    public function list(): CategoryCollection
    {
        $categories = $this->finder->getQuery()->get();

        return new CategoryCollection($categories);
    }

    /**
     * @param  string  $categoryId
     *
     * @return CategoryResource
     */
    public function show(string $categoryId): CategoryResource
    {
        $category = $this->finder->getById($categoryId);;

        return new CategoryResource($category);
    }

    /**
     * @param $data
     *
     * @return null|Category
     */
    public function create($data): ?Category
    {
        try {
            return Category::create($data);
        } catch (\Exception $exception) {
            LogService::exception($exception->getMessage(), $exception, 'E_CATEGORY_CREATE');

            return null;
        }
    }

    /**
     * @param string $categoryId
     * @param array $data
     *
     * @return CategoryResource
     * @throws ValidationException
     */
    public function update(string $categoryId, array $data): CategoryResource
    {
        $category = $this->finder->getById($categoryId);;

        $category->fill($data);

        try {
            $category->save();
        } catch (\Exception $exception) {
            LogService::exception($exception->getMessage(), $exception, 'E_CATEGORY_UPDATE');
        }

        LogService::info('Category update', __CLASS__ . ':' . __FUNCTION__, 'I_CATEGORY_UPDATE', [
            'categoryId' => $category->id,
        ]);

        return new CategoryResource($category);
    }

    /**
     * @param  int  $categoryId
     *
     * @return void
     * @throws AppException
     * @throws ValidationException
     */
    public function delete(int $categoryId): void
    {
        $category = $this->finder->getById($categoryId);

        if (null === $category) {
            throw new UnprocessableEntityHttpException();
        }

        try {
            $isDeleted = $category->delete();
        } catch (Exception $exception) {
            throw new AppException($exception->getMessage(), 500);
        }

        if ($isDeleted) {
            $this->info('Category delete', 'delete', ['categoryId' => $categoryId]);
        } else {
            $this->warning('Category delete', 'delete', ['categoryId' => $categoryId]);
            throw new AppException(('category.delete.error'), 500);
        }
    }
}
