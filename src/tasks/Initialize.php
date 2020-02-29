<?php


namespace ntentan\dev\tasks;


use clearice\io\Io;
use ntentan\honam\Templates;
use ntentan\utils\Filesystem;

class Initialize
{
    private $options;
    private $homeDirectory = __DIR__ . "/../..";
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
        file_put_contents($template, $this->templates->render("$this->homeDirectory/code_templates/app/$template.mustache", $data));
    }

    public function run()
    {
        $namespace = $this->options['namespace'] ?? 'app';
        $name = $this->options['name'] ?? addslashes('An ntentan application');
        $this->io->output("Copying skeleton application\n");
        Filesystem::get("$this->homeDirectory/code_templates/app/views")->copyTo("./views");
        Filesystem::get("$this->homeDirectory/code_templates/app/bootstrap")->copyTo("./bootstrap");
        $this->io->output("Creating public directory\n");
        Filesystem::directory("public")->create();
        Filesystem::directory("config")->create();
        Filesystem::directory("src/controllers")->create(true);
        Filesystem::directory("src/models")->create(true);
        $data = ['name' => $name, 'namespace' => $namespace];
        $this->writeTemplateFile('public/index.php', $data);
        $this->writeTemplateFile('config/app.conf.php', $data);
        $this->writeTemplateFile('src/controllers/HomeController.php', $data);
//        file_put_contents("public/index.php", $this->templates->render("$this->homeDirectory/code_templates/app/public/index.php.mustache", ['namespace' => $namespace]));
//        file_put_contents("config/app.conf.php", $this->templates->render("$this->homeDirectory/code_templates/app/config/app.conf.php.mustache", ['name' => $name]));
//        file_put_contents("src/controllers/HomeController.php", $this->templates->render("$this->homeDirectory/code_templates/app/src", ['name' => $name]));
        Filesystem::get("$this->homeDirectory/code_templates/app/index_php")->copyTo("index.php");
    }
}
