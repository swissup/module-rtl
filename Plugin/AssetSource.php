<?php

namespace Swissup\Rtl\Plugin;

use Swissup\Rtl\Model\MixinsRenderer;

class AssetSource
{
    /**
     * @var MixinsRenderer
     */
    private $mixinsRenderer;

    /**
     * @param MixinsRenderer $mixinsRenderer
     */
    public function __construct(MixinsRenderer $mixinsRenderer)
    {
        $this->mixinsRenderer = $mixinsRenderer;
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

        $filepath = $asset->getSourceFile();
        $filename = basename($filepath);

        if ($result &&
            strpos($filename, MixinsRenderer::FILENAME) !== false &&
            strpos($result, MixinsRenderer::PLACEHOLDER) !== false
        ) {
            $result = str_replace(
                MixinsRenderer::PLACEHOLDER,
                $this->mixinsRenderer->render($asset->getContext()),
                $result
            );
        }

        return $result;
    }
}
