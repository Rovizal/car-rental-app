<template>
    <main class="p-6 max-w-xl mx-auto">
        <h1 class="text-2xl font-bold text-blue-600 mb-6">Book Car</h1>

        <div v-if="car" class="space-y-6">
            <div class="bg-white p-4 rounded shadow border">
                <h2 class="text-xl font-semibold text-gray-800">{{ car.name }}</h2>
                <p class="text-gray-600">Brand: {{ car.brand }}</p>
                <p class="text-gray-600">Price per day: {{ formatRupiah(car.price_per_day) }}</p>
            </div>

            <form @submit.prevent="submitBooking" class="space-y-4 bg-white p-4 rounded shadow border">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                    <AutoComplete v-model="selectedUser" :suggestions="filteredUsers" complete-on-focus field="name" :dropdown="true"
                        placeholder="Search user..." @complete="searchUsers" class="w-full" />
                </div>

                <div class="flex gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input v-model="startDate" type="date" :min="today" required class="w-full mt-1 border rounded px-3 py-2" />
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700">End Date</label>
                        <input v-model="endDate" type="date" :min="startDate || today" required class="w-full mt-1 border rounded px-3 py-2" />
                    </div>
                </div>

                <div>
                    <p class="text-gray-700 font-medium">Total: {{ formatRupiah(totalPrice) }}</p>
                </div>

                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
                    Confirm Booking
                </button>
            </form>
        </div>
        <p v-else>Loading...</p>
    </main>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '../services/api';

const today = new Date().toISOString().split('T')[0]
const route = useRoute()
const router = useRouter()

const car = ref(null)
const startDate = ref('')
const endDate = ref('')

const selectedUser = ref(null)
const filteredUsers = ref([])

async function searchUsers(event) {
    const res = await api.get('/users/search', {
        params: { q: event.query }
    })
    filteredUsers.value = res.data
}


onMounted(async () => {
    const res = await api.get(`/cars/${route.params.id}`);
    car.value = res.data.car;

})

const totalPrice = computed(() => {
    if (!car.value || !startDate.value || !endDate.value) return 0
    const start = new Date(startDate.value)
    const end = new Date(endDate.value)
    const days = Math.max(1, (end - start) / (1000 * 60 * 60 * 24) + 1)
    return car.value.price_per_day * days
})

const submitBooking = async () => {
    await api.post('/bookings', {
        user_id: selectedUser.value?.id,
        car_id: car.value.id,
        start_date: startDate.value,
        end_date: endDate.value,
        total_price: totalPrice.value
    });
    router.push('/confirm-booking');
}

const formatRupiah = (value) => {
    if (!value) return 'Rp 0'
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value)
};

</script>