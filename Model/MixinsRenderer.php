<?php

namespace Swissup\Rtl\Model;

use Magento\Framework\View\Asset\Source;
use Magento\Framework\View\Asset\File\FallbackContext;
use Magento\Framework\View\Asset\PreProcessor\AlternativeSource\AssetBuilder;

class MixinsRenderer
{
    const FILENAME = '_modrtl.less';

    const PLACEHOLDER = '// @modrtl';

    /**
     * @var \Swissup\Rtl\Helper\Data
     */
    private $helper;

    /**
     * @var AssetBuilder
     */
    private $assetBuilder;

    /**
     * @var Source
     */
    private $assetSource;

    /**
     * @param \Swissup\Rtl\Helper\Data $helper
     * @param AssetBuilder $assetBuilder
     * @param Source $assetSource
     */
    public function __construct(
        \Swissup\Rtl\Helper\Data $helper,
        AssetBuilder $assetBuilder,
        Source $assetSource
    ) {
        $this->helper = $helper;
        $this->assetBuilder = $assetBuilder;
        $this->assetSource = $assetSource;
    }

    /**
     * @param  mixed $context
     * @return string
     */
    public function render($context)
    {
        if (!$context instanceof FallbackContext) {
            return '';
        }

        if ($this->helper->isRtl($context->getLocale())) {
            $vars = 'css/_vars_rtl.less';
        } else {
            $vars = 'css/_vars_ltr.less';
        }

        $result = '';
        foreach ([$vars, 'css/_mixins.less'] as $path) {
            $result .= $this->assetSource->getContent(
                $this->getAsset($path, $context)
            );
        }

        return $result;
    }

    /**
     * @param  string $path
     * @param  mixed  $context
     * @return \Magento\Framework\View\Asset\File
     */
    private function getAsset($path, $context)
    {
        return $this->assetBuilder
            ->setArea($context->getAreaCode())
            ->setTheme($context->getThemePath())
            ->setLocale($context->getLocale())
            ->setModule('Swissup_Rtl')
            ->setPath($path)
            ->build();
    }
}
