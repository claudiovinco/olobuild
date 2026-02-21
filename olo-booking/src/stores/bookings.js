import { defineStore } from 'pinia';
import { api } from './api.js';

export const useBookingsStore = defineStore('bookings', {
    state: () => ({
        bookings: [],
        current: null,
        log: [],
        loading: false,
        error: null,
        filters: {
            service_type: '',
            service_id: '',
            status: '',
            date_from: '',
            date_to: '',
            search: '',
        },
    }),

    actions: {
        async fetchBookings(params = {}) {
            this.loading = true;
            this.error = null;
            try {
                const query = new URLSearchParams();
                const merged = { ...this.filters, ...params };
                for (const [k, v] of Object.entries(merged)) {
                    if (v) query.set(k, v);
                }
                const qs = query.toString();
                this.bookings = await api.get('/bookings' + (qs ? '?' + qs : ''));
            } catch (e) {
                this.error = e.message;
            } finally {
                this.loading = false;
            }
        },

        async fetchBooking(id) {
            this.loading = true;
            try {
                this.current = await api.get('/bookings/' + id);
            } catch (e) {
                this.error = e.message;
            } finally {
                this.loading = false;
            }
        },

        async fetchLog(id) {
            try {
                this.log = await api.get('/bookings/' + id + '/log');
            } catch (e) {
                this.error = e.message;
            }
        },

        async createBooking(data) {
            return await api.post('/bookings', data);
        },

        async updateBooking(id, data) {
            const result = await api.put('/bookings/' + id, data);
            this.current = result;
            return result;
        },

        async updateStatus(id, status) {
            const result = await api.patch('/bookings/' + id + '/status', { status });
            this.current = result;
            return result;
        },

        async deleteBooking(id) {
            await api.del('/bookings/' + id);
            this.bookings = this.bookings.filter(b => b.id !== id);
        },
    },
});
