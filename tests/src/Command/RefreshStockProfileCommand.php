<?php

namespace App\Command;

use App\Entity\Stock;
use App\Http\FinanceApiClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Refresh Stock Profile Command 
 * - fatch data using Yahoo Finance Api Client
 * - save it to database
 */
#[AsCommand(
    name: 'app:refresh-stock-profile',
    description: 'Add a short description for your command',
)]
class RefreshStockProfileCommand extends Command
{
    private $entityManager;
    private $financeApiClient;
    private $serializer;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, 
                                FinanceApiClientInterface $financeApiClient,
                                SerializerInterface $serializer,
                                LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->financeApiClient = $financeApiClient;
        $this->serializer = $serializer;
        $this->logger = $logger;

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
        try {
            $stockProfile = $this->financeApiClient->fetchStockProfile($input->getArgument('symbol'), $input->getArgument('region'));

            if ($stockProfile->getStatusCode() !== 200) {
                $output->writeln($stockProfile->getContent());

                return Command::FAILURE;
            }

            $symbol = json_decode($stockProfile->getContent())->symbol ?? null;

            if ($stock = $this->entityManager->getRepository(Stock::class)->findOneBy(['symbol' => $symbol])) {
                $this->serializer->deserialize($stockProfile->getContent(), 
                Stock::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $stock]);
            } else {
                $stock = $this->serializer->deserialize($stockProfile->getContent(), Stock::class, 'json');
            }
            
            $this->entityManager->persist($stock);

            $this->entityManager->flush();

            $output->writeln($stock->getShortName() . ' has been saved / updated.');

            return Command::SUCCESS;
        } catch (Exception $exception) {
            $this->logger->warning(get_class($exception) . ': ' . $exception->getMessage() . ' in ' . $exception->getFile() . 
                ' on line ' . $exception->getLine() . ' using [symbol/region] ' . '[' . $input->getArgument('symbol') . '/' . $input->getArgument('region') . ']');
            return Command::FAILURE;
        }
    }
}
