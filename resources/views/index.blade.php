<!-- Хранится в resources/views/about.blade.php -->

@extends('app')

<!-- Секция, содержимое которой обычный текст. -->
@section('title', 'Анализатор страниц - главная')

<!-- Секция, содержащая HTML блок. Имеет открывающую и закрывающую часть. -->
@section('content')
    <main class="bg-dark py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-10 col-lg-8 mx-auto text-white">
                    <h1 class="display-3">Анализатор страниц</h1>
                    <p class="lead">Бесплатно проверяйте сайты на SEO пригодность</p>
                    <form action="/urls" method="POST" class="d-flex justify-content-center">
                        {{ csrf_field() }}
                        <input type="hidden">
                        <input type="text" name="url[name]" value="{{ isset($name) ? $name : '' }}" class="form-control form-control-lg" placeholder="http://www.google.com">
                        <button type="submit" class="btn btn-lg btn-primary ml-3 px-5 text-uppercase">Проверить</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection
