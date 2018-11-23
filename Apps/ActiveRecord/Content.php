<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Traits\SearchableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * Class Content. Active record object for content items with relation to category active record
 * @package Apps\ActiveRecord
 * @property int $id
 * @property array $title
 * @property array $text
 * @property string $path
 * @property int $category_id
 * @property int $author_id
 * @property string $poster
 * @property bool $display
 * @property bool $important
 * @property array $meta_title
 * @property array $meta_keywords
 * @property array $meta_description
 * @property int $views
 * @property int $rating
 * @property string $source
 * @property string $comment_hash
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property ContentCategory $category
 * @property ContentRating[] $ratings
 * @property ContentTag[] $tags
 * @property User $user
 * @property CommentPost[]|Collection $commentPosts
 */
class Content extends ActiveModel
{
    use SoftDeletes, SearchableTrait;

    protected $searchable = [
        'columns' => [
            'title' => 10,
            'text' => 2
        ]
    ];

    protected $casts = [
        'id' => 'integer',
        'title' => 'serialize',
        'text' => 'serialize',
        'path' => 'string',
        'category_id' => 'integer',
        'author_id' => 'integer',
        'poster' => 'string',
        'display' => 'boolean',
        'meta_title' => 'serialize',
        'meta_keywords' => 'serialize',
        'meta_description' => 'serialize',
        'views' => 'integer',
        'rating' => 'integer',
        'source' => 'string',
        'important' => 'boolean'
    ];

    /**
     * Get content category object relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('Apps\ActiveRecord\ContentCategory', 'category_id');
    }

    /**
     * Get content rating objects relation
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ratings()
    {
        return $this->hasMany('Apps\ActiveRecord\ContentRating', 'content_id');
    }

    /**
     * Get content tag objects relation
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tags()
    {
        return $this->hasMany('Apps\ActiveRecord\ContentTag', 'content_id');
    }

    /**
     * Get user object relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('Apps\ActiveRecord\User', 'author_id');
    }

    /**
     * Get comments objects relation
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function commentPosts()
    {
        return $this->hasMany(CommentPost::class, 'app_relation_id')
            ->where('app_name', 'content');
    }

    /**
     * Get item path URI - category/item
     * @return null|string
     */
    public function getPath(): ?string
    {
        if (!$this->path) {
            return null;
        }

        // get category pathway
        $path = $this->category->path;
        if (!Str::likeEmpty($path)) {
            $path .= '/';
        }
        // add item path
        $path .= $this->path;

        return $path;
    }

    /**
     * Get poster URI like /upload/gallery/1/orig/9ds2jd1.png
     * @return null|string
     */
    public function getPosterUri(): ?string
    {
        $pName = $this->poster;
        // check if poster is defined
        if (!$pName || Str::likeEmpty($pName)) {
            return null;
        }

        // build path and check is file exists on disk
        $path = '/upload/gallery/' . $this->id . '/orig/' . $pName;
        if (!File::exist($path)) {
            return null;
        }

        return $path;
    }

    /**
     * Get poster thumbnail uri
     * @return null|string
     */
    public function getPosterThumbUri(): ?string
    {
        $pName = $this->poster;
        if (!$pName || Str::likeEmpty($pName)) {
            return null;
        }

        // remove extension, thumbs always in jpeg ;D
        $pName = Str::cleanExtension($pName);
        $path = '/upload/gallery/' . $this->id . '/thumb/' . $pName . '.jpg';

        if (!File::exist($path)) {
            return null;
        }

        return $path;
    }
}
