# Magento RTL

Detect RTL locale and add CSS class name to the body element if matched.

![DOM Document Screenshot](http://docs.swissuplabs.com/images/m2/rtl/dom-document.png)

### Installation

```bash
cd <magento_root>

composer require swissup/rtl
bin/magento module:enable Swissup_Rtl
bin/magento setup:upgrade
```

### Custom Usage

##### Check locale manually

Inject `\Swissup\Rtl\Helper\Data` into your class call `isRtl` method:

```php
// Check current locale
$helper->isRtl();

// Check custom locale
$helper->isRtl('en_US');
```
