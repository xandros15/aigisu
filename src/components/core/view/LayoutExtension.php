<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-08
 * Time: 18:05
 */

namespace Aigisu\view;


class LayoutExtension implements ViewExtension
{

    const PH_HEAD = '<![CDATA[SLIM-BLOCK-HEAD]]>';

    const PH_BODY_BEGIN = '<![CDATA[SLIM-BLOCK-BODY-BEGIN]]>';

    const PH_BODY_END = '<![CDATA[SLIM-BLOCK-BODY-END]]>';

    private $assetBundles;

    public function head()
    {
        echo self::PH_HEAD;
    }


    public function beginPage()
    {
        ob_start();
        ob_implicit_flush(false);
    }

    public function beginBody()
    {
        echo self::PH_BODY_BEGIN;
    }

    public function endBody()
    {
        echo self::PH_BODY_END;

//        foreach (array_keys($this->assetBundles) as $bundle) {
//            $this->registerAssetFiles($bundle);
//        }
    }

    public function endPage()
    {
        $content = ob_get_clean();

        echo strtr($content, [
            self::PH_HEAD => $this->renderHeadHtml(),
            self::PH_BODY_BEGIN => $this->renderBodyBeginHtml(),
            self::PH_BODY_END => $this->renderBodyEndHtml(),
        ]);
    }

    private function renderHeadHtml()
    {
        return self::PH_HEAD;
    }

    private function renderBodyBeginHtml()
    {
        return self::PH_BODY_BEGIN;
    }

    private function renderBodyEndHtml()
    {
        return self::PH_BODY_END;
    }

    public function applyCallbacks(CallbackManager &$callbackManager)
    {
        $callbackManager->addCallbacks([
            'beginPage' => [$this, 'beginPage'],
            'head' => [$this, 'head'],
            'beginBody' => [$this, 'beginBody'],
            'endBody' => [$this, 'endBody'],
            'endPage' => [$this, 'endPage']
        ]);
    }

    private function registerAssetFiles($bundle)
    {
    }
}