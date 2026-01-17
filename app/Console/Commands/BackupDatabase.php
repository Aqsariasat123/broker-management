<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup {--keep=30 : Number of backups to keep}';

    protected $description = 'Create a database backup and store it securely.';

    public function handle(): int
    {
        $connection = config('database.default');
        $config = config("database.connections.{$connection}");

        if ($connection === 'sqlite') {
            return $this->backupSqlite($config);
        } elseif (in_array($connection, ['mysql', 'mariadb'])) {
            return $this->backupMysql($config);
        } else {
            $this->error("Database backup not supported for connection: {$connection}");
            return Command::FAILURE;
        }
    }

    private function backupSqlite(array $config): int
    {
        $databasePath = $config['database'] ?? database_path('database.sqlite');
        
        if (!file_exists($databasePath)) {
            $this->error("SQLite database file not found: {$databasePath}");
            return Command::FAILURE;
        }

        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $backupFileName = "sqlite_backup_{$timestamp}.sqlite";
        $backupPath = "{$backupDir}/{$backupFileName}";

        try {
            copy($databasePath, $backupPath);
            $this->info("SQLite backup created: {$backupFileName}");
            
            // Compress backup
            $this->compressBackup($backupPath);
            
            // Clean old backups
            $this->cleanOldBackups($backupDir, (int) $this->option('keep'));
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to create SQLite backup: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function backupMysql(array $config): int
    {
        $host = $config['host'] ?? '127.0.0.1';
        $port = $config['port'] ?? '3306';
        $database = $config['database'];
        $username = $config['username'];
        $password = $config['password'];

        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $backupFileName = "mysql_backup_{$database}_{$timestamp}.sql";
        $backupPath = "{$backupDir}/{$backupFileName}";

        // Build mysqldump command
        $command = sprintf(
            'mysqldump --host=%s --port=%s --user=%s --password=%s %s > %s',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($database),
            escapeshellarg($backupPath)
        );

        try {
            exec($command . ' 2>&1', $output, $returnVar);
            
            if ($returnVar !== 0) {
                $this->error("mysqldump failed: " . implode("\n", $output));
                return Command::FAILURE;
            }

            if (!file_exists($backupPath) || filesize($backupPath) === 0) {
                $this->error("Backup file was not created or is empty.");
                return Command::FAILURE;
            }

            $this->info("MySQL backup created: {$backupFileName} (" . $this->formatBytes(filesize($backupPath)) . ")");
            
            // Compress backup
            $this->compressBackup($backupPath);
            
            // Clean old backups
            $this->cleanOldBackups($backupDir, (int) $this->option('keep'));
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to create MySQL backup: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function compressBackup(string $backupPath): void
    {
        if (!extension_loaded('zlib')) {
            $this->warn("Zlib extension not available. Skipping compression.");
            return;
        }

        $compressedPath = $backupPath . '.gz';
        
        try {
            $data = file_get_contents($backupPath);
            $compressed = gzencode($data, 9);
            file_put_contents($compressedPath, $compressed);
            
            $originalSize = filesize($backupPath);
            $compressedSize = filesize($compressedPath);
            $savings = $originalSize - $compressedSize;
            
            // Remove original if compression was successful
            if ($compressedSize < $originalSize) {
                unlink($backupPath);
                $this->info("Backup compressed: " . $this->formatBytes($compressedSize) . 
                           " (saved " . $this->formatBytes($savings) . ")");
            } else {
                unlink($compressedPath);
            }
        } catch (\Exception $e) {
            $this->warn("Failed to compress backup: " . $e->getMessage());
        }
    }

    private function cleanOldBackups(string $backupDir, int $keep): void
    {
        $files = glob("{$backupDir}/*.{sql,sqlite,sql.gz,sqlite.gz}", GLOB_BRACE);
        
        if (count($files) <= $keep) {
            return;
        }

        // Sort by modification time (newest first)
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $filesToDelete = array_slice($files, $keep);
        $deleted = 0;

        foreach ($filesToDelete as $file) {
            if (unlink($file)) {
                $deleted++;
            }
        }

        if ($deleted > 0) {
            $this->info("Cleaned up {$deleted} old backup(s).");
        }
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
