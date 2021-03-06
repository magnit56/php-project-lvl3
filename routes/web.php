<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Jobs\StoreSeoInformation;

Route::get('/', function (Request $request, $name = ''): Illuminate\View\View {
    return view('index', ['name' => $name]);
})->name('urls.create');

Route::get('/urls/{id}', function (Request $request, $id): Illuminate\View\View {
    $site = DB::table('urls')
        ->where('id', $id)
        ->first();
    if (!boolval($site)) {
        abort(404);
    }
    $checks = DB::table('url_checks')
        ->where('url_id', $id)
        ->get();

    return view('url', [
        'site' => $site,
        'checks' => $checks,
    ]);
})->name('urls.show');

Route::get('/urls', function (Request $request): Illuminate\View\View {
    $sites = DB::table('urls')->paginate(10);
    $checks = DB::table('url_checks')
        ->whereIn('url_id', $sites->pluck('id'))
        ->orderBy('created_at')
        ->distinct('url_id')
        ->get()
        ->keyBy('url_id');
    return view('urls', ['sites' => $sites, 'checks' => $checks]);
})->name('urls.index');

Route::post('/urls', function (Request $request): Illuminate\Http\RedirectResponse {
    $url = $request->input('url');
    $link = $url['name'];

    $data = ['name' => $link];
    $validator = Validator::make($data, [
        'name' => 'required|url|max:255'
    ]);
    if ($validator->fails()) {
        flash('Некорректный URL')->error();
        return response()
            ->redirectToRoute('urls.create')
            ->withErrors($validator)
            ->withInput();
    }

    $scheme = parse_url($link, PHP_URL_SCHEME);
    $host = parse_url($link, PHP_URL_HOST);
    $name = "{$scheme}://{$host}";

    $user = DB::table('urls')->where('name', $name)->get();
    if ($user->count() > 0) {
        $id = optional(DB::table('urls')
            ->where('name', $name)
            ->first())->id;

        flash('Сайт уже был добавлен ранее')->warning();
        return redirect("urls/{$id}");
    }

    $id = DB::table('urls')->insertGetId(
        [
            'name' => $name,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]
    );

    flash('Сайт успешно добавлен')->success();
    return redirect("/urls/{$id}");
})->name('urls.store');

Route::post('/urls/{id}/checks', function (Request $request, $id): Illuminate\Http\RedirectResponse {
    $site = DB::table('urls')
        ->where('id', $id)
        ->first();
    if (!boolval($site)) {
        abort(404);
    }
    StoreSeoInformation::dispatch(optional($site)->id, optional($site)->name);
    flash('Страница добавлена в очередь на проверку')->success();
    return redirect("/urls/{$id}");
})->name('url_checks.store');
