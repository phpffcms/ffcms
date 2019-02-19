<?php

/** @var Apps\Model\Front\Content\EntityContentRead $model */
/** @var Apps\Model\Front\Content\EntityContentSearch $search */
/** @var \Ffcms\Templex\Template\Template $this */
/** @var bool $trash */
/** @var array $configs */

use Ffcms\Core\Helper\Type\Any;

// set meta title
$title = $model->metaTitle;
if (Any::isEmpty($title)) {
    $title = $model->title;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= $title ?></title>
</head>
<body>
<h1><?= $model->title ?></h1>
<hr />
<?= $model->text ?>
</body>
</html>