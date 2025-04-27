<template>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
        <div>
            <label class="text-sm mb-1 block">Search by Name</label>
            <input v-model="filters.search" @input="debounceSearch" placeholder="Search..." class="w-full border px-3 py-2 rounded text-sm" />
        </div>
        <div>
            <label class="text-sm mb-1 block">Brand</label>
            <select v-model="filters.brand" @change="loadData" class="w-full border px-3 py-2 rounded text-sm">
                <option value="">All</option>
                <option v-for="b in brandList" :key="b" :value="b">{{ b }}</option>
            </select>
        </div>

        <div>
            <label class="text-sm mb-1 block">Price Range (Rp)</label>
            <div class="flex gap-2">
                <input v-model="filters.min_price_formatted" @input="syncPrice('min')" @change="loadData" placeholder="Min"
                    class="w-full border px-3 py-2 rounded text-sm" />
                <input v-model="filters.max_price_formatted" @input="syncPrice('max')" @change="loadData" placeholder="Max"
                    class="w-full border px-3 py-2 rounded text-sm" />
            </div>
        </div>

        <div>
            <label class="text-sm mb-1 block">Status</label>
            <select v-model="filters.status" @change="loadData" class="w-full border px-3 py-2 rounded text-sm">
                <option value="">All</option>
                <option value="available">Available</option>
                <option value="booked">Booked</option>
            </select>
        </div>

        <div class="flex gap-2 items-end">
            <button @click="resetFilters" class="bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-400 transition">
                Reset
            </button>
        </div>
    </div>

    <DataTable :value="cars" :lazy="true" :loading="loading" :paginator="true" :rows="rows" :totalRecords="total" dataKey="id" @page="onPage"
        @sort="onSort" class="shadow rounded overflow-hidden" tableClass="min-w-full text-sm" headerClass="bg-gray-100 text-gray-700 font-semibold"
        :rowClass="() => 'hover:bg-gray-50 transition'">
        <Column field="name" header="Name" sortable />
        <Column field="brand" header="Brand" sortable />
        <Column field="price_per_day" header="Price/Day" sortable />
        <Column field="availability_status" header="Status" sortable />
        <Column header="Action" bodyClass="text-center">
            <template #body="{ data }">
                <button :class="[
                    'text-white text-sm px-3 py-1 rounded hover:bg-blue-600 transition',
                    data.is_booked ? 'bg-gray-500 cursor-not-allowed' : 'bg-blue-500'
                ]" :disabled="data.is_booked" @click="!data.is_booked && $router.push(`/book/${data.id}`)">
                    Book
                </button>
            </template>
        </Column>
    </DataTable>

</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'

const cars = ref([])
const loading = ref(false)
const total = ref(0)
const rows = ref(10)
const page = ref(0)
const sortField = ref(null)
const sortOrder = ref(null)

const filters = ref({
    brand: '',
    min_price: '',
    max_price: '',
    min_price_formatted: '',
    max_price_formatted: '',
    status: '',
    search: '',
})

let searchTimeout = null;

function debounceSearch() {
    // Batalkan pencarian sebelumnya jika ada
    clearTimeout(searchTimeout);

    // Menunggu 800ms sebelum memanggil loadData
    searchTimeout = setTimeout(() => {
        loadData();
    }, 800);
}

const brandList = [
    'Toyota', 'Honda', 'Ford', 'BMW', 'Mercedes',
    'Nissan', 'Hyundai', 'Kia', 'Volkswagen', 'Mazda', 'Chevrolet'
]

function syncPrice(type) {
    const str = filters.value[`${type}_price_formatted`].replace(/\D/g, '')
    const num = parseInt(str || '0')
    filters.value[`${type}_price`] = num
    filters.value[`${type}_price_formatted`] = num.toLocaleString('id-ID')
}

function resetFilters() {
    filters.value = {
        brand: '',
        min_price: '',
        max_price: '',
        min_price_formatted: '',
        max_price_formatted: '',
        status: '',
        search: '',
    }
    loadData()
}

async function loadData() {
    loading.value = true

    const params = {
        page: page.value + 1,
        per_page: rows.value,
        brand: filters.value.brand,
        min_price: filters.value.min_price,
        max_price: filters.value.max_price,
        availability_status: filters.value.status,
        search: filters.value.search
    }

    if (sortField.value && sortOrder.value !== null) {
        params.sort_by = sortField.value
        params.sort_order = sortOrder.value === 1 ? 'asc' : 'desc'
    }

    const res = await api.get('/cars/', { params })
    cars.value = res.data.data.map(car => {
        // Menambahkan `is_booked` berdasarkan data yang sudah ada di server
        car.is_booked = car.is_booked || false; // jika `is_booked` kosong, set ke `false`
        return car;
    })
    total.value = res.data.total
    loading.value = false
}


function onPage(event) {
    page.value = event.page
    rows.value = event.rows
    loadData()
}

function onSort(event) {
    sortField.value = event.sortField
    sortOrder.value = event.sortOrder
    loadData()
}

onMounted(loadData);
</script>