<?php

use Cloudstudio\Ollama\Facades\Ollama;


$response = Ollama::agent('You are a weather expert...')
    ->prompt('Why is the sky blue?')
    ->model('llama2')
    ->options(['temperature' => 0.8])
    ->stream(false)
    ->ask();
    echo $response;
