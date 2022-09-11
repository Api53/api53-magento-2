
<h1 align="center">
  <br>
  <a href="https://www.api53.com"><img src="https://www.api53.com/static/images/logo-all-long-400-100-color.png" alt="Api53.com" width="400"></a>
</h1>


<h4 align="center">Api53 provides Headless solutions for eCommerce.</h4>

<h4 align="center"><a href="https://wwww.api53.com">www.api53.com</a></h4>

<p align="center">
<img src="http://poser.pugx.org/api53/api53-magento-2/v" alt="Latest Stable Version">
<img src="http://poser.pugx.org/api53/api53-magento-2/license" alt="Licensee">
<img src="http://poser.pugx.org/api53/api53-magento-2/require/php" alt="PHP Version Require">
</p>

<h4 align="center">Works with:</h4>

<p align="center">
<img src="https://shields.io/badge/Magento%202-2.3.3-f46f25?logo=magento&style=flat-square" alt="magento 2 v2.3.3">
<img src="https://shields.io/badge/Magento%202-2.4.4-f46f25?logo=magento&style=flat-square" alt="magento 2 v2.4.4">
</p>

<hr>

<p align="center">
  <a href="#key-features">Key Features</a> •
  <a href="#getting-started">Getting Started / Installation</a> •
  <a href="#documentation">Documentation</a> •
  <a href="#support">Support</a> •
  <a href="#license">License</a>
</p>

![screenshot](https://github.com/Api53/api53-magento-2/blob/main/assets/api53-configuration-magento-admin.jpg)

## Key Features

* Yes, we make your eCommerce shop headless through the API we offer
* Decoupling the product catalog from your magento shop
* Creation of a dedicated API endpoint over Api53.com
* Near real-time synchronization of the entire catalog
* Multi tenant user control for Headless API
* ...and many more

## Getting Started / Installation

To be able to install this Magento module, you need SSH access to your server. <br>

### Installation via Composer

You can install our module via [composer](https://getcomposer.org/). You can find the Api53 composer package at the following address: https://packagist.org/packages/api53/api53-magento-2

After successfully login to your Magento server, please go to the root magento directory and run the following commands:
```bash
# Install Api53 Module by using composer
$ composer require api53/api53-magento-2

# Enable Api53 module
$ php bin/magento module:enable Api53_Api53

# Run setup:upgrade
$ php bin/magento setup:upgrade

# Complete the setup
$ php bin/magento setup:di:compile

# Deploy static content
$ php bin/magento setup:static-content:deploy

# Clean cache
$ php bin/magento cache:clean
```

After successful installation you can check whether the module has been activated:
```bash
$ php bin/magento module:status
```

### Manual installation

After successfully login to your Magento server, please go to the root magento directory and run the following commands:

```bash
# Download the latest Api53 Magento extension release from GitHub
$ wget -O api53.tar.gz https://github.com/Api53/api53-magento-2/tarball/main

# Create Api53 directory inside app/code
$ mkdir -p app/code/Api53/Api53

# Unpack the Api53 extension 
$ tar -xf api53.tar.gz -C app/code/Api53/Api53/ --strip-components=1

# Enable Api53 module
$ php bin/magento module:enable Api53_Api53

# Run setup:upgrade
$ php bin/magento setup:upgrade

# Complete the setup
$ php bin/magento setup:di:compile

# Deploy static content
$ php bin/magento setup:static-content:deploy

# Clean cache
$ php bin/magento cache:clean
```


### Enable Api53 Magento extension

Api53 module was successfully installed. You can enable it in `Stores` -> `Settings` -> `Configuration` -> `Api53`


<br>

**Please Note:**<br>
This Magento module use cron to init your catalog with Api53. Make sure your Magento cron is running propertly.

To use the module you need a valid Api53 Shop API key. To get a key you have to create a [new Api53 account](https://www.api53.com/signup/) (Free sign up - No credit card required). You can read more details in our documentation [here](https://www.api53.com/documentation/api53-setup-guide/).

The initial catalog sync process may take some time. **Up to 15 minutes**. It all depends on how many products are synced with Api53.


## Documentation

We also have an internal wiki how you can use our Api solution:
- [Setup Guide](https://www.api53.com/documentation/api53-setup-guide/)
- [Install Magento 2 module](https://www.api53.com/documentation/install-api53-app-or-extension/#magento2)
- [How to use Api53 CSV API](https://www.api53.com/documentation/using-csv-apis/)


## Support

This magento module is fully supported. That means if you have any questions or suggestions please contact us via email support@api53.com or Twitter [@api53com](https://twitter.com/api53com).


## License

Apache-2.0 license

---

> [api53.com](https://www.api53.com) &nbsp;&middot;&nbsp;
> Twitter [@api53com](https://twitter.com/api53com)

