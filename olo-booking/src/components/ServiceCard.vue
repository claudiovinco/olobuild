<template>
  <router-link :to="'/struttura/' + service.id" class="olom-svc-card">
    <div class="olom-svc-card-img" :style="imgStyle">
      <span v-if="!service.image" class="olom-svc-card-placeholder">
        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 21h18L17 8l-4 6-3-3-7 10z"/>
          <circle cx="8.5" cy="7.5" r="2"/>
        </svg>
      </span>
    </div>
    <div class="olom-svc-card-body">
      <h3 class="olom-svc-card-title">{{ service.title }}</h3>
      <div class="olom-svc-card-meta">
        <span v-if="service.capacity">{{ service.capacity }} ospiti</span>
        <span v-if="service.bedrooms">{{ service.bedrooms }} camere</span>
        <span v-if="service.price" class="olom-svc-card-price">&euro;{{ service.price }}/notte</span>
      </div>
      <div class="olom-svc-card-badges" v-if="service.pending || service.active_bookings || service.mushrooms">
        <span v-if="service.pending" class="olom-badge olom-badge-warn">{{ service.pending }} in attesa</span>
        <span v-if="service.active_bookings" class="olom-badge olom-badge-info">{{ service.active_bookings }} attive</span>
        <span v-if="service.mushrooms" class="olom-badge olom-badge-mush">{{ '🍄'.repeat(service.mushrooms) }}</span>
      </div>
    </div>
  </router-link>
</template>

<script setup>
import { computed } from 'vue';
const props = defineProps({ service: Object });
const imgStyle = computed(() => {
  if (props.service.image) return { backgroundImage: `url(${props.service.image})` };
  return { background: `linear-gradient(135deg, ${props.service.color || '#818cf8'}22, ${props.service.color || '#818cf8'}08)` };
});
</script>
