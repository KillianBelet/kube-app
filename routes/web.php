<?php

use App\Models\Counter;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $value = Counter::sum('count');
    return view('welcome', ['value' => $value]);
});

// Sonde Kubernetes : le conteneur est vivant (ne touche pas la base de données).
// Sans le groupe "web" : pas de session créée à chaque appel des sondes.
Route::get('/healthz', function () {
    return response('ok', 200)->header('Content-Type', 'text/plain');
})->withoutMiddleware('web');

// Métriques au format texte Prometheus
Route::get('/metrics', function () {
    try {
        $count = Counter::sum('count');
        $dbUp = 1;
    } catch (\Throwable $e) {
        $count = 0;
        $dbUp = 0;
    }

    $lines = [
        '# HELP app_up Application en marche',
        '# TYPE app_up gauge',
        'app_up 1',
        '# HELP app_db_up Base de donnees joignable',
        '# TYPE app_db_up gauge',
        "app_db_up $dbUp",
        '# HELP app_counter_total Valeur du compteur',
        '# TYPE app_counter_total gauge',
        "app_counter_total $count",
    ];

    return response(implode("\n", $lines) . "\n", 200)
        ->header('Content-Type', 'text/plain; version=0.0.4');
})->withoutMiddleware('web');
