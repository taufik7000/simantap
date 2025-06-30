<div class="w-full sm:max-w-md p-6 mx-auto">
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold tracking-tight text-gray-950 dark:text-white">
            Selamat Datang Kembali
        </h2>
        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
            Silakan masuk untuk melanjutkan
        </p>
    </div>

    <form wire:submit.prevent="authenticate">
        {{-- Email --}}
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Email</label>
            <input wire:model.lazy="email" id="email" type="email" required autofocus
                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:border-primary-500 focus:ring-primary-500">
            @error('email') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
        </div>

        {{-- Password --}}
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Password</label>
            <input wire:model.lazy="password" id="password" type="password" required
                   class="block w-full mt-1 border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 focus:border-primary-500 focus:ring-primary-500">
        </div>

        {{-- Remember Me --}}
        <div class="flex items-center justify-between mb-4">
            <label for="remember" class="flex items-center">
                <input wire:model.lazy="remember" id="remember" type="checkbox" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500">
                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Ingat saya</span>
            </label>
        </div>

        {{-- Submit Button --}}
        <div class="mt-6">
            <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                Masuk
            </button>
        </div>
    </form>
</div>