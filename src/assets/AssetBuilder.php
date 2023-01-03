<?php

namespace ntentan\dev\assets;

/**
 * Base class for all asset builders.
 * This class provides methods that allow asset builders to receive inputs;
 * detect changes between built assets and sources; and write out built assets. 
 */
abstract class AssetBuilder
{

    private $inputs;
    private $isTemp = true;
    private $output;
    private $options;

    public function __construct()
    {
        $this->output = rand(0, 10000000);
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
    public function setInputs($inputs)
    {
        $this->inputs = $inputs;
    }

    public function getInputs()
    {
        return $this->inputs;
    }

    public function setOutput(string $output) : AssetBuilder
    {
        $this->output = $output;
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
    public function hasChanges($outputFile = null)
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

    public function setIsTemp($isTemp)
    {
        $this->isTemp = $isTemp;
    }

    public function getIsTemp()
    {
        return $this->isTemp;
    }

    public function getOutputFile()
    {
        $file = AssetPipeline::getAssetsDirectory() . "/{$this->output}";
        $directory = dirname($file);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        return $file;
    }

    public function __toString()
    {
        $this->build();
        return $this->getOutputFile();
    }

    public function setAssetsDirectory($assetsDirectory)
    {
        $this->assetsDirectory = $assetsDirectory;
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }

    public function getOptions($options)
    {
        return $this->options;
    }

    abstract public function build();
}
