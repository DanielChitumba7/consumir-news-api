<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NewsApiController;

Route::get('/noticias', [NewsApiController::class, 'HeadLines']);