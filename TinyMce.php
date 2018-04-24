<?php

namespace dumuro\tinymce;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\InputWidget;

/**
 * Class TinyMce
 * @package dumuro\tinymce
 */
class TinyMce extends InputWidget
{

    /** @var bool|string Widget language if set, if false app language is used */
    public $language = false;

    /** @var bool|string Route to compressor action */
    public $compressorRoute = false;

    /**
     * For example here could be url to yandex spellchecker service.
     * http://speller.yandex.net/services/tinyspell
     * More info about it here: http://api.yandex.ru/speller/doc/dg/tasks/how-to-spellcheck-tinymce.xml
     *
     * Or you can build own spellcheking service using code provided by moxicode:
     * http://www.tinymce.com/download/download.php
     *
     * @var bool|string|array URL or an action route that can be used to create a URL or false if no url
     */
    public $spellcheckerUrl = false;

    /**
     * @var bool|array FileManager configuration.
     * For example:
     * 'fileManager' => array(
     *      'class' => 'FileManager',
     * )
     */
    public $fileManager = false;

    /**
     * @var bool whether to set the on change event for the editor. This is required to be able to validate data.
     * @see https://github.com/2amigos/yii2-tinymce-widget/issues/7
     */
    public $triggerSaveOnBeforeValidateForm = true;

    /**
     * @var string
     */
    public $selector = 'textarea';

    // TODO: Read existing files in assets/langs directory
    /** @var array Supported languages */
    private static $languages = array(
        'ar',
        'ar_SA',
        'az',
        'be',
        'bg_BG',
        'bn_BD',
        'bs',
        'ca',
        'cs',
        'cy',
        'da',
        'de',
        'de_AT',
        'dv',
        'el',
        'en_CA',
        'en_GB',
        'es',
        'et',
        'eu',
        'fa',
        'fi',
        'fo',
        'fr_FR',
        'gd',
        'gl',
        'he_IL',
        'hr',
        'hu_HU',
        'hy',
        'id',
        'is_IS',
        'it',
        'ja',
        'ka_GE',
        'kk',
        'km_KH',
        'ko_KR',
        'lb',
        'lt',
        'lv',
        'ml',
        'ml_IN',
        'mn_MN',
        'nb_NO',
        'nl',
        'pl',
        'pt_BR',
        'pt_PT',
        'ro',
        'ru',
        'si_LK',
        'sk',
        'sl_SI',
        'sr',
        'sv_SE',
        'ta',
        'ta_IN',
        'tg',
        'th_TH',
        'tr_TR',
        'tt',
        'ug',
        'uk',
        'uk_UA',
        'vi',
        'vi_VN',
        'zh_CN',
        'zh_TW',
    );

    private static $defaultSettings = array(
        'plugins' => array(
            "advlist autolink lists link image charmap print preview hr anchor pagebreak",
            "searchreplace visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking save table contextmenu directionality",
            "template paste textcolor"
        ),
        'toolbar' => "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor",
        'toolbar_items_size' => 'small',
        'image_advtab' => true,
        'relative_urls' => false,
        'spellchecker_languages' => "Russian=ru,English=en",
    );

    /** @var array Widget settings will override defaultSettings */
    public $settings = array();

    public function init()
    {
        parent::init();

        $this->settings = array_merge(self::$defaultSettings, $this->settings);

        if ($this->language === false) {
            $this->settings['language'] = Yii::$app->language;
        } else {
            $this->settings['language'] = $this->language;
        }

        $this->settings['language'] = str_replace('-', '_', $this->settings['language']);

        if (!in_array($this->settings['language'], self::$languages)) {
            $lang = false;
            foreach (self::$languages as $language) {
                if (strpos($this->settings['language'], $language) !== false) {
                    $lang = $language;
                }
            }

            if ($lang === false && ($pos = strpos($this->settings['language'], '-')) !== false) {
                $this->settings['language'] = substr($this->settings['language'], 0, $pos);

                foreach (self::$languages as $language) {
                    if (strpos($this->settings['language'], $language) !== false) {
                        $lang = $language;
                    }
                }
            }

            if ($lang !== false) {
                $this->settings['language'] = $lang;
            } else {
                $this->settings['language'] = 'en_GB';
            }
        }

        if ($this->spellcheckerUrl !== false) {
            $this->settings['plugins'][] = 'spellchecker';

            if (is_array($this->spellcheckerUrl)) {
                $this->settings['spellchecker_rpc_url'] = Url::toRoute($this->spellcheckerUrl);
            } else {
                $this->settings['spellchecker_rpc_url'] = $this->spellcheckerUrl;
            }
        }
    }

    public function run()
    {
        $this->registerScripts();

        if (isset($this->model)) {
            echo Html::activeTextarea($this->model, $this->attribute, $this->options);
        } else {
            echo Html::textArea($this->name, $this->value, $this->options);
        }
    }

    private function registerScripts()
    {
        $id = $this->options['id'];
        $view = $this->getView();

        $languagesDir = $view->getAssetManager()->getBundle(\dumuro\tinymce\TinyMceLangAsset::className())->baseUrl;
        $languagesFile = "{$languagesDir}/langs/{$this->settings['language']}.js";
        $this->settings['language_url'] = $languagesFile;

        if ($this->compressorRoute === false) {
            TinyMceAsset::register($view);
        } else {
            $opts = array(
                'files' => 'jquery.tinymce',
                'source' => defined('YII_DEBUG') && YII_DEBUG,
            );

            $opts["plugins"] = strtr(implode(',', $this->settings['plugins']), array(' ' => ','));

            if (isset($this->settings['theme'])) {
                $opts["themes"] = $this->settings['theme'];
            }

            $opts["files"] .= ',' . $languagesFile;

            $view->registerJsFile(
                TinyMceCompressorAction::scripUrl($this->compressorRoute, $opts),
                [
                    'depends' => [
                        'dumuro\tinymce\TinyMceAsset'
                    ]
                ]
            );
        }

        if ($this->fileManager !== false) {
            /** @var $fm FileManager */
            $fm = Yii::createObject(array_merge($this->fileManager,
                ['tinyMceSettings' => $this->settings, 'parentView' => $view]));
            $fm->init();
            $fm->registerAsset();
            $this->settings['file_browser_callback'] = $fm->getFileBrowserCallback();
        }

        if( Yii::$app->getRequest()->getIsAjax() ) {
            $js[] = "tinymce.remove()";
        }

        $settings = Json::encode($this->settings);

        $js[] = "tinymce.init({$settings})";
        if ($this->triggerSaveOnBeforeValidateForm) {
            $js[] = "$('{$this->selector}').parents('form').on('beforeValidate', function() { tinymce.triggerSave(); });";
        }

        $view->registerJs(implode("\n", $js));
    }
}
