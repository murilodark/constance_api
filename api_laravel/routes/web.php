<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Route::post('/login', function () {
//     echo 'chegou post em login';
//     exit;
// });

Route::get('/login', function () {
  echo 'chegou post em login';
  exit;
});