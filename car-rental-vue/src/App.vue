<template>
  <div>
    <!-- Navbar -->
    <nav class="bg-white shadow px-6 py-4 flex items-center gap-4">
      <router-link to="/" class="text-sm font-medium px-4 py-2 rounded-lg bg-blue-500 text-white hover:bg-blue-600 transition">
        Home
      </router-link>
      <router-link to="/cars" class="text-sm font-medium px-4 py-2 rounded-lg bg-blue-500 text-white hover:bg-blue-600 transition">
        Cars
      </router-link>
      <router-link to="/bookings" class="text-sm font-medium px-4 py-2 rounded-lg bg-purple-500 text-white hover:bg-purple-600 transition">
        Booking List
      </router-link>
    </nav>

    <!-- Page content -->
    <router-view />
  </div>
</template>
