# PagBank GraphQL Module for PWA Studio Extension

This module adds **GraphQL support** for the [PagBank PWA Studio Extension](https://github.com/GabrielFNLima/payment-method-pagbank-pwa-studio), allowing seamless integration of the PagBank payment method with **PWA Studio (Venia UI)**.

## Prerequisites

-   A Magento installation with **PWA Studio** configured.
-   The **[Official PagBank Module for Magento](https://github.com/pagseguro/payment-magento)** installed.
-   The **[PagBank PWA Studio Extension](https://github.com/GabrielFNLima/payment-method-pagbank-pwa-studio)** installed in your PWA Studio setup.

## Installation

To set up the PagBank GraphQL module in your Magento environment, follow these steps:

### Step 1: Install the Module

In your Magento root directory, install the PagBank GraphQL module via Composer:

```bash
composer require devgfnl/pagbank-magento-graph-ql
```

### Step 2: Run Magento Setup Commands

After installation, execute the following Magento CLI commands to finalize the module setup:

```bash
bin/magento module:enable Devgfnl_PagBankGraphQl
bin/magento setup:upgrade
bin/magento setup:di:compile
```

### Step 3: Clear Cache

Clear the Magento cache to ensure that the module is properly integrated and operational:

```bash
bin/magento cache:flush
```

If you encounter any issues or have questions about this module, please open an issue in the [GitHub repository](https://github.com/GabrielFNLima/payment-method-pagbank-magento-graphql/issues).
