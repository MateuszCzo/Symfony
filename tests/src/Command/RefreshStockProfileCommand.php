<?php

namespace App\Command;

use App\Entity\Stock;
use App\Http\YahooFinanceApiClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:refresh-stock-profile',
    description: 'Add a short description for your command',
)]
class RefreshStockProfileCommand extends Command
{
    private $entityManager;
    private $yahooFinanceApiClient;

    public function __construct(EntityManagerInterface $entityManager, YahooFinanceApiClient $yahooFinanceApiClient)
    {
        $this->entityManager = $entityManager;
        $this->yahooFinanceApiClient = $yahooFinanceApiClient;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Retrives a stock profile from the Yahoo Finance API. Update the record in the DB.')
            ->addArgument('symbol', InputArgument::REQUIRED, 'Stock symbol e.g. AMZN for Amazon')
            ->addArgument('region', InputArgument::REQUIRED, 'The region of the company e.g. US for United States.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stockProfile = $this->yahooFinanceApiClient->fetchStockProfile($input->getArgument('symbol'), $input->getArgument('region'));

        if ($stockProfile['statusCode'] !== 200) {
            
        }

        $stock = $this->serializer->deserialize($stockProfile['content'], Stock::class, 'json');
        /*
        $stock->setSymbol($stockProfile->symbol);
        $stock->setShortName($stockProfile->shortName);
        $stock->setCurrency($stockProfile->currency);
        $stock->setExchangeName($stockProfile->exchangeName);
        $stock->setRegion($stockProfile->region);
        $stock->setPrice($stockProfile->price);
        $stock->setPreviousClose($stockProfile->previousClose);
        $priceChange = $stockProfile->price - $stockProfile->previousClose;
        $stock->setPriceChange($priceChange);
*/
        $this->entityManager->persist($stock);

        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
