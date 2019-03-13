<?php

namespace Swissup\Rtl\Plugin;

class AssetSource
{
    const PLACEHOLDER = '// @modrtl';

    /**
     * @var \Swissup\Rtl\Helper\Data
     */
    private $helper;

    /**
     * @var \Magento\Framework\View\Asset\PreProcessor\AlternativeSource\AssetBuilder
     */
    private $assetBuilder;

    /**
     * @var \Magento\Framework\View\Asset\Source
     */
    private $assetSource;

    /**
     * @var \Magento\Framework\View\Asset\File\FallbackContext
     */
    private $staticContext;

    /**
     * @param \Swissup\Rtl\Helper\Data $helper
     * @param \Magento\Framework\View\Asset\PreProcessor\AlternativeSource\AssetBuilder $assetBuilder
     * @param \Magento\Framework\View\Asset\Source $assetSource
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     */
    public function __construct(
        \Swissup\Rtl\Helper\Data $helper,
        \Magento\Framework\View\Asset\PreProcessor\AlternativeSource\AssetBuilder $assetBuilder,
        \Magento\Framework\View\Asset\Source $assetSource,
        \Magento\Framework\View\Asset\Repository $assetRepo
    ) {
        $this->helper = $helper;
        $this->assetBuilder = $assetBuilder;
        $this->assetSource = $assetSource;
        $this->staticContext = $assetRepo->getStaticViewFileContext();
    }

    /**
     * Replace self::PLACEHOLDER inside result with mortl mixins.
     *
     * @param mixed $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterGetContent($subject, $result)
    {
        if (strpos($result, self::PLACEHOLDER) !== false) {
            $vars = $this->getVars();
            $mixins = $this->getMixins();

            $result = str_replace(
                self::PLACEHOLDER,
                $vars . $mixins,
                $result
            );
        }

        return $result;
    }

    /**
     * @return string
     */
    private function getVars()
    {
        if ($this->helper->isRtl()) {
            $path = 'css/_vars_rtl.less';
        } else {
            $path = 'css/_vars_ltr.less';
        }

        return $this->assetSource->getContent($this->getAsset($path));
    }

    /**
     * @return string
     */
    private function getMixins()
    {
        return $this->assetSource->getContent($this->getAsset('css/_mixins.less'));
    }

    /**
     * @param  string $path
     * @return \Magento\Framework\View\Asset\File
     */
    private function getAsset($path)
    {
        return $this->assetBuilder
            ->setArea($this->staticContext->getAreaCode())
            ->setTheme($this->staticContext->getThemePath())
            ->setLocale($this->staticContext->getLocale())
            ->setModule('Swissup_Rtl')
            ->setPath($path)
            ->build();
    }
}
