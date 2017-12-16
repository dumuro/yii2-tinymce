<?php

namespace dumuro\tinymce;

/**
 * Class TinyMceAsset
 * @package dumuro\tinymce
 */
class TinyMceAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@bower/tinymce';
    public $js = [
        'tinymce.jquery.min.js',
        'jquery.tinymce.min.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset'
    ];
}