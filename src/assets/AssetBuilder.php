<?php

namespace ntentan\dev\assets;

use Exception;

/**
 * Base class for all asset builders.
 * This class provides methods that allow asset builders to receive inputs, detect changes between built assets and 
 * sources, and write out built assets. 
 */
abstract class AssetBuilder
{

    /**
     * Input paths for the processor.
     * @var array
     */
    private array $inputs;

    /**
     * A flag that is set whenever an output is explicitly provided.
     * When an output is not provided, a temporary output file is used.
     * @var bool
     */
    private bool $isTemp = true;

    /**
     * A path to the output file.
     * @var string
     */
    private string $outputFile;

    /**
     * An array of options for the processor.
     * @var array
     */
    private array $options;

    /**
     * A registry of all supported builders.
     * @var array
     */
    private static array $registry;

    public static function register($name, $factory): void
    {
        self::$registry[$name] = $factory;
    }

    public static function create($name) : AssetBuilder
    {
        if (!isset(self::$registry[$name])) {
            throw new Exception("Unknown asset builder $name");
        }
        return self::$registry[$name]();
    }

    public function __destruct()
    {
        $outputFile = $this->getOutputFile();
        if ($this->isTemp && file_exists($outputFile)) {
            unlink($this->getOutputFile());
        }
    }

    /**
     * Set the input paths to the assets used by this builder.
     * 
     * @param array $inputs
     */
    public function setInputs(array $inputs): void
    {
        $this->inputs = $inputs;
    }

    public function getInputs(): array
    {
        return $this->inputs;
    }

    public function setOutputFile(string $output) : AssetBuilder
    {
        $this->outputFile = $output;
        $this->isTemp = false;
        return $this;
    }

    /**
     * Expand glob inputs that are passed through the asset pipeline.
     * 
     * @return array
     */
    protected function expandInputs() : array
    {
        $files = [];
        $inputs = $this->getInputs();
        foreach ($inputs as $input) {
            $resolvedFiles = array_map(fn($path) => realpath($path), glob($input));
            if(count($resolvedFiles)) {
                $files = array_merge($files, $resolvedFiles);
            } else {
                error_log("Cannot find input file [{$input}]");
            }
        }
        return $files;
    }

    /**
     * Checks if any of the input files have changed since last build.
     * 
     * For builders that have inputs piped from other builders, this method uses the inputs of the piped builders to 
     * recursively reach all input files required that are fed to the builder.
     * 
     * @param string $outputFile The output file against which inputs are compared for newness.
     * @return boolean
     */
    public function hasChanges(): bool
    {
        // Use the default output file of this builder if none was passed
        $outputFile = $this->getOutputFile();
        if (!file_exists($outputFile)) {
            return true;
        }
        $outputModificationTime = filemtime($outputFile);
        $inputs = $this->getInputs();
        foreach ($inputs as $input) {
            $files = glob($input);
            foreach ($files as $file) {
                if ($outputModificationTime < filemtime($file)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getOutputFile(): ?string
    {
        if (!isset($this->outputFile)) {
            return null;
        }
        
        $file = AssetPipeline::getAssetsDirectory() . "/{$this->outputFile}";
        $directory = dirname($file);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        return $file;
    }

    public function __toString(): string
    {
        $this->build();
        return $this->getOutputFile();
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    abstract public function build(): void;
    
    /**
     * A brief description of the builder to help developers identify problem points when debugging. 
     * The value returned by this method can be anything helpful, from the name of the builder, to the files being 
     * built, or some internal state of the builder.
     */
    abstract public function getDescription(): string;
}

