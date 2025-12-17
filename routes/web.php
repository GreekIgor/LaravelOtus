<?php

use App\Models\Recipe;
use App\Models\Tag;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    return view('Home');
});

Route::get('/personal', function () {
    return view('UserPersonal');
});

Route::get('/recipe', function () {
    return view('Recipe');
});

Route::get('/list', function () {
    return view('RecipeList');
});

Route::get('/recipe-edit', function () {
    return view('RecipeEdit');
});

Route::get('/dbrecipe', function () {
    dd(Tag::all());
    return '<h1>Debugbar test</h1>';
});


