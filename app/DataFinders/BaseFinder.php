<?php

namespace App\DataFinders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class BaseFinder
 *
 * @package App\DataFinders
 */
class BaseFinder
{
    /**
     * @var Model
     */
    protected Model $model;

    /**
     * BaseFinder constructor.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @return Collection|null
     */
    public function all(): ?Collection
    {
        return $this->getQuery()->get();
    }

    /**
     * @param  string  $id
     *
     * @return Model
     */
    public function find(string $id): Model
    {
        return $this->getQuery()->findOrFail($id);
    }

    /**
     * @param  string  $id
     *
     * @return Model
     */
    public function findTrashed(string $id): Model
    {
        return $this->getQuery()->withTrashed()->findOrFail($id);
    }

    /**
     * @param  string  $column
     *
     * @return Builder
     */
    public function getGroupByQuery(string $column): Builder
    {
        return $this->getQuery()->groupBy($column);
    }

    /**
     * @param  string  $column
     * @param  string  $direction
     *
     * @return Builder
     */
    public function getOrderByQuery(string $column, string $direction = 'ASC'): Builder
    {
        return $this->getQuery()->orderBy($column, $direction);
    }

    /**
     * @return Builder
     */
    public function getQuery(): Builder
    {
        return $this->getModel()->newQuery();
    }

    /**
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }
}
