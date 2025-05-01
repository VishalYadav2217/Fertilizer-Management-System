<?php

use App\Http\Controllers\RecommendationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RecommendationController::class, 'index'])->name('home');
Route::post('/recommend', [RecommendationController::class, 'getRecommendation'])->name('recommend');