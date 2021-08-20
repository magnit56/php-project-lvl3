<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Carbon\Carbon;

Route::get('/', function (Request $request) {
    return view('index');
})->name('urls.create');

Route::get('/urls/{id}', function (Request $request, $id) {
    $site = DB::table('urls')
        ->where('id', $id)
        ->first();
    $checks = DB::table('url_checks')
        ->where('url_id', $id)
        ->get();

    return view('url', [
        'id' => $site->id,
        'name' =>$site->name,
        'createdAt' => $site->created_at,
        'updatedAt' => $site->updated_at,
        'checks' => $checks,
    ]);
})->name('urls.show');

Route::get('/urls', function (Request $request) {
    $sites = DB::table('urls')->paginate(10);
    $checks = DB::table('url_checks')
        ->whereIn('url_id', $sites->pluck('id'))
        ->orderBy('created_at')
        ->distinct('url_id')
        ->get()
        ->keyBy('url_id');
    return view('urls', ['sites' => $sites, 'checks' => $checks]);
})->name('urls.index');

Route::post('/', function (Request $request) {
//    Изменить валидацию
//    Обрезать url
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

Route::post('/urls/{id}/checks', function (Request $request, $id) {
//    Обработать ошибку неуспешной проверки
    DB::table('url_checks')->insert(
        [
            'url_id' => $id,
            'status_code' => 200,
            'h1' => 'h1',
            'keywords' => 'keyword',
            'description' => 'description',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]
    );
    flash('Какой-то косяк')->error();
    flash('Страница успешно проверена')->success();
    return redirect("/urls/{$id}");
})->name('url_checks.store');
