<?php

use App\Http\Controllers\Api\V1\Utils\CepController;
use Illuminate\Support\Facades\Route;

// Note que o prefixo 'api/v1/utils' resultará em api/v1/utils/cep/{cep}
Route::prefix('v1/utils')->group(function () {
    Route::get('cep/{cep}', CepController::class);
});
