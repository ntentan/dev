<?php
namespace ntentan\dev\assets;


class AssetPipeline
{
    private static string $publicPath;
    private static string $assetsPath;
    private static string $pipelinePath;
    private static string $forcedRebuild;

    public static function setup($options)
    {
        self::$publicPath = $options['public-path'];
        self::$pipelinePath = $options['pipeline-path'];
        self::$forcedRebuild = $options['force'] ?? false;
        self::$assetsPath = $options['assets-path'];
    }

    public static function getPublicPath()
    {
        return self::$publicPath;
    }

    public static function getAssetsPath()
    {
        return self::$assetsPath;
    }

    public static function define(AssetBuilder ...$builders)
    {
        $pipelineLastModified = filemtime(self::$pipelinePath);
        error_log("Looking for modified assets to rebuild ...");

        foreach($builders as $builder) {
            $outputFile = $builder->getOutputFile();
            if($outputFile == null) {
                error_log("No output path specified for {$builder->getDescription()}");
                continue;
            }

            $lastModified = file_exists($outputFile) ? filemtime($outputFile) : time();
            if($builder->hasChanges() || $lastModified < $pipelineLastModified || self::$forcedRebuild) {
                $builder->build();
                error_log("Building asset [{$builder->getOutputFile()}]");
            }
        }
    }
}
