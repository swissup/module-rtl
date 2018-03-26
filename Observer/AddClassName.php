<?php

namespace Swissup\Rtl\Observer;

use Swissup\Rtl\Helper\Data as RtlHelper;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\Event\Observer;

class AddClassName implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var PageConfig
     */
    protected $pageConfig;

    /**
     * @var RtlHelper
     */
    protected $helper;

    /**
     * Constructor
     *
     * @param PageConfig $pageConfig
     * @param RtlHelper  $helper
     */
    public function __construct(
        PageConfig $pageConfig,
        RtlHelper $helper
    ) {
        $this->pageConfig = $pageConfig;
        $this->helper = $helper;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->helper->isRtl()) {
            $this->pageConfig->addBodyClass('rtl');
        }
    }
}
