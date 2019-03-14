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
     * @var mixed
     */
    private $context;

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
     * After is not used for Magento 2.2 compatibility.
     * (Params are not passed in "after" methods)
     *
     * @param mixed $subject
     * @param callable $proceed
     * @return mixed
     */
    public function aroundGetContent(
        \Magento\Framework\View\Asset\Source $subject,
        callable $proceed,
        \Magento\Framework\View\Asset\LocalInterface $asset
    ) {
        $result = $proceed($asset);

        if ($result && strpos($result, self::PLACEHOLDER) !== false) {
            $this->context = $asset->getContext();
            $result = str_replace(self::PLACEHOLDER, $this->getContent(), $result);
        }

        return $result;
    }

    /**
     * @return string
     */
    private function getContent()
    {
        if ($this->helper->isRtl($this->getContext()->getLocale())) {
            $path = 'css/_vars_rtl.less';
        } else {
            $path = 'css/_vars_ltr.less';
        }

        $vars = $this->assetSource->getContent($this->getAsset($path));
        $mixins = $this->assetSource->getContent($this->getAsset('css/_mixins.less'));

        return $vars . $mixins;
    }

    /**
     * @param  string $path
     * @return \Magento\Framework\View\Asset\File
     */
    private function getAsset($path)
    {
        return $this->assetBuilder
            ->setArea($this->getContext()->getAreaCode())
            ->setTheme($this->getContext()->getThemePath())
            ->setLocale($this->getContext()->getLocale())
            ->setModule('Swissup_Rtl')
            ->setPath($path)
            ->build();
    }

    /**
     * Try to use context from current asset as it will have correct locale.
     *
     * Had troubles with 'staticContext' when 'store' cookie has rtl store code,
     * but client is browsing non-rtl store with '?___store=' parameter.
     *
     * @return mixed
     */
    private function getContext()
    {
        if ($this->context instanceof \Magento\Framework\View\Asset\File\FallbackContext) {
            return $this->context;
        }
        return $this->staticContext;
    }
}
