<template>
    <main class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-white">
        <div class="text-center max-w-xl">
            <h1 class="text-4xl font-extrabold text-blue-700 mb-4">Welcome to Car Rental App</h1>
            <p class="text-lg text-gray-600 mb-6">
                Browse available cars, book instantly, and get your ride confirmed with ease.
            </p>

            <router-link to="/cars" class="inline-block px-6 py-3 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition">
                View Cars
            </router-link>
        </div>
    </main>
</template>