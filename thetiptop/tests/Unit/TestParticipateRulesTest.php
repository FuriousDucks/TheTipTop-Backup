<?php

namespace App\Tests\Unit;

use App\Controller\ParticipateController;
use App\Repository\WinnerRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TestParticipateRulesTest extends KernelTestCase
{
    private $winnerRepository;

    private $maxWinners;

    public function testRulesFunction(): void
    {
        $this->winnerRepository = $this->createMock(WinnerRepository::class);
        $this->maxWinners = 1500000;
        self::bootKernel();

        $participateController = new ParticipateController();

        $response = $participateController->rules($this->winnerRepository, $this->maxWinners);

        $this->assertIsInt($response);
        $this->assertGreaterThanOrEqual(1, $response);
        $this->assertLessThanOrEqual(5, $response);
    }
}
