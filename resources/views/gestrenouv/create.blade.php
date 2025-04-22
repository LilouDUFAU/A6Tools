@extends('layouts.app')
@section('content')
<div class="min-h-screen flex flex-col items-center py-10">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Hello World</h1>
    <form action="{{ route('gestrenouv.store') }}" method="POST" class="w-full max-w-md bg-white p-6 rounded shadow-md">
        @csrf
        <div class="mb-4">
            <label for="name" class="block text-gray-700 font-medium mb-2">Name</label>
            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" id="name" name="name" required>
        </div>
        <div class="mb-4">
            <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
            <input type="email" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" id="email" name="email" required>
        </div>
        <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Submit</button>
    </form>

    <a href="{{ route('gestrenouv.index') }}" class="mt-4 text-gray-500 hover:underline">Retour</a>
</div>
@endsection