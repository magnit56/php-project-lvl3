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
                        <th scope="col">Имя</th>
                        <th scope="col">Последняя проверка</th>
                        <th scope="col">Код ответа</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($sites as $site)
                    <tr>
                        <th scope="row">{{  $site->id  }}</th>
                        <td><a href="/urls/{{  $site->id  }}">{{  $site->name  }}</a></td>
                        <td>{{  $checks[$site->id]->created_at ?? ''  }}</td>
{{--                        <td>{{  $checks[$site->id]->status_code ?? ''  }}</td>--}}
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
