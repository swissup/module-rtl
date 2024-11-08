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
     * @param \Magento\Framework\View\Asset\Source $subject
     * @param bool|string $result
     * @param \Magento\Framework\View\Asset\LocalInterface $asset
     * @return bool|string
     */
    public function afterGetContent(
        \Magento\Framework\View\Asset\Source $subject,
        $result,
        \Magento\Framework\View\Asset\LocalInterface $asset
    ) {
        if ($result && strpos($result, MixinsRenderer::PLACEHOLDER) !== false) {
            $result = str_replace(
                MixinsRenderer::PLACEHOLDER,
                $this->mixinsRendererFactory->create()->render($asset->getContext()),
                $result
            );
        }

        return $result;
    }
}
