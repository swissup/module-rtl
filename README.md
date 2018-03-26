# RTL

Detect RTL locale and add CSS class name to the body element if matched.

![DOM Document Screenshot](http://docs.swissuplabs.com/images/m2/rtl/dom-document.png)

### Installation

```bash
cd <magento_root>
composer config repositories.swissup composer http://swissup.github.io/packages/
composer require swissup/rtl --prefer-source

bin/magento module:enable Swissup_Rtl
bin/magento setup:upgrade
```
