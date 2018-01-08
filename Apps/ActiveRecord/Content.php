<?php

namespace Apps\ActiveRecord;

use Ffcms\Core\Arch\ActiveModel;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Traits\SearchableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

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
     * Get item path URI - category/item
     * @return null|string
     */
    public function getPath()
    {
        if ($this->path === null) {
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
    public function getPosterUri()
    {
        $pName = $this->poster;
        // check if poster is defined
        if ($pName === null || Str::likeEmpty($pName)) {
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
    public function getPosterThumbUri()
    {
        $pName = $this->poster;
        if ($pName === null || Str::likeEmpty($pName)) {
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

    /**
     * Get category relation of this content id
     * @return \Apps\ActiveRecord\ContentCategory|null
     * @deprecated
     */
    public function getCategory()
    {
        return ContentCategory::getById($this->category_id);
    }

    /**
     * Get content_rating relation one-to-many
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @deprecated
     */
    public function getRating()
    {
        return $this->ratings();
    }

    /**
     * Get content_tags relation one-to-many
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @deprecated
     */
    public function getTags()
    {
        return $this->tags();
    }
}
