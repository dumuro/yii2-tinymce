<?php

namespace dumuro\tinymce;

use yii\web\Controller;

/**
 * Class TinyMceController
 * @package dumuro\tinymce
 */
class TinyMceController extends Controller
{
    public function actions()
    {
        return [
            'tinyMceCompressor' => [
                'class' => TinyMceCompressorAction::className()
            ]
        ];
    }
}