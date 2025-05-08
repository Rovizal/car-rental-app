<template>
    <div class="mb-4 flex items-center gap-4">
        <label class="text-sm font-medium">Filter Status:</label>
        <select v-model="filterStatus" @change="loadData" class="border rounded px-3 py-2 text-sm">
            <option value="">All</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
        </select>
    </div>

    <DataTable :value="bookings" :lazy="true" :loading="loading" :paginator="true" :rows="rows" :totalRecords="total" dataKey="id" @page="onPage"
        @sort="onSort" :sortField="sortField" :sortOrder="sortOrder" class="shadow rounded overflow-hidden" tableClass="min-w-full text-sm"
        headerClass="bg-gray-100 text-gray-700 font-semibold" :rowClass="() => 'hover:bg-gray-50 transition'">
        <Column field="user_name" header="Customer" sortable />
        <Column field="car.name" header="Car" />
        <Column field="start_date" header="Start" sortable />
        <Column field="end_date" header="End" sortable />
        <Column field="total_price" header="Total" />
        <Column field="status" header="Status">
            <template #body="{ data }">
                <span :class="[
                    'text-xs px-3 py-1 rounded-full font-semibold',
                    data.status === 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                ]">
                    {{ data.status }}
                </span>
            </template>
        </Column>
        <Column header="Action">
            <template #body="{ data }">
                <button @click="updateStatus(data.id)" :disabled="data.status === 'confirmed' || data.status === 'canceled'"
                    class="text-xs px-3 py-1 rounded transition" :class="[
                        data.status === 'confirmed' || data.status === 'canceled'
                            ? 'bg-gray-300 text-gray-600 cursor-not-allowed'
                            : 'bg-green-500 text-white hover:bg-green-600'
                    ]">
                    {{
                        data.status === 'confirmed'
                            ? 'Already Confirmed'
                            : data.status === 'canceled'
                                ? 'Canceled'
                                : 'Mark Confirmed'
                    }}
                </button>
            </template>
        </Column>
    </DataTable>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import DataTable from 'primevue/datatable'
import Column from 'primevue/column'
import api from '../services/api'
import Swal from 'sweetalert2'

const filterStatus = ref('')
const bookings = ref([])
const loading = ref(false)
const total = ref(0)
const rows = ref(10)
const page = ref(0)
const sortField = ref('created_at')
const sortOrder = ref(-1)

async function loadData() {
    loading.value = true
    const res = await api.get('/bookings', {
        params: {
            page: page.value + 1,
            per_page: rows.value,
            sort_by: sortField.value,
            sort_order: sortOrder.value === 1 ? 'asc' : 'desc',
            status: filterStatus.value
        }
    });
    bookings.value = res.data.data
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

async function updateStatus(id) {
    try {
        await api.patch(`/bookings/${id}/status`, { status: 'confirmed' })
        await loadData()

        Swal.fire({
            icon: 'success',
            title: 'Booking Confirmed!',
            showConfirmButton: false,
            timer: 1500
        })
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Failed to confirm booking',
            text: error.message
        })
    }
}

onMounted(loadData);
</script>