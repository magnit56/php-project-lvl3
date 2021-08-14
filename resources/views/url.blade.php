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
        </div>
    </main>
@endsection
