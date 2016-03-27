<?php
use anyen\Wizard;

return [
    Wizard::page(
        'Initialize Ntentan',
        Wizard::text(
            'This wizard will guide you to setup an ntentan dev environment.' .
            'Upon completion, you would have all the directories and files ' .
            'that ntentan requires correctly setup.'
        )
    ),
    Wizard::page(
        'Namespace',
        Wizard::text(
            'Please provide a base namespace for your apps classes. ' .
            'This will give you the opportunity to build your app under a namespace of your choice. ' .
            'Ntentan will write out all the required changes to your composer files to make this work.'
        ),
        Wizard::input('Namespace', 'namespace', ['required' => true]),
        Wizard::onroute(function($wizard){
            $wizard->getCallbackObject()->runTask($wizard->getData());
        })
    )
];
