<?php

namespace dumuro\tinymce\fm;

use Yii;
use yii\base\Exception;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;

/**
 * Class MihaildevElFinder
 * @package dumuro\tinymce\fm
 */
class MihaildevElFinder extends \dumuro\tinymce\fm\FileManager
{
    private $_id;
    private static $_counter = 0;
    private $managerUrl;

    public $tinyMceSettings = [];
    public $parentView;

    /** @var array Used by file manager assets */
    public $assets = [
        '\mihaildev\elfinder\AssetsCallBack'
    ];

    /** @var string elFinder controller */
    public $controller = 'elfinder';

    /** @var string Manager language. If not set try to use TinyMce language */
    public $language;

    /** @var string */
    public $filter;

    /** @var string */
    public $path;

    /** @var int Window width */
    public $width = 900;
    /** @var int Window height */
    public $height = 600;

    public $multiple;

    public function getId()
    {
        if ($this->_id !== null) {
            return $this->_id;
        } else {
            return $this->_id = 'elfd' . self::$_counter++;
        }
    }

    /**
     * @return string
     */
    public function getFileBrowserCallback()
    {
        if (!$this->language) {
            $this->language = $this->tinyMceSettings['language'];
        }

        $managerOptions = [
            'callback' => $this->getId()
        ];

        if (!empty($this->filter)) {
            $managerOptions['filter'] = $this->filter;
        }

        if (!empty($this->language)) {
            $managerOptions['lang'] = $this->language;
        }

        if (!empty($this->multiple)) {
            $managerOptions['multiple'] = $this->multiple;
        }

        if (!empty($this->path)) {
            $managerOptions['path'] = $this->path;
        }

        $managerOptions[0] = '/' . $this->controller . "/manager";

        $this->managerUrl = Yii::$app->urlManager->createUrl($managerOptions);

        $this->parentView->registerJs("mihaildev.elFinder.register(" . Json::encode($this->getId()) . ", function (file, id) { parent.tinymce.activeEditor.windowManager.getParams().setUrl(file.url); parent.tinymce.activeEditor.windowManager.close(); });");

        $script = <<<JS
        function(field_name, url, type, win) {
            tinymce.activeEditor.windowManager.open({
                file: '{$this->managerUrl}',
                title: 'ElFinder',
                width: '{$this->width}',
                height: '{$this->height}',
                resizable: 'yes'
            }, {
                setUrl: function (url) {
                    win.document.getElementById(field_name).value = url;
                }
            });

            return false;
        }
JS;

        return new JsExpression($script);
    }

    public function registerAsset()
    {
        if (!is_array($this->assets)) {
            $this->assets = [$this->assets];
        }

        foreach ($this->assets as $asset) {
            $this->parentView->registerAssetBundle($asset);
        }
    }
}
