<?php

$key = "897cf84b3088c7bec1bfd7f7957fa244";

echo "Enter city name OR 'city, country'" . PHP_EOL;
$userInput = trim(strtolower(readline("City name to get weather data:")));

if (is_numeric($userInput)) {
    echo "Please enter a string and not a number!";
    exit;
}

$cityUrl = "";

if (strpos($userInput, ",")) {
    $words = explode(",", $userInput);
    $cityUrl = urlencode($words[0]);
} else {
    $cityUrl = urlencode($userInput);
}

try {
    $url = "https://api.openweathermap.org/data/2.5/weather?q={$cityUrl}&appid={$key}&units=metric";
    $weatherJSON = file_get_contents($url);

    if ($weatherJSON === false) {
        throw new Exception("Check your network or input validity!");
    }
    $weatherData = json_decode($weatherJSON);

    if ($weatherData === null) {
        throw new Exception("Error parsing weather data");
    }

    if (isset($weatherData->cod) && $weatherData->cod === 429) {
        throw new Exception("API limit exceeded, try again later!");
    }

    if (isset($weatherData->cod) && $weatherData->cod !== 200) {
        throw new Exception("Unexpected error");
    }

    $cityName = isset($weatherData->name) ? $weatherData->name : 'Unknown';
    $weatherDescription = isset($weatherData->weather[0]->description) ? $weatherData->weather[0]->description : 'Unknown';
    $weatherTemperature = isset($weatherData->main->temp) ? $weatherData->main->temp : 'Unknown';

    echo "$cityName: $weatherDescription, $weatherTemperature Celsius";


} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}