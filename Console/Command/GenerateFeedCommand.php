<?php

namespace Ahsan\ProductFeed\Console\Command;

use Ahsan\ProductFeed\Model\FeedGenerator;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateFeedCommand extends Command
{
    private FeedGenerator $feedGenerator;

    public function __construct(FeedGenerator $feedGenerator, ?string $name = null)
    {
        $this->feedGenerator = $feedGenerator;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('ahsan:productfeed:generate');
        $this->setDescription('Generate the product feed using the current admin configuration');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $filePath = $this->feedGenerator->generate();
            $output->writeln('<info>Feed generated: ' . $filePath . '</info>');

            return Command::SUCCESS;
        } catch (LocalizedException $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');

            return Command::FAILURE;
        }
    }
}
