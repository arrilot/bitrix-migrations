<?php

namespace Arrilot\BitrixMigrations;

use Arrilot\BitrixIblockHelper\HLBlock;
use Arrilot\BitrixIblockHelper\IblockId;
use Arrilot\BitrixMigrations\Interfaces\DatabaseStorageInterface;
use Arrilot\BitrixMigrations\Interfaces\FileStorageInterface;
use Arrilot\BitrixMigrations\Interfaces\MigrationInterface;
use Arrilot\BitrixMigrations\Storages\BitrixDatabaseStorage;
use Arrilot\BitrixMigrations\Storages\FileStorage;
use Exception;

class Migrator
{
    /**
     * Migrator configuration array.
     *
     * @var array
     */
    protected $config;

    /**
     * Directory to store m.
     *
     * @var string
     */
    protected $dir;

    /**
     * Files interactions.
     *
     * @var FileStorageInterface
     */
    protected $files;

    /**
     * Interface that gives us access to the database.
     *
     * @var DatabaseStorageInterface
     */
    protected $database;

    /**
     * TemplatesCollection instance.
     *
     * @var TemplatesCollection
     */
    protected $templates;

    /**
     * Constructor.
     *
     * @param array                    $config
     * @param TemplatesCollection      $templates
     * @param DatabaseStorageInterface $database
     * @param FileStorageInterface     $files
     */
    public function __construct($config, TemplatesCollection $templates, DatabaseStorageInterface $database = null, FileStorageInterface $files = null)
    {
        $this->config = $config;
        $this->dir = $config['dir'];

        $this->templates = $templates;
        $this->database = $database ?: new BitrixDatabaseStorage($config['table']);
        $this->files = $files ?: new FileStorage();
    }

    /**
     * Create migration file.
     *
     * @param string $name         - migration name
     * @param string $templateName
     * @param array  $replace      - array of placeholders that should be replaced with a given values.
     * @param string  $subDir
     *
     * @return string
     */
    public function createMigration($name, $templateName, array $replace = [], $subDir = '')
    {
        $targetDir = $this->dir;
        $subDir = trim(str_replace('\\', '/', $subDir), '/');
        if ($subDir) {
            $targetDir .= '/' . $subDir;
        }

        $this->files->createDirIfItDoesNotExist($targetDir);

        $fileName = $this->constructFileName($name);
        $className = $this->getMigrationClassNameByFileName($fileName);
        $templateName = $this->templates->selectTemplate($templateName);

        $template = $this->files->getContent($this->templates->getTemplatePath($templateName));
        $template = $this->replacePlaceholdersInTemplate($template, array_merge($replace, ['className' => $className]));

        $this->files->putContent($targetDir.'/'.$fileName.'.php', $template);

        return $fileName;
    }

    /**
     * Run all migrations that were not run before.
     */
    public function runMigrations()
    {
        $migrations = $this->getMigrationsToRun();
        $ran = [];

        if (empty($migrations)) {
            return $ran;
        }

        foreach ($migrations as $migration) {
            $this->runMigration($migration);
            $ran[] = $migration;
        }

        return $ran;
    }

    /**
     * Run a given migration.
     *
     * @param string $file
     *
     * @throws Exception
     *
     * @return string
     */
    public function runMigration($file)
    {
        $migration = $this->getMigrationObjectByFileName($file);

        $this->disableBitrixIblockHelperCache();

        if ($migration->up() === false) {
            throw new Exception("Migration up from {$file}.php returned false");
        }

        $this->logSuccessfulMigration($file);
    }

    /**
     * Log successful migration.
     *
     * @param string $migration
     *
     * @return void
     */
    public function logSuccessfulMigration($migration)
    {
        $this->database->logSuccessfulMigration($migration);
    }

    /**
     * Get ran migrations.
     *
     * @return array
     */
    public function getRanMigrations()
    {
        return $this->database->getRanMigrations();
    }

    /**
     * Determine whether migration file for migration exists.
     *
     * @param string $migration
     *
     * @return bool
     */
    public function doesMigrationFileExist($migration)
    {
        return $this->files->exists($this->getMigrationFilePath($migration));
    }

    /**
     * Rollback a given migration.
     *
     * @param string $file
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function rollbackMigration($file)
    {
        $migration = $this->getMigrationObjectByFileName($file);

        if ($migration->down() === false) {
            throw new Exception("<error>Can't rollback migration:</error> {$file}.php");
        }

        $this->removeSuccessfulMigrationFromLog($file);
    }

    /**
     * Remove a migration name from the database so it can be run again.
     *
     * @param string $file
     *
     * @return void
     */
    public function removeSuccessfulMigrationFromLog($file)
    {
        $this->database->removeSuccessfulMigrationFromLog($file);
    }

    /**
     * Delete migration file.
     *
     * @param string $migration
     *
     * @return bool
     */
    public function deleteMigrationFile($migration)
    {
        return $this->files->delete($this->getMigrationFilePath($migration));
    }

    /**
     * Get array of migrations that should be ran.
     *
     * @return array
     */
    public function getMigrationsToRun()
    {
        $allMigrations = $this->files->getMigrationFiles($this->dir);

        $ranMigrations = $this->getRanMigrations();

        return array_diff($allMigrations, $ranMigrations);
    }

    /**
     * Construct migration file name from migration name and current time.
     *
     * @param string $name
     *
     * @return string
     */
    protected function constructFileName($name)
    {
        list($usec, $sec) = explode(' ', microtime());

        $usec = substr($usec, 2, 6);

        return date('Y_m_d_His', $sec).'_'.$usec.'_'.$name;
    }

    /**
     * Get a migration class name by a migration file name.
     *
     * @param string $file
     *
     * @return string
     */
    protected function getMigrationClassNameByFileName($file)
    {
        $fileExploded = explode('_', $file);

        $datePart = implode('_', array_slice($fileExploded, 0, 5));
        $namePart = implode('_', array_slice($fileExploded, 5));

        return Helpers::studly($namePart.'_'.$datePart);
    }

    /**
     * Replace all placeholders in the stub.
     *
     * @param string $template
     * @param array  $replace
     *
     * @return string
     */
    protected function replacePlaceholdersInTemplate($template, array $replace)
    {
        foreach ($replace as $placeholder => $value) {
            $template = str_replace("__{$placeholder}__", $value, $template);
        }

        return $template;
    }

    /**
     * Resolve a migration instance from a file.
     *
     * @param string $file
     *
     * @throws Exception
     *
     * @return MigrationInterface
     */
    protected function getMigrationObjectByFileName($file)
    {
        $class = $this->getMigrationClassNameByFileName($file);

        $this->requireMigrationFile($file);

        $object = new $class();

        if (!$object instanceof MigrationInterface) {
            throw new Exception("Migration class {$class} must implement Arrilot\\BitrixMigrations\\Interfaces\\MigrationInterface");
        }

        return $object;
    }

    /**
     * Require migration file.
     *
     * @param string $file
     *
     * @return void
     */
    protected function requireMigrationFile($file)
    {
        $this->files->requireFile($this->getMigrationFilePath($file));
    }

    /**
     * Get path to a migration file.
     *
     * @param string $migration
     *
     * @return string
     */
    protected function getMigrationFilePath($migration)
    {
        $files = Helpers::rGlob("$this->dir/$migration.php");
        if (count($files) != 1) {
            throw new \Exception("Not found migration file");
        }

        return $files[0];
    }

    /**
     * If package arrilot/bitrix-iblock-helper is loaded then we should disable its caching to avoid problems.
     */
    private function disableBitrixIblockHelperCache()
    {
        if (class_exists('\\Arrilot\\BitrixIblockHelper\\IblockId')) {
            IblockId::setCacheTime(0);
            if (method_exists('\\Arrilot\\BitrixIblockHelper\\IblockId', 'flushLocalCache')) {
                IblockId::flushLocalCache();
            }
        }

        if (class_exists('\\Arrilot\\BitrixIblockHelper\\HLBlock')) {
            HLBlock::setCacheTime(0);
            if (method_exists('\\Arrilot\\BitrixIblockHelper\\HLBlock', 'flushLocalCache')) {
                HLBlock::flushLocalCache();
            }
        }
    }
}
