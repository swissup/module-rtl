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

    private $memo = [];

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
     * @param  string|null $locale
     * @return boolean
     * @api
     */
    public function isRtl($locale = null)
    {
        if (!$locale) {
            $locale = $this->getLocale();
        }

        if (!isset($this->memo[$locale])) {
            if (strstr($locale, '_') === false) {
                $language = 'en';
                $country = 'US';
            } else {
                list($language, $country) = explode('_', $locale);
            }

            $this->memo[$locale] = array_key_exists(
                $language,
                $this->rtlLanguages
            );
        }

        return $this->memo[$locale];
    }
}
