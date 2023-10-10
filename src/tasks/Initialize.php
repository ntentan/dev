<?php


namespace ntentan\dev\tasks;


use clearice\io\Io;
use ntentan\honam\Templates;
use ntentan\utils\Filesystem;

class Initialize
{
    private $options;
    private $devDirectory = __DIR__ . "/../..";
    private $templates;
    private $io;

    public function __construct(Templates $templates, Io $io = null)
    {
        $this->templates = $templates;
        $this->io = $io;
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }

    private function writeTemplateFile($template, $data)
    {
        file_put_contents($template, $this->templates->render("$this->devDirectory/code_templates/app/$template.mustache", $data));
    }

    private function runPreflightChecks()
    {
        $results = [];

        // Check if home directory is writeable
        $results["home_writeable"] = is_writable(".");

        // Check if any directories already exist.
        $results["no_public_directory"] = !file_exists("public");
        $results["no_config_directory"] = !file_exists("config");
        $results["no_src_directory"] = !file_exists("src");
        $results["no_view_directory"] = !file_exists("views");

        return [
            "success" => array_reduce($results, fn ($x, $y) => $x && $y, true), 
            "results" => $results
        ];
    }

    public function run()
    {
        $preFlight = $this->runPreflightChecks();

        if($preFlight["success"]) {
            $namespace = $this->options['namespace'] ?? 'app';
            $name = $this->options['name'] ?? addslashes('An ntentan application');
            $this->io->output("Copying skeleton application\n");
            Filesystem::get("$this->devDirectory/code_templates/app/views")->copyTo("./views");
            Filesystem::directory("views/shared")->create();
            Filesystem::get("$this->devDirectory/code_templates/app/bootstrap")->copyTo("./bootstrap");
            $this->io->output("Creating public directory\n");
            Filesystem::directory("public")->create();
            Filesystem::directory("config")->create();
            Filesystem::directory("src/controllers")->create(true);
            Filesystem::directory("src/models")->create(true);
            $data = ['name' => $name, 'namespace' => $namespace];
            $this->writeTemplateFile('public/index.php', $data);
            $this->writeTemplateFile('config/app.conf.php', $data);
            $this->writeTemplateFile('src/controllers/HomeController.php', $data);
            Filesystem::get("$this->devDirectory/code_templates/app/index_php")->copyTo("index.php");
        } 
        return $preFlight;
    }
}
