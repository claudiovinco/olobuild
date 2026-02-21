import { createRouter, createWebHashHistory } from 'vue-router';

import DashboardView from './views/DashboardView.vue';
import ServiceDetailView from './views/ServiceDetailView.vue';
import BookingsView from './views/BookingsView.vue';
import BookingDetailView from './views/BookingDetailView.vue';
import UsersView from './views/UsersView.vue';
import PermissionsView from './views/PermissionsView.vue';

export const router = createRouter({
    history: createWebHashHistory(),
    routes: [
        { path: '/', name: 'dashboard', component: DashboardView },
        { path: '/struttura/:id', name: 'service', component: ServiceDetailView, props: true },
        { path: '/prenotazioni', name: 'bookings', component: BookingsView },
        { path: '/prenotazione/:id', name: 'booking', component: BookingDetailView, props: true },
        { path: '/utenti', name: 'users', component: UsersView },
        { path: '/permessi', name: 'permissions', component: PermissionsView },
    ],
});
