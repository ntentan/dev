<?php
namespace ntentan\dev\assets;

abstract class AssetBuilder
{
    protected $inputs;
    
    /**
     * Set the input paths to the assets used by this builder.
     * 
     * @param array $inputs
     */
    public function setInputs($inputs) {
        $this->inputs = $inputs;
        foreach($inputs as $file) {
            Filesystem::checkExists($file);
        }
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
            if($outputModificationTime < filemtime($input)) {
                return true;
            }
        }
        return false;
    }
    
    abstract public function getOutputFile();
    
    abstract public function build();
}