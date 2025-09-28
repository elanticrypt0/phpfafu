<?php

// Error: 404 Página no encontrada
Flight::map('notFound', function () {
    echo 'Error 404: <br/> Página no encontrada';
});

// Then define a route and assign a function to handle the request.
Flight::route('/', function () {
  Flight::view()->render('home/home.latte', ['title' => 'My App']);
});

Flight::route('POST /saludar', function () {
   $request = Flight::request();

   $nombre = $request->data['nombre'] ?? 'Pepe';

   $homeController = Flight::homeController();
   $homeController->saludar($nombre);
});