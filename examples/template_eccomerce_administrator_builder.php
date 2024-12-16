<?php


use Lemonade\EmailGenerator\DefaultBuilder;
use Lemonade\EmailGenerator\DefaultContainerBuilder;
use Lemonade\EmailGenerator\DTO\AddressData;

require __DIR__ . '/../vendor/autoload.php';

// Vytvoření kontejneru
$container = DefaultContainerBuilder::create();

// Vytvoření adresy
$addressService = $container->getAddressService();
$footerAddress = $addressService->getAddress(new AddressData([
    "addressCompanyId" => "CZ12345678",
    "addressCompanyVatId" => "CZ87654321",
    "addressCompanyName" => "Firma XYZ",
    "addressAlias" => "Sídlo",
    "addressName" => "Josef Novák",
    "addressStreet" => "Ulice 1234/56",
    "addressPostcode" => "12345",
    "addressCity" => "Praha",
    "addressCountry" => "CZ",
    "addressPhone" => "+420 123 456 789",
    "addressEmail" => "info@muj-eshop.com"
]));

// Použití DefaultBuilder pro sestavení e-mailu
$builder = new DefaultBuilder($container);

echo $builder
    ->addHeader("Password Reset")
    ->componentLostPassword("Your Website", "https://example.com/reset-password")
    ->addFooter($footerAddress)
    ->build();