<?php

namespace dumuro\tinymce;

/**
 * Class TinyMceAsset
 * @package dumuro\tinymce
 */
class TinyMceAsset extends \yii\web\AssetBundle
{
    /** @var string */
    public $sourcePath = 'tinymce/tinymce';

    /** @var array */
    public $depends = ['yii\web\JqueryAsset'];

    public function init()
    {
        parent::init();
        $this->js[] = YII_DEBUG ? 'tinymce.js' : 'tinymce.min.js';
        $this->js[] = YII_DEBUG ? 'tinymce.jquery.min.js' : 'tinymce.jquery.js';
    }
}