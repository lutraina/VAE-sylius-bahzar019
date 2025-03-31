<?php

declare(strict_types=1);

namespace Tests\App\Sylius;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Taxonomy\Model\Taxon;

class TaxonTest extends TestCase
{
    public function testTaxonCreation(): void
    {
        $taxon = new Taxon();
        $taxon->setCurrentLocale('en_US');
        $taxon->setCode('CATEGORY_1');
        $taxon->setName('Category 1');

        $this->assertEquals('CATEGORY_1', $taxon->getCode());
        $this->assertEquals('Category 1', $taxon->getName());
    }

    public function testTaxonParentChildRelationship(): void
    {
        $parentTaxon = new Taxon();
        $parentTaxon->setCurrentLocale('en_US');
        $parentTaxon->setCode('PARENT_CATEGORY');
        $parentTaxon->setName('Parent Category');

        $childTaxon = new Taxon();
        $childTaxon->setCurrentLocale('en_US');
        $childTaxon->setCode('CHILD_CATEGORY');
        $childTaxon->setName('Child Category');

        $parentTaxon->addChild($childTaxon);

        $this->assertCount(1, $parentTaxon->getChildren());
        $this->assertEquals('CHILD_CATEGORY', $parentTaxon->getChildren()->first()->getCode());
    }
}
