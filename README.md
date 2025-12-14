![Banner](.wordpress-org/banner-1544x500.png)

# BrevWoo [![Lint](https://github.com/AlecRust/brevwoo/actions/workflows/lint.yml/badge.svg)](https://github.com/AlecRust/brevwoo/actions/workflows/lint.yml)

> WordPress plugin that adds WooCommerce customers to Brevo lists.

Set default Brevo lists that customers are added to when they buy a product, and/or set lists for specific products.

## Features

- Add customers to specific Brevo lists based on products they buy
- Add customers to default Brevo lists when they buy any product
- Select when during checkout the customer is added to Brevo
- Contacts created in Brevo include [transactional attributes](https://help.brevo.com/hc/en-us/articles/10635646979218-Create-and-manage-transactional-attributes)
- Optional logging included to help debug any issues

## Installation

Install from the [WordPress Plugin Directory](https://wordpress.org/plugins/brevwoo/) or grab a ZIP from
[Releases](https://github.com/AlecRust/brevwoo/releases).

Once activated, add your [Brevo API key](https://developers.brevo.com/docs/getting-started#quick-start) at
**Settings > BrevWoo**.

### Requirements

- WordPress 6.4+
- PHP 8.0+
- WooCommerce 5.5+

## Development

Ensure you have the following installed:

- [Node.js](https://nodejs.org/)
- [Composer](https://getcomposer.org/)
- [Docker](https://www.docker.com/)

Start a WordPress instance for developing this plugin:

1. `npm install`
2. `composer install`
3. `npm run env start`

Login at `http://localhost:8080/wp-admin` with username `admin` and password `password`.
