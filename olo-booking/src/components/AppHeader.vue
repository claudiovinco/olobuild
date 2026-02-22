<template>
  <header class="olom-header">
    <router-link to="/" class="olom-logo">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
        <rect x="2" y="2" width="20" height="20" rx="6" fill="currentColor" opacity="0.2"/>
        <path d="M8 12L11 15L16 9" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      Olo Booking
    </router-link>
    <nav class="olom-nav">
      <router-link to="/" class="olom-nav-link" :class="{ 'olom-nav-active': $route.path === '/' }">Dashboard</router-link>
      <router-link to="/prenotazioni" class="olom-nav-link" active-class="olom-nav-active">Prenotazioni</router-link>
      <router-link v-if="canManageUsers" to="/utenti" class="olom-nav-link" active-class="olom-nav-active">Utenti</router-link>
      <router-link v-if="isAdmin" to="/permessi" class="olom-nav-link" active-class="olom-nav-active">Permessi</router-link>
      <router-link v-if="isAdmin" to="/amenities" class="olom-nav-link" active-class="olom-nav-active">Amenities</router-link>
      <router-link v-if="isAdmin" to="/login-settings" class="olom-nav-link" active-class="olom-nav-active">Aspetto Login</router-link>
    </nav>
    <div class="olom-header-right">
      <div class="olom-user-badge">{{ initial }}</div>
      <span class="olom-user-name">{{ user.name }}</span>
      <a :href="logoutUrl" class="olom-logout">Esci</a>
    </div>
  </header>
</template>

<script setup>
const cfg = window.oloManagerConfig || {};
const user = cfg.user || {};
const p = cfg.user?.permissions || {};
const isAdmin = cfg.user?.access_level === 'admin';
const canManageUsers = p.create_users || p.delete_users || p.edit_user_profile || p.assign_services;
const logoutUrl = cfg.logoutUrl || '/gestione/?olo_logout=1';
const initial = (user.name || 'U').charAt(0).toUpperCase();
</script>
