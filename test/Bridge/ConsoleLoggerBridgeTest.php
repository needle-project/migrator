<?php
namespace NeedleProject\Migrator\Bridge;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleLoggerBridgeTest extends TestCase
{
    /**
     * Validate that each log will end up in Output
     */
    public function testForwardedLogLevels()
    {
        $outputMock = $this->createMock(OutputInterface::class);
        $outputMock->expects($this->exactly(9))
            ->method('writeln')
            ->willReturn(null);

        $buildBridge = new ConsoleLoggerBridge($outputMock);
        $buildBridge->emergency('Foo');
        $buildBridge->alert('Foo');
        $buildBridge->info('Foo');
        $buildBridge->debug('Foo');
        $buildBridge->notice('Foo');
        $buildBridge->error('Foo');
        $buildBridge->critical('Foo');
        $buildBridge->warning('Foo');
        $buildBridge->log(-1, "Foo");
    }

    /**
     * Validate that each log will end up in Output
     */
    public function testNonVerboseOutput()
    {
        $outputMock = $this->createMock(OutputInterface::class);
        $outputMock->expects($this->exactly(2))
            ->method('isVerbose')
            ->willReturn(false);

        $outputMock->expects($this->exactly(0))
            ->method('writeln')
            ->willReturn(null);

        $buildBridge = new ConsoleLoggerBridge($outputMock);
        $buildBridge->notice('Foo');
        $buildBridge->warning('Foo');
    }

    /**
     * Validate that each log will end up in Output
     */
    public function testNonVeryVerboseOutput()
    {
        $outputMock = $this->createMock(OutputInterface::class);
        $outputMock->expects($this->exactly(1))
            ->method('isVeryVerbose')
            ->willReturn(false);

        $outputMock->expects($this->exactly(0))
            ->method('writeln')
            ->willReturn(null);

        $buildBridge = new ConsoleLoggerBridge($outputMock);
        $buildBridge->debug('Foo');
    }
}
