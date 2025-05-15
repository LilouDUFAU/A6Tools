@extends('layouts.app')

@section('content')
<div class="h-full flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md">
        <div class="bg-white shadow-md rounded-lg">
            <div class="bg-gray-800 text-white text-lg font-semibold p-4 rounded-t-lg">
                {{ __('Vérifiez votre adresse e-mail') }}
            </div>

            <div class="p-6">
                @if (session('resent'))
                    <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                        {{ __('Un nouveau lien de vérification a été envoyé à votre adresse e-mail.') }}
                    </div>
                @endif

                <p class="text-gray-700 mb-4">
                    {{ __('Avant de continuer, veuillez vérifier votre e-mail pour un lien de vérification.') }}
                </p>
                <p class="text-gray-700 mb-4">
                    {{ __('Si vous n\'avez pas reçu l\'e-mail') }},
                </p>
                <form class="inline" method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" class="text-blue-500 hover:underline">
                        {{ __('cliquez ici pour en demander un autre') }}
                    </button>.
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
