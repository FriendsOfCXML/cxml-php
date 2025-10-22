<?php

declare(strict_types=1);

namespace CXml\Jms;

use CXml\Model\Exception\CXmlModelNotFoundException;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use RuntimeException;
use SplFileInfo;
use Throwable;

class ModelClassMapping
{
    /**
     * @var array<string, class-string|string>
     */
    private static array $modelRegistry = [];

    public static bool $initialized = false;

    public static function fromDefaultModelPath(): self
    {
        return new self(
            __DIR__ . '/../Model',
        );
    }

    public function __construct(private readonly string $pathToModelFiles)
    {
    }

    public function register(string $shortName, string $className): void
    {
        if (isset(self::$modelRegistry[$shortName])) {
            throw new RuntimeException('Short name was already registered: ' . $shortName);
        }

        self::$modelRegistry[$shortName] = $className;
    }

    private function loadModelClasses(): void
    {
        $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->pathToModelFiles, FilesystemIterator::SKIP_DOTS));

        /** @var SplFileInfo $file */
        foreach ($rii as $file) {
            if ('php' !== $file->getExtension()) {
                continue;
            }

            $subNamespace = substr($file->getPath(), strlen($this->pathToModelFiles));
            $subNamespace = str_replace('/', '\\', $subNamespace);

            /** @var class-string $className */
            $className = 'CXml\Model' . $subNamespace . '\\' . $file->getBasename('.php');

            $class = new ReflectionClass($className);
            if ($class->isAbstract()) {
                continue;
            }

            if ($class->isInterface()) {
                continue;
            }

            if ($class->isTrait()) {
                continue;
            }

            if ($class->isAnonymous()) {
                continue;
            }

            if ($class->implementsInterface(Throwable::class)) {
                continue;
            }

            $shortName = $class->getShortName();

            $this->register($shortName, $className);
        }

        self::$initialized = true;
    }

    /**
     * @throws CXmlModelNotFoundException
     */
    public function findClassForSerializedName(string $serializedName): string
    {
        if (!self::$initialized) {
            $this->loadModelClasses();
        }

        return self::$modelRegistry[$serializedName] ?? throw new CXmlModelNotFoundException($serializedName);
    }
}
