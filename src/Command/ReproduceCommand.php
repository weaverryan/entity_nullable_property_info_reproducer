<?php

namespace App\Command;

use App\Entity\Product;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AsCommand(name: 'app:reproduce', description: 'Reproduce the issue')]
class ReproduceCommand extends Command
{
    public function __construct(
        private PropertyTypeExtractorInterface $typeExtractor,
        private NormalizerInterface $normalizer,
        private DenormalizerInterface $denormalizer
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // will return a Type[] with a single Type that has nullable: false
        dump($this->typeExtractor->getTypes(Product::class, 'id'));

        $product = new Product();
        $data = $this->normalizer->normalize($product);
        // ['id' => null]
        dump($data);

        // This will produce a "NotNormalizableValueException":
        //      > The type of the "id" attribute for class "App\Entity\Product" must be one of "int" ("null" given)
        // The error can be "fixed" by adding `nullable: true` option to the `Product.id` `ORM\Column`
        $this->denormalizer->denormalize($data, Product::class);

        return self::SUCCESS;
    }
}
