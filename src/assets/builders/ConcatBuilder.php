<?php

namespace ntentan\dev\assets\builders;

use ntentan\dev\assets\AssetBuilder;

class ConcatBuilder extends AssetBuilder {

    public function build(): void {
        $output = "";
        $files = $this->expandInputs();
        foreach ($files as $file) {
            $output .= "\n" . file_get_contents($file);
        }
        file_put_contents($this->getOutputFile(), $output);
    }

    public function getDescription(): string {
        return "concatenator";
    }
}
