<?php

namespace App\Models\Blog;

use App\Models\Blog\Post;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Category
 *
 * @package App\Models\Blog
 */
class Category extends Model
{
    use SoftDeletes;
    use HasFactory;

    /**
     * @var string
     */
    public $table = 'categories';


    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The boot method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // $model->{$model->getKeyName()} = (string) Str::uuid();

            $model->slug = (string) Str::slug($model->name);
        });
    }

    /**
     * @return mixed
     */
    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    /**
     * @return HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
