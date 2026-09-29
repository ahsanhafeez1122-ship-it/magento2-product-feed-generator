<?php
/**
 * Registers the module with the Magento component registrar so the
 * framework knows this code exists and where to load it from.
 */

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'Ahsan_ProductFeed',
    __DIR__
);
