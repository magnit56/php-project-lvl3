<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Jobs\StoreSeoInformation;

Route::get('/', function (Request $request, $name = '') {
    return view('index', ['name' => $name]);
})->name('urls.create');

Route::get('/urls/{id}', function (Request $request, $id) {
    $site = DB::table('urls')
        ->where('id', $id)
        ->first();
    $checks = DB::table('url_checks')
        ->where('url_id', $id)
        ->get();

    return view('url', [
        'site' => $site,
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
    $validator = Validator::make($request->all(), [
        'url.name' => 'required|url|max:255',
    ]);
    if ($validator->fails()) {
        $url = $request->input('url');
        $name = $url['name'];

        flash('Некорректный URL')->error();
        return redirect()
            ->route('urls.create', ['name' => $name])
            ->withErrors($validator)
            ->withInput();
    }

    $url = $request->input('url');
    $link = $url['name'];
    $scheme = parse_url($link, PHP_URL_SCHEME);
    $host = parse_url($link, PHP_URL_HOST);
    $name = "{$scheme}://{$host}";

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
    $site = DB::table('urls')
        ->where('id', $id)
        ->first();
    StoreSeoInformation::dispatch($site->id, $site->name);
    flash('Страница добавлена в очередь на проверку')->success();
    return redirect("/urls/{$id}");
})->name('url_checks.store');
