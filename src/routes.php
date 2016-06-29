<?php

Route::get(Config::get('laravel-5-solarium.uri'), 'Fbf\LaravelSolarium\SearchController@results');