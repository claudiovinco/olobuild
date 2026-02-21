<template>
  <div class="olom-app">
    <AppHeader />
    <main class="olom-main">
      <router-view />
    </main>
    <ThemeEditor v-if="isAdmin" />
  </div>
</template>

<script setup>
import AppHeader from './components/AppHeader.vue';
import ThemeEditor from './components/ThemeEditor.vue';

const cfg = window.oloManagerConfig || {};
const isAdmin = cfg.user?.is_admin === true;
</script>
