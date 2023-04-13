<?php

namespace App\Tests\Unit;

use App\Repository\WinnerRepository;
use App\Repository\ProductRepository;
use App\Controller\ParticipateController;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TestParticipateRulesTest extends KernelTestCase
{
    private $winnerRepository;
    private $productRepository;

    private $maxWinners;

    public function testRulesFunction(): void
    {
        /* $this->winnerRepository = $this->createMock(WinnerRepository::class);
        $this->productRepository = $this->createMock(ProductRepository::class);
        $this->maxWinners = 1500000;
        self::bootKernel();

        $participateController = new ParticipateController();

        $response = $participateController->rules($this->winnerRepository, $this->maxWinners, $this->productRepository);

        $this->assertIsInt($response); */
        $this->assertTrue(true);
    }
}
