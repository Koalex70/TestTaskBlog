<?php

declare(strict_types=1);

namespace App\Database;

use App\Contracts\Database\MigrationInterface;
use App\Database\Sql\MigrationSqlOption;
use PDO;

final class MigrationManager
{
    private const MIGRATIONS_TABLE = 'schema_migrations';

    /** @var MigrationInterface[] */
    private array $migrations = [];

    public function __construct(private readonly PDO $pdo, array $migrations = [])
    {
        foreach ($migrations as $migration) {
            $this->register($migration);
        }
    }

    public function register(MigrationInterface $migration): void
    {
        $this->migrations[$migration->getVersion()] = $migration;
        ksort($this->migrations);
    }

    /**
     * @return string[] versions of applied migrations
     */
    public function migrate(): array
    {
        $this->ensureMigrationsTable();

        $applied = [];
        $executedVersions = $this->getExecutedVersions();

        foreach ($this->migrations as $version => $migration) {
            if (isset($executedVersions[$version])) {
                continue;
            }

            $migration->up($this->pdo);
            $stmt = $this->pdo->prepare(
                sprintf(
                    'INSERT INTO %s (version, executed_at) VALUES (:version, NOW())',
                    self::MIGRATIONS_TABLE
                )
            );
            $stmt->execute(['version' => $version]);

            $applied[] = $version;
        }

        return $applied;
    }

    /**
     * @return string[]
     */
    public function pendingVersions(): array
    {
        $this->ensureMigrationsTable();
        $executedVersions = $this->getExecutedVersions();

        $pending = [];
        foreach ($this->migrations as $version => $migration) {
            if (!isset($executedVersions[$version])) {
                $pending[] = $version;
            }
        }

        return $pending;
    }

    public function ensureMigrationsTable(): void
    {
        $this->pdo->exec(
            sprintf(
                'CREATE TABLE IF NOT EXISTS %s (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                version VARCHAR(255) NOT NULL UNIQUE,
                executed_at DATETIME NOT NULL
            ) %s',
                self::MIGRATIONS_TABLE,
                MigrationSqlOption::tableOptions()
            )
        );
    }

    /**
     * @return array<string, true>
     */
    private function getExecutedVersions(): array
    {
        $rows = $this->pdo
            ->query(sprintf('SELECT version FROM %s', self::MIGRATIONS_TABLE))
            ->fetchAll();

        $versions = [];
        foreach ($rows as $row) {
            $versions[$row['version']] = true;
        }

        return $versions;
    }
}
