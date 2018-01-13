<?php


namespace Apps\Controller\Api\Profile;

use Apps\ActiveRecord\WallAnswer;
use Ffcms\Core\Exception\NativeException;
use Ffcms\Core\Helper\Type\Any;
use Ffcms\Core\Helper\Type\Str;
use Ffcms\Core\Network\Request;
use Ffcms\Core\Network\Response;

/**
 * Trait ActionWallAnswerCount
 * @package Apps\Controller\Api\Profile
 * @property Response $response
 * @property Request $request
 * @method void setJsonHeader()
 */
trait ActionWallAnswerCount
{

    /**
     * Get wall answer's count by post-ids list
     * @param string $postIds
     * @throws NativeException
     * @return string
     */
    public function wallAnswerCount(string $postIds): ?string
    {
        // set header
        $this->setJsonHeader();
        // check query length
        if (Any::isEmpty($postIds)) {
            throw new NativeException('Wrong input count');
        }

        $list = explode(',', $postIds);
        $itemCount = count($list);
        // empty or is biggest then limit?
        if ($itemCount < 1 || $itemCount > self::ITEM_PER_PAGE) {
            throw new NativeException('Wrong input count');
        }

        // prepare response
        $response = [];
        foreach ($list as $post) {
            $response[$post] = WallAnswer::where('post_id', $post)->count();
        }

        // display json data
        return json_encode([
            'status' => 1,
            'data' => $response
        ]);
    }
}
