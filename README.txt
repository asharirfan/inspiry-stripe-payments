# Inspiry Stripe Payments
Contributors: inspirythemes, mrasharirfan, saqibsarwar
Tags: stripe, inspiry, payments, checkout
Requires at least: 4.7
Tested up to: 4.7.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A lightweight plugin to add stripe payments support to real estate themes by Inspiry Themes.

== Description ==

This plugin can also be used with any other WordPress website to add stripe payments using easy to use shortcodes.

For shortcode details, please consult the documentation below.

### Features

* Add Stripe payment support for real estate themes by [Inspiry Themes](https://inspirythemes.com)
* Easily add Stripe payment button in your posts or pages using simple shortcode.

### Documentation

* Display a simple stripe checkout button.
    `[isp_button]`

* To display description at the top of the checkout form.
    `[isp_button desc="This is a simple description."]`

* To pay custom amount with stripe.
    `[isp_button amount="20"]`

* To change the default currency code i.e. USD.
    `[isp_button currency="GBP"]`

* To set the email of the customer.
    `[isp_button email="ashar@inspirythemes.com"]`

* To change the label of the stripe checkout button.
    `[isp_button label="Pay with Credit Card"]`

* To turn on the `remember-user` feature of stripe.
    `[isp_button remember_user="true"]`

* To turn on the `billing address` feature of stripe.
    `[isp_button billing="true"]`

* To turn on the `shipping address` feature of stripe.
    `[isp_button shipping="true"]`

* To turn on the `alipay` feature of stripe.
    `[isp_button alipay="true"]`

* To turn on the `bitcoin` feature of stripe.
    `[isp_button bitcoin="true"]`

### Links

- [GitHub Repository](https://github.com/InspiryThemes/inspiry-stripe-payments)

== Installation ==

1. Unzip the downloaded package
1. Upload `inspiry-stripe-payments` to the `/wp-content/plugins/` directory
1. Activate the `Inspiry Stripe Payments` through the 'Plugins' menu in WordPress

== Changelog ==

= 1.0.0 =
* Initial Release

