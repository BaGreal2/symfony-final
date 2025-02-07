<?php
namespace App\Command;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'app:add-product',
    description: 'Creates a new product.',
    hidden: false,
)]
class AddProductCommand extends Command
{
    private $entityManager;
    protected static $defaultName = 'app:add-product';

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Adds a product to the database.')
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'Product name')
            ->addOption('price', null, InputOption::VALUE_REQUIRED, 'Product price')
            ->addOption('created-at', null, InputOption::VALUE_OPTIONAL, 'Product creation date (YYYY-MM-DD)', (new \DateTime())->format('Y-m-d'));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getOption('name');
        $price = $input->getOption('price');
        $createdAt = new \DateTime($input->getOption('created-at'));

        // Create the new product
        $product = new Product();
        $product->setName($name);
        $product->setPrice($price);
        $product->setCreatedAt($createdAt);

        // Persist and flush the product entity
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $output->writeln('<info>Product added successfully!</info>');

        return Command::SUCCESS;
    }
}

