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

    public function __construct(Templates $templates, Io $io)
    {
        $this->templates = $templates;
        $this->io = $io;
    }

    public function setOptions($options)
    {
        $this->options = $options;
    }

    public function run()
    {
        $namespace = $this->options['namespace'] ?? 'app';
        $this->io->output("Copying skeleton application\n");
        Filesystem::get("$this->homeDirectory/code_templates/src")->copyTo("");
        Filesystem::get("$this->homeDirectory/code_templates/views")->copyTo("");
        $this->io->output("Creating public directory\n");
        Filesystem::directory("public")->create();
        $this->templates->render("$this->homeDirectory/code_templates/", ['namespace' => $namespace]);
    }
}
