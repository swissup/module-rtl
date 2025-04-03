# Magento RTL

Magento module that helps to add RTL support to your modules and themes.

## Installation

```bash
composer require swissup/module-rtl
bin/magento module:enable Swissup_Rtl
```

## Usage

### CSS class

Module automatically adds `rtl` class name to the body element
when current language is detected as RTL. This allows you to write plain
RTL-specific styles in your css/less files:

```css
.my-element {
    right: 20px;
}
.rtl .my-element {
    right: auto;
    left: 20px;
}
```

While this approach is nice for the small files, it becomes a headache
when dealing with large portion of css that should be adjusted. Additionally,
this approach make your style files larger.

We recommend to use [LESS mixins](#less-mixins) for the best experience.

### LESS mixins

This approach allows to keep minimal size of generated CSS file:

```less
@import 'Swissup_Rtl::css/_modrtl.less';

.sidebar-popup {
    .left(0);
    .modrtl(transform, ~"translateX(-100%)", ~"translateX(100%)");

    &.shown {
        transform: translateX(0);
    }

    .close {
        .right(0);
    }
}
```

Additionally, you can use `@modrtl-dir` variable in `& when` statements:

```less
@import 'Swissup_Rtl::css/_modrtl.less';

& when (@modrtl-dir = rtl) {
    .my-element {
        right: auto;
        left: 20px;
    }
}
```

**Mixins list**

Mixin                                       | Example
:-------------------------------------------|:----------------------------------
**Misc**                                    |
.modrtl(@property, @ltrValue, @rtlValue)    | .modrtl(display, block, inline)
.direction()                                | .direction() // will output current direction
.direction(@value)                          | .direction(rtl)
.background-position(@ltrValue, @rtlValue)  | .background-position(100% 50%, 0 50%)
.text-align(@direction)                     | .text-align(left)
**Padding**                                 |
.padding(@value)                            | .padding(10px 25px 10px 5px)
.padding-left(@value)                       | .padding-left(5px)
.padding-right(@value)                      | .padding-right(25px)
**Margin**                                  |
.margin(@value)                             | .margin(10px 25px 10px 5px)
.margin-left(@value)                        | .margin-left(5px)
.margin-right(@value)                       | .margin-right(25px)
**Positioning**                             |
.float(@direction)                          | .float(left)
.clear(@direction)                          | .clear(left)
.left(@distance)                            | .left(20px)
.right(@distance)                           | .right(20px)
**Border**                                  |
.border-radius(@value)                      | .border-radius(5px 0 0 5px)
.border-[top\|right\|bottom\|left]-radius(@radius)  | .border-top-radius(5px)
.border-[top\|bottom]-[left\|right]-radius(@radius) | .border-top-left-radius(5px)
.border-left(@border-style)                 | .border-left(1px solid #f4f4f4);
.border-right(@border-style)                | .border-right(1px solid #f4f4f4);
.border-color(@value)                       | .border-color(#f4f4f4 transparent #eee #f4f4f4)
.border-left-color(@color)                  | .border-left-color(#f4f4f4)
.border-right-color(@color)                 | .border-right-color(transparent)
.border-style(@value)                       | .border-style(dotted dashed none solid)
.border-left-style(@style)                  | .border-left-style(solid)
.border-right-style(@style)                 | .border-right-style(none)
.border-width(@value)                       | .border-width(1px 0 1px 2px)
.border-left-width(@width)                  | .border-left-width(0)
.border-right-width(@width)                 | .border-right-width(2px)

### PHP helper

```php
class Example
{
    public function __construct(\Swissup\Rtl\Helper\Data $rtlHelper)
    {
        // Check current locale
        $rtlHelper->isRtl();

        // Check locale code
        $rtlHelper->isRtl('en_US');
    }
}
```

## License

Distributed under the [MIT license](http://opensource.org/licenses/MIT)

## Credits

Mixins are taken from [anasnakawa/bi-app-sass](https://github.com/anasnakawa/bi-app-sass).
Thanks!
