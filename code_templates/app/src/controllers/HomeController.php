<?php
namespace app\controllers;

use ntentan\Controller;
use ntentan\View;

class HomeController extends Controller
{
    public function index(View $view)
    {
        return $view;
    }
}
