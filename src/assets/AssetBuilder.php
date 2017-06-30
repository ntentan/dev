<?php
namespace ntentan\dev\assets;

use ntentan\utils\Filesystem;

abstract class AssetBuilder
{
    protected $inputs;
  
    protected $isTemp = true;
    
    private $output;
    
    public function __construct() {
        $this->output = rand(0, 10000000);
    }
    
    public function __destruct() {
        $outputFile = $this->getOutputFile();
        if($this->isTemp && file_exists($outputFile)) {
            unlink($this->getOutputFile());
        }
    }

    /**
     * Set the input paths to the assets used by this builder.
     * 
     * @param array $inputs
     */
    public function setInputs($inputs) {
        $this->inputs = $inputs;
    }
    
    public function setOutput($output)
    {
        $this->output = $output;
        $this->isTemp = false;
        return $this;
    }
    
    /**
     * Checks if any of the input files have changed since last build.
     * 
     * @return boolean
     */
    public function hasChanges() {
        $outputFile = $this->getOutputFile();
        if(!file_exists($outputFile)){
            return true;
        }
        $outputModificationTime = filemtime($outputFile);
        foreach($this->inputs as $input) {
            $files = glob($input);
            foreach($files as $file){
                if($outputModificationTime < filemtime($file)) {
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
        if(!file_exists($directory)) {
            mkdir($directory, 0700, true);
        }
        return $file;
    }
    
    public function __toString() {
        $this->build();
        return $this->getOutputFile();
    }
    
    public function setAssetsDirectory($assetsDirectory)
    {
        $this->assetsDirectory = $assetsDirectory;
    }
    
    abstract public function build();
}