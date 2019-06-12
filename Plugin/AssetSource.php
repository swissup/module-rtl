<?php

namespace Swissup\Rtl\Plugin;

use Swissup\Rtl\Model\MixinsRenderer;
use Swissup\Rtl\Model\MixinsRendererFactory;

class AssetSource
{
    /**
     * @var MixinsRendererFactory
     */
    private $mixinsRendererFactory;

    /**
     * @param MixinsRendererFactory $mixinsRendererFactory
     */
    public function __construct(MixinsRendererFactory $mixinsRendererFactory)
    {
        $this->mixinsRendererFactory = $mixinsRendererFactory;
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

        try {
            $filepath = $asset->getSourceFile();
            $filename = basename($filepath);
        } catch (\Exception $e) {
            return $result;
        }

        if ($result &&
            strpos($filename, MixinsRenderer::FILENAME) !== false &&
            strpos($result, MixinsRenderer::PLACEHOLDER) !== false
        ) {
            $result = str_replace(
                MixinsRenderer::PLACEHOLDER,
                $this->mixinsRendererFactory->create()->render($asset->getContext()),
                $result
            );
        }

        return $result;
    }
}
