@extends('layouts.app')

@section('content')
<div class="h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-md">
        <div class="bg-white shadow-md rounded-lg">
            <div class="bg-gray-800 text-white text-lg font-semibold p-4 rounded-t-lg">
                {{ __('Verify Your Email Address') }}
            </div>

            <div class="p-6">
                @if (session('resent'))
                    <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
                        {{ __('A fresh verification link has been sent to your email address.') }}
                    </div>
                @endif

                <p class="text-gray-700 mb-4">
                    {{ __('Before proceeding, please check your email for a verification link.') }}
                </p>
                <p class="text-gray-700 mb-4">
                    {{ __('If you did not receive the email') }},
                </p>
                <form class="inline" method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" class="text-blue-500 hover:underline">
                        {{ __('click here to request another') }}
                    </button>.
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
