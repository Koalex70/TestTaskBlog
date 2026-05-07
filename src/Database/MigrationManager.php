<?php

declare(strict_types=1);

namespace App\Database;

use App\Config\Config;
use App\Database\Migration\MigrationInterface;
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

            $this->pdo->beginTransaction();
            try {
                $migration->up($this->pdo);
                $stmt = $this->pdo->prepare(
                    sprintf(
                        'INSERT INTO %s (version, executed_at) VALUES (:version, NOW())',
                        self::MIGRATIONS_TABLE
                    )
                );
                $stmt->execute(['version' => $version]);
                $this->pdo->commit();
            } catch (\Throwable $e) {
                $this->pdo->rollBack();
                throw $e;
            }

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
        $databaseConfig = Config::namespace('database');
        $charset = $databaseConfig['charset'] ?? 'utf8mb4';
        $collation = $databaseConfig['collation'] ?? 'utf8mb4_unicode_ci';

        $this->pdo->exec(
            sprintf(
                'CREATE TABLE IF NOT EXISTS %s (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                version VARCHAR(255) NOT NULL UNIQUE,
                executed_at DATETIME NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=%s COLLATE=%s',
                self::MIGRATIONS_TABLE,
                $charset,
                $collation
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
