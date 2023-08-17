<?php

namespace App\Tests\Entity;

use App\Entity\Wish;
use PHPUnit\Framework\TestCase;

class WishTest extends TestCase
{
    public function testSomething(): void
    {
        $this->assertTrue(true);
    }

    public function testGetterSetters() {
        $wish = new Wish();
        $wish->setTitle('Nouveau wish');

        $this->assertEquals('Nouveau wish', $wish->getTitle());
    }

    // Tester si un wish isPublished = true par default
    public function testPublication() {
        $wish = new Wish();
        $this->assertTrue($wish->isIsPublished());
    }
}
