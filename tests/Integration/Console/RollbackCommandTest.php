<?php
declare(strict_types=1);

namespace ElasticMigrations\Tests\Integration\Console;

use ElasticMigrations\Console\RollbackCommand;
use ElasticMigrations\Migrator;
use ElasticMigrations\Tests\Integration\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * @covers \ElasticMigrations\Console\RollbackCommand
 */
final class RollbackCommandTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $migrator;
    /**
     * @var MigrateCommand
     */
    private $command;

    protected function setUp(): void
    {
        parent::setUp();

        $this->migrator = $this->createMock(Migrator::class);

        $this->command = new RollbackCommand($this->migrator);
        $this->command->setLaravel($this->app);
    }

    public function test_does_nothing_if_migrator_is_not_ready(): void
    {
        $this->migrator
            ->expects($this->once())
            ->method('isReady')
            ->willReturn(false);

        $this->migrator
            ->expects($this->never())
            ->method('rollbackOne');

        $this->migrator
            ->expects($this->never())
            ->method('rollbackLastBatch');

        $this->command->run(
            new ArrayInput(['--force' => true]),
            new NullOutput()
        );
    }

    public function test_rollbacks_one_migration_if_file_name_is_provided(): void
    {
        $this->migrator
            ->expects($this->once())
            ->method('isReady')
            ->willReturn(true);

        $this->migrator
            ->expects($this->once())
            ->method('rollbackOne')
            ->with('test_file_name');

        $this->command->run(
            new ArrayInput(['--force' => true, 'fileName' => 'test_file_name']),
            new NullOutput()
        );
    }

    public function test_rollbacks_last_batch_if_file_name_is_not_provided(): void
    {
        $this->migrator
            ->expects($this->once())
            ->method('isReady')
            ->willReturn(true);

        $this->migrator
            ->expects($this->once())
            ->method('rollbackLastBatch');

        $this->command->run(
            new ArrayInput(['--force' => true]),
            new NullOutput()
        );
    }
}
