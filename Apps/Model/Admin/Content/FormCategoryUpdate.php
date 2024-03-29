<?php

namespace Apps\Model\Admin\Content;

use Apps\ActiveRecord\ContentCategory;
use Ffcms\Core\App;
use Ffcms\Core\Arch\Model;
use Ffcms\Core\Exception\SyntaxException;
use Ffcms\Core\Helper\FileSystem\File;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Arr;
use Ffcms\Core\Helper\Type\Str;

/**
 * Class FormCategoryUpdate. Category update business logic model
 * @package Apps\Model\Admin\Content
 */
class FormCategoryUpdate extends Model
{
    public $id;
    public $path;
    public $title = [];
    public $description = [];
    public $configs = [];
    public $tpl;
    public $dependId = 1;

    private $_pathNested;

    private $_tmpDependId = null;
    private $_record;
    private $_new = false;

    /**
     * FormCategoryUpdate constructor. Pass record object inside model
     * @param ContentCategory $record
     * @param int|null $dependId
     */
    public function __construct(ContentCategory $record, $dependId = null)
    {
        $this->_record = $record;
        $this->_tmpDependId = (int)$dependId;
        parent::__construct();
    }

    /**
     * Set model attributes from record object
     */
    public function before()
    {
        if ($this->_record->id === null) {
            $this->_new = true;
            // pass owner id category from construct model
            $this->dependId = $this->_tmpDependId < 1 ? 1 : $this->_tmpDependId;
        } else {
            // make tmp id for frontend form
            $this->id = $this->_record->id;
            $path = $this->_record->path;
            // nesting levels
            if (Str::contains('/', $path)) {
                $nestedPath = explode('/', $path);
                $this->path = array_pop($nestedPath);
                $this->_pathNested = implode('/', $nestedPath);
                // get owner category id by nesting path
                $owner = ContentCategory::getByPath($this->_pathNested);
                if ($owner !== null) {
                    $this->dependId = $owner->id;
                }
            } else {
                $this->path = $path;
            }

            // set data from record
            $this->title = $this->_record->title;
            $this->description = $this->_record->description;
            if ($this->_record->configs !== null && !Any::isEmpty($this->_record->configs)) {
                $this->configs = $this->_record->configs;
            }
        }
    }

    /**
     * Form display labels
     * @return array
     */
    public function labels(): array
    {
        return [
            'title' => __('Title'),
            'description' => __('Description'),
            'path' => __('Path slug'),
            'dependId' => __('Owner category'),
            'configs.showDate' => __('Show date'),
            'configs.showRating' => __('Show rating'),
            'configs.showAuthor' => __('Show author'),
            'configs.showViews' => __('Show views'),
            'configs.showComments' => __('Show comments'),
            'configs.showPoster' => __('Show poster'),
            'configs.showCategory' => __('Show category'),
            'configs.showRss' => __('Show RSS'),
            'configs.showSimilar' => __("Show similar items"),
            'configs.showTags' => __('Keywords to tags'),
            'tpl' => __('Template')
        ];
    }

    /**
     * Validation rules
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            [['title', 'description', 'configs'], 'used'],
            ['tpl', 'required'],
            [['configs.showDate', 'configs.showRating', 'configs.showAuthor', 'configs.showViews', 'configs.showComments'], 'boolean'],
            [['configs.showPoster', 'configs.showCategory', 'configs.showRss', 'configs.showSimilar', 'configs.showTags'], 'boolean']
        ];

        // general category
        if (!$this->_new && (int)$this->_record->id === 1) {
            $rules[] = ['path', 'used'];
        } else {
            $rules[] = ['path', 'required'];
            $rules[] = ['dependId', 'required'];
            $rules[] = ['path', 'Apps\Model\Admin\Content\FormCategoryUpdate::pathIsFree'];
            $rules[] = ['path', 'reverse_match', '/[\/\'~`\!@#\$%\^&\*\(\)+=\{\}\[\]\|;:"\<\>,\?\\\]/'];
        }

        $rules[] = ['tpl', 'Apps\Model\Admin\Content\FormCategoryUpdate::validateTemplate'];

        $rules[] = ['title.' . App::$Request->getLanguage(), 'required'];


        return $rules;
    }

    /**
     * Save changed data in db
     */
    public function save()
    {
        $this->_record->title = $this->title;
        $this->_record->description = $this->description;
        $this->_record->tpl = $this->tpl;
        $savePath = trim($this->_pathNested . '/' . $this->path, '/');
        $this->_record->path = $savePath;
        $this->_record->configs = $this->configs;
        $this->_record->save();
    }

    /**
     * Get allowed category ids as array (string values for validation)
     * @return array|null
     */
    public function categoryList(): ?array
    {
        $response = ContentCategory::getSortedCategories();
        if ($this->id) {
            unset($response[$this->id]);
        }
        return $response;
    }

    /**
     * Validate pathway
     * @param string $path
     * @return bool
     * @throws SyntaxException
     */
    public function pathIsFree($path): bool
    {
        $owner = ContentCategory::getById($this->dependId);
        if (!$owner) {
            throw new SyntaxException(__('No owner category found'));
        }

        // build path with owner category
        $this->_pathNested = $owner->path;
        if (Str::length($this->_pathNested) > 0) {
            $path = $this->_pathNested . '/' . $path;
        }

        // make select for check
        $query = ContentCategory::where('path', $path);
        if (!$this->_new) {
            // exclude current category from checking path's
            $query->where('id', '!=', $this->_record->id);
        }

        return $query->count() === 0;
    }

    /**
     * Check if template is exist
     * @return bool
     */
    public function validateTemplate(): bool
    {
        return Arr::in($this->tpl, $this->getAvailableTemplates());
    }

    /**
     * Get available templates for content category in front
     * @return array
     */
    public function getAvailableTemplates(): array
    {
        $theme = App::$Properties->get('theme')['Front'] ?? 'default';
        $dir = File::listFiles('/Apps/View/Front/' . $theme . '/content/cattpl', ['.php'], true);
        if (!$dir || count($dir) < 1) {
            $dir = ['default'];
        }

        foreach ($dir as $idx => $file) {
            $dir[$idx] = Str::sub($file, 0, strlen($file)-4);
        }

        return $dir;
    }
}
