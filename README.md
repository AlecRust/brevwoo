![Banner](.wordpress-org/banner-1544x500.png)

# BrevWoo [![Lint](https://github.com/AlecRust/brevwoo/actions/workflows/lint.yml/badge.svg)](https://github.com/AlecRust/brevwoo/actions/workflows/lint.yml)

Adds a panel to the WooCommerce edit product page where you can select from your Brevo lists.

When a customer completes purchase of that product, they will be added to the selected Brevo lists.

## Features

-   Simpler and more reliable at adding customer to lists than the JavaScript Brevo Tracker
-   Creates (or updates) a Brevo contact using the customer's email address and first/last name
-   Adds [transactional attributes](https://help.brevo.com/hc/en-us/articles/10635646979218-Create-and-manage-transactional-attributes) to the Brevo contact including the order ID and price
-   Uses the [official PHP client](https://github.com/getbrevo/brevo-php) to interact with the Brevo API

## Installation

Install from the [WordPress Plugin Directory](https://wordpress.org/plugins/brevwoo/) or grab a ZIP from
[Releases](https://github.com/AlecRust/brevwoo/releases).

Once activated, add your [Brevo API key](https://developers.brevo.com/docs/getting-started#quick-start) at **Settings > BrevWoo** then edit a product to select your Brevo lists.

## Development

### Requirements

-   [Node.js](https://nodejs.org/)
-   [Composer](https://getcomposer.org/)
-   [Docker](https://www.docker.com/)

Launch a Docker-based development environment with this plugin pre-installed by running:

1. `npm install`
2. `composer install`
3. `npm run env start`
