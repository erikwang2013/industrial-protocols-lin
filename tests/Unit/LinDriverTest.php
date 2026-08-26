<?php

/*
 * Copyright (c) 2026 erik <erik@erik.xyz> — https://erik.xyz
 */

namespace Erikwang2013\IndustrialProtocols\Lin\Tests\Unit;

use Erikwang2013\IndustrialProtocols\Lin\Driver\LinDriver;
use Erikwang2013\IndustrialProtocols\Lin\Frame\LinFrame;
use PHPUnit\Framework\TestCase;

/**
 * LIN driver is a serial-UART driver (no TCP). A php://memory pseudo-device
 * opens like a real device, so the connect/lifecycle path is testable without
 * hardware; the send path is exercised to its no-response error.
 */
class LinDriverTest extends TestCase
{
    public function testConnectAndDisconnectWithPseudoDevice(): void
    {
        $driver = new LinDriver('php://memory', 19200, 0.1);
        $driver->connect();
        $this->assertTrue($driver->isConnected());
        $driver->disconnect();
        $this->assertFalse($driver->isConnected());
    }

    public function testSendWithoutResponseThrows(): void
    {
        $driver = new LinDriver('php://memory', 19200, 0.1);
        $driver->connect();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('No response from LIN bus');
        $driver->send(new LinFrame(0x10, [0x01]));
    }

    public function testSendRejectsNonLinFrame(): void
    {
        $driver = new LinDriver('php://memory', 19200, 0.1);
        $this->expectException(\InvalidArgumentException::class);
        $driver->send($this->createStub(\Erikwang2013\IndustrialProtocols\Protocol\FrameInterface::class));
    }

    public function testSendAsyncThrows(): void
    {
        $driver = new LinDriver('php://memory', 19200, 0.1);
        $this->expectException(\RuntimeException::class);
        $driver->sendAsync(new LinFrame(0x10));
    }

    public function testSupportsAsyncIsFalse(): void
    {
        $this->assertFalse((new LinDriver())->supportsAsync());
    }

    public function testConnectorLifecycleWithPseudoDevice(): void
    {
        $connector = new \Erikwang2013\IndustrialProtocols\Lin\LinConnector([
            'device' => 'php://memory',
            'timeout' => 100,
        ]);
        $connector->connect();
        $this->assertTrue($connector->isConnected());
        $this->assertSame(\Erikwang2013\IndustrialProtocols\Connection\ConnectionState::HEALTHY, $connector->getHealth()->state);

        $this->expectException(\RuntimeException::class);
        $connector->read('10'); // no bus response on a memory device
    }
}
