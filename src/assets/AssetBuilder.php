<?php

namespace ntentan\dev\assets;

/**
 * Base class for all asset builders.
 * This class provides methods that allow asset builders to receive inputs;
 * detect changes between built assets and sources; and write out built assets. 
 */
abstract class AssetBuilder
{

    private array $inputs;
    private bool $isTemp = true;
    private string $outputFile;
    private array $options;

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
     * For builders that have inputs piped from other builders, this method
     * uses the inputs of the piped builders to recursively reach all input 
     * files required that are fed to the builder.
     * 
     * @param string $outputFile The output file against which inputs are compared for newness.
     * @return boolean
     */
    public function hasChanges(?string $outputFile = null): bool
    {

        // Use the default output file of this builder if none was passed
        $outputFile = $outputFile ?? $this->getOutputFile();
        if (!file_exists($outputFile)) {
            return true;
        }
        $outputModificationTime = filemtime($outputFile);
        $inputs = $this->getInputs();
        foreach ($inputs as $input) {
            $files = glob($input);
            foreach ($files as $file) {
                if (is_a($input, self::class)) {
                    // Recursively check for changes in piped assets.
                    if ($input->hasChanges($outputFile)) {
                        return true;
                    } else {
                        continue;
                    }
                } 
                if ($outputModificationTime < filemtime($file)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function setIsTemp(bool $isTemp): void
    {
        $this->isTemp = $isTemp;
    }

    public function getIsTemp(): bool
    {
        return $this->isTemp;
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

    public function setAssetsDirectory(string $assetsDirectory): void
    {
        $this->assetsDirectory = $assetsDirectory;
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
    
    abstract public function getDescription(): string;
}
