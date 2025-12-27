<?php

declare (strict_types=1);
namespace OmniIcon\Core\Discovery;

use OmniIcon\Core\Container\Container;
use OmniIcon\Core\Database\Migration\MigrationDiscovery;
use OmniIcon\Core\Logger\DiscoveryLogger;
use OmniIcon\Core\Logger\LoggerService;
use OMNI_ICON;
use OmniIconDeps\Psr\Log\LoggerInterface;
use Throwable;
final class DiscoveryManager
{
    /** @var array<Discovery> */
    private array $discoveries = [];
    /** @var array<DiscoveryLocation> */
    private array $discoveryLocations = [];
    private ?\OmniIcon\Core\Discovery\DiscoveryCache $discoveryCache = null;
    private readonly LoggerInterface $logger;
    public function __construct(private readonly Container $container)
    {
        $this->logger = new DiscoveryLogger();
    }
    public function discover(): void
    {
        $this->initializeDiscoveryLocations();
        $this->initializeDiscoveries();
        $this->runDiscovery();
        $this->applyDiscoveries();
    }
    /**
     * @return array<Discovery>
     */
    public function getDiscoveries(): array
    {
        return $this->discoveries;
    }
    public function clear_cache(): void
    {
        if ($this->discoveryCache instanceof \OmniIcon\Core\Discovery\DiscoveryCache) {
            $this->discoveryCache->clear();
        }
    }
    private function initializeDiscoveryLocations(): void
    {
        $this->loadComposerLocations();
    }
    public function loadComposerLocations(): void
    {
        $composerFile = OMNI_ICON::DIR . 'composer.json';
        $composerContent = file_get_contents($composerFile);
        if ($composerContent === \false) {
            $this->logger->error('Failed to read composer.json', ['component' => 'DiscoveryManager', 'file' => $composerFile]);
            return;
        }
        $composerData = json_decode($composerContent, \true);
        if (!is_array($composerData)) {
            $this->logger->error('Invalid composer.json format', ['component' => 'DiscoveryManager', 'file' => $composerFile]);
            return;
        }
        if (isset($composerData['autoload']) && is_array($composerData['autoload']) && isset($composerData['autoload']['psr-4']) && is_array($composerData['autoload']['psr-4'])) {
            foreach ($composerData['autoload']['psr-4'] as $namespace => $path) {
                if (is_string($namespace) && is_string($path) && str_starts_with($namespace, 'OmniIcon\\')) {
                    $fullPath = OMNI_ICON::DIR . $path;
                    $this->discoveryLocations[] = new \OmniIcon\Core\Discovery\DiscoveryLocation($namespace, $fullPath);
                }
            }
        }
        if (defined('WP_DEBUG') && \WP_DEBUG && (isset($composerData['autoload-dev']) && is_array($composerData['autoload-dev']) && isset($composerData['autoload-dev']['psr-4']) && is_array($composerData['autoload-dev']['psr-4']))) {
            foreach ($composerData['autoload-dev']['psr-4'] as $namespace => $path) {
                if (is_string($namespace) && is_string($path) && str_starts_with($namespace, 'OmniIcon\Tests\\')) {
                    $fullPath = OMNI_ICON::DIR . $path;
                    $this->discoveryLocations[] = new \OmniIcon\Core\Discovery\DiscoveryLocation($namespace, $fullPath);
                }
            }
        }
    }
    private function initializeDiscoveries(): void
    {
        $this->discoveryCache = new \OmniIcon\Core\Discovery\DiscoveryCache($this->determineCacheStrategy());
        // Create LoggerService for discoveries that require it (no dependencies required)
        $loggerService = new LoggerService();
        $this->discoveries = [new \OmniIcon\Core\Discovery\ServiceDiscovery($this->container), new \OmniIcon\Core\Discovery\HookDiscovery($this->container), new \OmniIcon\Core\Discovery\CommandDiscovery($this->container, $loggerService), new MigrationDiscovery($this->container, $loggerService), new \OmniIcon\Core\Discovery\ControllerDiscovery($this->container)];
    }
    private function determineCacheStrategy(): \OmniIcon\Core\Discovery\DiscoveryCacheStrategy
    {
        if (defined('WP_DEBUG') && \WP_DEBUG) {
            return \OmniIcon\Core\Discovery\DiscoveryCacheStrategy::PARTIAL;
        }
        return \OmniIcon\Core\Discovery\DiscoveryCacheStrategy::FULL;
    }
    private function runDiscovery(): void
    {
        foreach ($this->discoveryLocations as $discoveryLocation) {
            // Try to restore composer cache
            $composerCached = $this->discoveryCache?->restore($discoveryLocation, 'composer');
            if ($composerCached !== null) {
                $this->restoreFromCache($discoveryLocation, $composerCached);
            } else {
                // Scan via composer classmap and cache results
                $this->scanViaComposerClassmap($discoveryLocation);
                $this->cacheLocation($discoveryLocation, 'composer');
            }
            // For directory scanning: in PARTIAL mode, always scan; in FULL mode, use cache
            $directoryCached = $this->discoveryCache?->restore($discoveryLocation, 'directory');
            if ($directoryCached !== null) {
                $this->restoreFromCache($discoveryLocation, $directoryCached);
            } else {
                // Always do directory scanning to catch classes not in composer classmap
                $this->scanViaDirectoryScanner($discoveryLocation);
                $this->cacheLocation($discoveryLocation, 'directory');
            }
        }
    }
    private function restoreFromCache(\OmniIcon\Core\Discovery\DiscoveryLocation $discoveryLocation, array $cached): void
    {
        foreach ($this->discoveries as $discovery) {
            $items = $cached[$discovery::class] ?? [];
            if (!empty($items)) {
                $discoveryItems = $discovery->getItems();
                foreach ($items as $item) {
                    $discoveryItems->add($discoveryLocation, $item);
                }
                $discovery->setItems($discoveryItems);
            }
        }
    }
    private function scanViaDirectoryScanner(\OmniIcon\Core\Discovery\DiscoveryLocation $discoveryLocation): void
    {
        $path = $discoveryLocation->path;
        if (!is_dir($path)) {
            return;
        }
        $directoryScanner = new \OmniIcon\Core\Discovery\DirectoryScanner($this->discoveries, $this->logger);
        $directoryScanner->scan($discoveryLocation, $path);
    }
    private function scanViaComposerClassmap(\OmniIcon\Core\Discovery\DiscoveryLocation $discoveryLocation): bool
    {
        $classmap = $this->getComposerClassmap();
        if ($classmap === []) {
            return \false;
        }
        $classes = [];
        foreach ($classmap as $className => $filePath) {
            if (str_starts_with($className, rtrim($discoveryLocation->namespace, '\\'))) {
                // skip if .discovery-skip file exists in the same directory
                if (file_exists(dirname($filePath) . '/.discovery-skip')) {
                    continue;
                }
                $classes[$className] = $filePath;
            }
        }
        if ($classes === []) {
            return \false;
        }
        foreach (array_keys($classes) as $className) {
            // Use class_exists with autoload=false since we already have the file path from classmap
            // This prevents fatal errors when a class file has missing dependencies
            if (!class_exists($className, \false)) {
                // Try to load the class file, but catch any errors
                try {
                    require_once $classes[$className];
                    if (!class_exists($className, \false)) {
                        continue;
                    }
                } catch (Throwable $e) {
                    $this->logger->debug('Skipped class due to load error', ['component' => 'DiscoveryManager', 'className' => $className, 'error' => $e->getMessage()]);
                    continue;
                }
            }
            try {
                $classReflector = new \OmniIcon\Core\Discovery\ClassReflector($className);
                foreach ($this->discoveries as $discovery) {
                    $discovery->discover($discoveryLocation, $classReflector);
                }
            } catch (Throwable $e) {
                $this->logger->error('Discovery error for class', ['component' => 'DiscoveryManager', 'className' => $className, 'exception' => $e]);
            }
        }
        return \true;
    }
    /**
     * @return array<class-string, string>
     */
    private function getComposerClassmap(): array
    {
        $classmapFile = OMNI_ICON::DIR . 'vendor/composer/autoload_classmap.php';
        if (file_exists($classmapFile)) {
            $classmap = include $classmapFile;
            if (is_array($classmap)) {
                /** @var array<class-string, string> $classmap */
                return $classmap;
            }
        }
        return [];
    }
    private function cacheLocation(\OmniIcon\Core\Discovery\DiscoveryLocation $discoveryLocation, string $source): void
    {
        if (!$this->discoveryCache || !$this->discoveryCache->isEnabled()) {
            return;
        }
        $this->discoveryCache->store($discoveryLocation, $this->discoveries, $source);
    }
    private function applyDiscoveries(): void
    {
        foreach ($this->discoveries as $discovery) {
            $discovery->apply();
        }
    }
}
