@extends('app')

@section('title', "Анализатор страниц - сайт $name")

@section('content')
    <main class="bg-light py-5">
        <div class="container">
            <h1>Сайт {{ $name }}</h1>
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-nowrap">
                    <tbody>
                    <tr>
                        <td>ID</td>
                        <td>{{ $id }}</td>
                    </tr>
                    <tr>
                        <td>URL</td>
                        <td><a href="{{ $name }}">{{ $name }}</td>
                    </tr>
                    <tr>
                        <td>Дата добавления сайта</td>
                        <td>{{ $createdAt }}</td>
                    </tr>
                    <tr>
                        <td>Дата обновления информации о сайте</td>
                        <td>{{ $updatedAt }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <h2>Проверки</h2>
            <form action="{{ $id }}/checks" method="POST" class="d-flex justify-content-left">
                {{ csrf_field() }}
                <input type="hidden">
                <button type="submit" class="btn btn-primary">Запустить проверку</button>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-nowrap">
                    <tbody>
                    <tr>
                        <th>ID</th>
                        {{--                        <th>Код ответа</th>--}}
                        {{--                        <th>h1</th>--}}
                        {{--                        <th>keywords</th>--}}
                        {{--                        <th>description</th>--}}
                        <th>Дата создания</th>
                    </tr>
                    @foreach($checks as $check)
                        <tr>
                            <td>{{ $check->id }}</td>
                            {{--                            <td>{{ $check->status_code }}</td>--}}
                            {{--                            <td>{{ $check->h1 }}</td>--}}
                            {{--                            <td>{{ $check->keywords }}</td>--}}
                            {{--                            <td>{{ $check->description }}</td>--}}
                            <td>{{ $check->created_at }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>
@endsection
