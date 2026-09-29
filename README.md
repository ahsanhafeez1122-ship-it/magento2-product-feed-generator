# Ahsan_ProductFeed — Magento 2 Product Feed Generator

A Magento 2 module that exports a product feed (CSV or XML) with **zero hard-coded values** — every part of the feed (which attributes to export, which categories to include, the output format, the filename) is controlled from **Stores → Configuration → Product Feed Generator** in the admin panel.

## Why admin-driven, not hard-coded

Marketplace/comparison-shopping feeds change requirements often — a new attribute needs to go in, a category needs to be excluded, the client wants XML instead of CSV. If those choices are hard-coded in PHP, every change means a code deploy. This module treats them as configuration: a merchant/admin can change what the feed contains without touching code.

## How it works

```
Admin Configuration (system.xml)
        │
        ▼
   Model/Config.php  ── single source of truth for every setting
        │
        ▼
Model/FeedGenerator.php
        │  ├─ reads selected attributes + categories
        │  ├─ loads the product collection accordingly
        │  └─ delegates writing to the configured format writer
        ▼
Model/Writer/{CsvWriter,XmlWriter}.php
        │
        ▼
   var/export/<filename>.<format>
```

The feed can be triggered two ways:
- **CLI:** `bin/magento ahsan:productfeed:generate`
- **Cron:** runs automatically at 2 AM daily (`etc/crontab.xml`)

## Key Magento concepts demonstrated

- **`system.xml` / `config.xml`** — admin-configurable settings, including a `source_model` that populates the attribute dropdown dynamically from the real EAV attribute set (not a hard-coded list)
- **Dependency Injection (`di.xml`)** — the set of format writers (CSV, XML) is injected into `FeedGenerator` as an array, so adding a new format (e.g. JSON) means adding one writer class and one `di.xml` line — `FeedGenerator` itself doesn't change
- **`acl.xml`** — restricts who can see/edit this configuration section in the admin
- **Console command** — a proper `bin/magento` CLI command extending Symfony's `Command`
- **Cron job** — scheduled, admin-toggleable background execution
- **EAV-aware product collection** — loads only the attributes actually needed (`addAttributeToSelect`), filters by category and status

## Installation (into an existing Magento 2 instance)

```bash
# Copy (or composer-require) this module into:
app/code/Ahsan/ProductFeed/

php bin/magento module:enable Ahsan_ProductFeed
php bin/magento setup:upgrade
php bin/magento cache:flush
```

Then configure it at **Stores → Configuration → Catalog → Product Feed Generator**, and generate a feed with:

```bash
php bin/magento ahsan:productfeed:generate
```

## Note on testing

This module was written and syntax-validated standalone (`php -l` on every class, well-formed-XML checks on every config file). It targets Magento 2's standard module architecture and coding conventions but has not been run inside a live Magento instance in this repository — installing it requires Magento Marketplace `composer` auth keys and a full Magento install, which this portfolio repo does not include.
