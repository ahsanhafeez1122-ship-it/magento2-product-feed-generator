<?php

namespace Ahsan\ProductFeed\Cron;

use Ahsan\ProductFeed\Model\Config;
use Ahsan\ProductFeed\Model\FeedGenerator;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class GenerateFeed
{
    private FeedGenerator $feedGenerator;
    private Config $config;
    private LoggerInterface $logger;

    public function __construct(FeedGenerator $feedGenerator, Config $config, LoggerInterface $logger)
    {
        $this->feedGenerator = $feedGenerator;
        $this->config = $config;
        $this->logger = $logger;
    }

    public function execute(): void
    {
        if (!$this->config->isEnabled()) {
            return;
        }

        try {
            $this->feedGenerator->generate();
        } catch (LocalizedException $e) {
            $this->logger->error('Ahsan_ProductFeed cron failed: ' . $e->getMessage());
        }
    }
}
