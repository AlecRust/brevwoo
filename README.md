![Banner](.wordpress-org/banner-1544x500.png)

# BrevWoo [![Lint](https://github.com/AlecRust/brevwoo/actions/workflows/lint.yml/badge.svg)](https://github.com/AlecRust/brevwoo/actions/workflows/lint.yml)

> WordPress plugin to integrate WooCommerce with Brevo

Allows connecting each WooCommerce product to any number of Brevo lists.

When a customer purchases the product, they are added to the selected lists.

## Features

-   Customer added to selected Brevo lists for a given product when purchased
-   Configuration of when during checkout the customer is added to Brevo
-   Customer name and email attributes included in the created Brevo contact
-   [Transactional attributes](https://help.brevo.com/hc/en-us/articles/10635646979218-Create-and-manage-transactional-attributes) included in the created Brevo contact

## Installation

Install from the [WordPress Plugin Directory](https://wordpress.org/plugins/brevwoo/) or grab a ZIP from
[Releases](https://github.com/AlecRust/brevwoo/releases).

Once activated, add your [Brevo API key](https://developers.brevo.com/docs/getting-started#quick-start) at
**Settings > BrevWoo** then edit a product to select some Brevo lists.

## Development

### Requirements

-   [Node.js](https://nodejs.org/)
-   [Composer](https://getcomposer.org/)
-   [Docker](https://www.docker.com/)

Start a WordPress instance for developing this plugin by running:

1. `npm install`
2. `composer install`
3. `npm run env start`
