import { defineStore } from 'pinia';
import { api } from './api.js';

export const useManagerStore = defineStore('manager', {
    state: () => ({
        user: window.oloManagerConfig?.user || {},
        services: [],
        stats: null,
        upcoming: [],
        loading: false,
        error: null,
    }),

    getters: {
        isAdmin: (state) => state.user.is_admin,
        serviceCount: (state) => state.services.length,
    },

    actions: {
        async fetchServices() {
            this.loading = true;
            try {
                this.services = await api.get('/manager/services');
            } catch (e) {
                this.error = e.message;
            } finally {
                this.loading = false;
            }
        },

        async fetchStats() {
            try {
                this.stats = await api.get('/stats');
            } catch (e) {
                this.error = e.message;
            }
        },

        async fetchUpcoming() {
            try {
                this.upcoming = await api.get('/upcoming?days=14');
            } catch (e) {
                this.error = e.message;
            }
        },

        async fetchDashboard() {
            await Promise.all([
                this.fetchServices(),
                this.fetchStats(),
                this.fetchUpcoming(),
            ]);
        },
    },
});
