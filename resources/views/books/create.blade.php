@extends('layouts.app')

@section('content')
    <div>
        <h1 class="font-bold text-lg text-center">Add the book!</h1>
        <div>
            <form method="POST" action="{{ route('books.store') }}">
                @csrf
                <label for="title">Title of the book</label>
                <textarea name="title" id="title" required class="input mb-4"></textarea>

                <label for="author">Author</label>
                <textarea name="author" id="author" required class="input mb-4"></textarea>

                <button type="submit" class="btn">Add Book</button>
            </form>
        </div>

    </div>


@endsection
