<?php
namespace dumuro\tinymce\fm;

use yii\base\Object;
use yii\web\JsExpression;
use yii\web\View;

/**
 * Abstract FileManager to use with TinyMce.
 * For example see elFinder extension.
 */
abstract class FileManager extends Object
{

    /**
     * Initialize FileManager component, registers required JS
     */
    public function init()
    {

    }

    /**
     * @return JsExpression JavaScript callback function
     */
    abstract public function getFileBrowserCallback();

    abstract public function registerAsset();
}
