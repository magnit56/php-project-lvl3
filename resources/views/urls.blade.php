@extends('app')

@section('title', "Анализатор страниц - сайты")

@section('content')
    <main class="bg-light py-5">
        <div class="container">
            <div class="table-responsive">
                <table class="table caption-top">
                    <caption>Список сайтов</caption>
                    <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">URL</th>
                        <th scope="col">Дата добавления сайта</th>
                        <th scope="col">Дата обновления информации о сайте</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($sites as $site)
                    <tr>
                        <th scope="row">{{ $site->id }}</th>
                        <td><a href="/urls/{{ $site->id }}">{{ $site->name }}</a></td>
                        <td>{{ $site->created_at }}</td>
                        <td>{{ $site->updated_at }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div>
                {{ $sites->render('vendor.pagination.bootstrap-4'); }}
            </div>
        </div>
    </main>
@endsection
