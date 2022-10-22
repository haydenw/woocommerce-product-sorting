# Improve WooCommerce Manual Admin Product Sorting Plugin

Based on [the original](https://github.com/woocommerce/woocommerce/blob/dd49f89e77acaaa097251fe0a5dd69320ded48c6/includes/class-wc-ajax.php#L1778) product sorting code from WooCommerce. 

For stores with a large number of products the manual drag/drop sorting is very slow. This plugin improves it but is not a perfect fix. Test case is a store with ~2000 products. Time to load the new ordering improved from ~35 seconds to 600ms - 3 seconds (depending on where in the list the selected product is).

Tested with WooCommerce 3.6.4 to 7.0.0.

### Installation

Refer to these [instructions](https://wordpress.org/support/article/managing-plugins/#manual-upload-via-wordpress-admin). Download the zip file by clicking on the 'Clone or download' button and selecting 'Download Zip'.

### Others with the same issue

- [Product sorting admin listing - very very slow](https://github.com/woocommerce/woocommerce/issues/25227)
- [WooCommerce product sorting VERY slow](https://wordpress.stackexchange.com/questions/332003/woocommerce-product-sorting-very-slow)

