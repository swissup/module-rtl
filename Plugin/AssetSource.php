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
     * @var \Magento\Framework\View\Asset\LocalInterface
     */
    private $asset;

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
     * Magento 2.2 compatibility. $asset doesn't exists in "after" methods.
     *
     * @param \Magento\Framework\View\Asset\Source $subject
     * @param \Magento\Framework\View\Asset\LocalInterface $asset
     * @return mixed
     */
    public function beforeGetContent(
        \Magento\Framework\View\Asset\Source $subject,
        \Magento\Framework\View\Asset\LocalInterface $asset
    ) {
        $this->asset = $asset;
        $this->context = $asset->getContext();

        return [$asset];
    }

    /**
     * Replace self::PLACEHOLDER inside result with mortl mixins.
     *
     * @param \Magento\Framework\View\Asset\Source $subject
     * @param string $result
     * @return bool|string
     */
    public function afterGetContent(
        \Magento\Framework\View\Asset\Source $subject,
        $result
    ) {
        if ($result && strpos($result, self::PLACEHOLDER) !== false) {
            $vars = $this->getVars();
            $mixins = $this->getMixins();
            $result = str_replace(self::PLACEHOLDER, $vars . $mixins, $result);
        }

        return $result;
    }

    /**
     * @return string
     */
    private function getVars()
    {
        if ($this->helper->isRtl($this->getContext()->getLocale())) {
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
