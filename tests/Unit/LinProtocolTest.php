<?php

/*
 * Copyright (c) 2026 erik <erik@erik.xyz> — https://erik.xyz
 */

namespace Erikwang2013\IndustrialProtocols\Lin\Tests\Unit;

use Erikwang2013\IndustrialProtocols\Lin\LinConnector;
use Erikwang2013\IndustrialProtocols\Lin\LinProtocol;
use PHPUnit\Framework\TestCase;

class LinProtocolTest extends TestCase
{
    public function testProtocolMetadata(): void
    {
        $protocol = new LinProtocol();
        $this->assertSame('lin', $protocol->getName());
        $this->assertSame('1.1.1', $protocol->getVersion());
        $this->assertSame(0, $protocol->getDefaultPort());
        $this->assertSame(['master', 'slave'], $protocol->getSupportedVariants());
    }

    public function testCreateConnectorReturnsLinConnector(): void
    {
        $connector = (new LinProtocol())->createConnector([
            'device' => '/dev/ttyUSB0',
            'baud_rate' => 19200,
            'timeout' => 1000,
        ]);
        $this->assertInstanceOf(LinConnector::class, $connector);
        $this->assertFalse($connector->isConnected());
    }

    public function testCreateConnectorWithEmptyConfig(): void
    {
        $connector = (new LinProtocol())->createConnector([]);
        $this->assertInstanceOf(LinConnector::class, $connector);
    }

    public function testConnectorHealthBeforeConnect(): void
    {
        $connector = (new LinProtocol())->createConnector([]);
        $this->assertSame(\Erikwang2013\IndustrialProtocols\Connection\ConnectionState::CLOSED, $connector->getHealth()->state);
    }

    public function testConnectorConnectFailsOnMissingDevice(): void
    {
        $connector = (new LinProtocol())->createConnector([
            'device' => '/nonexistent/ttyUSB99',
            'timeout' => 100,
        ]);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cannot open LIN serial device');
        $connector->connect();
    }
}
