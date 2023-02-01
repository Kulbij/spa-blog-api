<?php

namespace App\Models\Blog;

use App\Models\Blog\Tag;
use Illuminate\Support\Str;
use App\Models\Blog\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Post
 *
 * @package App\Models\Blog
 */
class Post extends Model
{
    use SoftDeletes;
    use HasFactory;

    /**
     * @var string
     */
    public $table = 'articles';

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'name',
        'description',
        'is_enabled',
        'is_published',
        'category_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'deleted_at',
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
    public static function boot(): void
    {
        parent::boot();

        // static::creating(function ($model) {
        //     $model->{$model->getKeyName()} = (string) Str::uuid();
        // });
    }

    /**
     * @return mixed
     */
    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsToMany
     */
    public function tags(): BelongsToMany
    {
        return
            $this->belongsToMany(Tag::class, 'post_tag');
    }
}
