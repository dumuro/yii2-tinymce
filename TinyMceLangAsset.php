<?php
namespace dumuro\tinymce;

/**
 * Class TinyMceLangAsset
 * @package dumuro\tinymce
 */
class TinyMceLangAsset extends \yii\web\AssetBundle
{

    public $sourcePath = '@vendor/dumuro/yii2-tinymce/assets';
    public $depends = [
        'dumuro\tinymce\TinyMceAsset'
    ];
}
