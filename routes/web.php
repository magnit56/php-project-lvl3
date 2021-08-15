<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Carbon\Carbon;

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

Route::get('/', function (Request $request) {
    return view('index');
})->name('urls.create');
Route::get('/urls/{id}', function (Request $request, $id) {
    $site = DB::table('urls')
        ->where('id', $id)
        ->first();

    return view('url', [
        'id' => $site->id,
        'name' =>$site->name,
        'createdAt' => $site->created_at,
        'updatedAt' => $site->updated_at,
    ]);
})->name('urls.show');
Route::get('/urls', function (Request $request) {
    $sites = DB::table('urls')->paginate(10);
    return view('urls', ['sites' => $sites]);
})->name('urls.index');
Route::post('/', function (Request $request) {
    try {
        $request->validate([
            'url.name' => 'required|url|max:255'
        ]);
    } catch (Exception) {
        $url = $request->input('url');
        $name = $url['name'];

        flash('Некорректный URL')->error();
        return view('index', ['name' => $name]);
    }
    $url = $request->input('url');
    $name = $url['name'];

    $user = DB::table('urls')->where('name', $name)->get();
    if ($user->count() > 0) {
        $id = DB::table('urls')
            ->where('name', $name)
            ->first()->id;

        flash('Сайт уже был добавлен ранее')->warning();
        return redirect("urls/{$id}");
    }

    DB::table('urls')->insert(
        [
            'name' => $name,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]
    );

    $id = DB::table('urls')
        ->where('name', $name)
        ->first()->id;

    flash('Сайт успешно добавлен')->success();
    return redirect("/urls/{$id}");
})->name('urls.store');
