<?php

require __DIR__."/BaseController.php";

class HomeController extends BaseController{

    public function saludar(string $name="Pepe"): void{
        echo "Hola, {$name}";
    }

}