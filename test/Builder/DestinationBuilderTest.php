<?php
namespace NeedleProject\Migrator\Builder;

use NeedleProject\Migrator\Component\MySQL\MySQLDestinationComponent;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class DestinationBuilderTest extends TestCase
{
    public function testBuildMySQL()
    {
        $mockLogger = $this->createMock(LoggerInterface::class);
        $builder = new DestinationBuilder($mockLogger);

        $component = $builder->createComponent(
            [
                'connection' => [
                    'table' => 'foo',
                    'database' => 'bar',
                    'hostname' => 'data',
                    'username' => 'abc',
                    'password' => 'fgh'
                ],
            ],
            []
        );
        $this->assertEquals(
            MySQLDestinationComponent::class,
            get_class($component)
        );
    }
}
