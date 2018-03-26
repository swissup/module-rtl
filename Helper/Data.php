<?php

namespace Swissup\Rtl\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Locale\ResolverInterface;

class Data extends AbstractHelper
{
    /**
     * @var ResolverInterface
     */
    private $localeResolver;

    /**
     * List of RTL languages
     * @var array
     */
    private $rtlLanguages = [
        'ar'  => 'Arabic',
        'arc' => 'Aramaic',
        'dv'  => 'Dhivehi/Maldivian',
        'ha'  => 'Hausa',
        'he'  => 'Hebrew',
        'ks'  => 'Kashmiri',
        'khw' => 'Khowar',
        'ku'  => 'Kurdish',
        'ps'  => 'Pashto',
        'fa'  => 'Persian',
        'ur'  => 'Urdu',
        'yi'  => 'Yiddish',
    ];

    /**
     * Constructor
     *
     * @param Context $context
     * @param ResolverInterface $localeResolver
     */
    public function __construct(
        Context $context,
        ResolverInterface $localeResolver
    ) {
        parent::__construct($context);

        $this->localeResolver = $localeResolver;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->localeResolver->getLocale();
    }

    /**
     * Check if locale is a Right-to-Left locale
     *
     * @return boolean
     */
    public function isRtl($locale = null)
    {
        if (!$locale) {
            $locale = $this->getLocale();
        }

        list($language, $country) = explode('_', $locale);

        if (array_key_exists($language, $this->rtlLanguages)) {
            return true;
        }

        return false;
    }
}
