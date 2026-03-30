<template>
  <div class="olo-cfg">
    <div class="olo-cfg-layout">
      <!-- Sidebar -->
      <nav class="olo-cfg-nav">
        <div class="olo-cfg-nav-group">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            :class="['olo-cfg-nav-item', { active: activeTab === tab.id }]"
            @click="activeTab = tab.id"
          >
            <span class="olo-cfg-nav-dot" v-html="tab.icon"></span>
            <span class="olo-cfg-nav-label">{{ tab.label }}</span>
            <span v-if="activeTab === tab.id" class="olo-cfg-nav-arrow">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
            </span>
          </button>
        </div>
      </nav>

      <!-- Content -->
      <div class="olo-cfg-main">
        <div v-show="activeTab === 'styles'"><StylesTab /></div>
        <div v-show="activeTab === 'colors'"><ColorsTab /></div>
        <div v-show="activeTab === 'typography'"><TypographyTab /></div>
        <div v-show="activeTab === 'ai'"><AITab /></div>
        <div v-show="activeTab === 'api'"><ApiKeysTab /></div>
        <div v-show="activeTab === 'responsive'"><BreakpointsTab /></div>
      </div>
    </div>

    <!-- Toast -->
    <Transition name="olo-toast">
      <div v-if="toast" class="olo-cfg-toast" :class="toast.type">
        <svg v-if="toast.type === 'success'" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
        <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
        {{ toast.message }}
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, provide } from 'vue';
import StylesTab from './StylesTab.vue';
import ColorsTab from './ColorsTab.vue';
import TypographyTab from './TypographyTab.vue';
import AITab from './AITab.vue';
import ApiKeysTab from './ApiKeysTab.vue';
import BreakpointsTab from './BreakpointsTab.vue';

const activeTab = ref('styles');
const toast = ref(null);
const version = window.oloData?.version || '';

function showToast(message, type = 'success') {
  toast.value = { message, type };
  setTimeout(() => toast.value = null, 2500);
}

provide('showToast', showToast);

const tabs = [
  { id: 'styles', label: 'Stili', icon: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>' },
  { id: 'colors', label: 'Colori', icon: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/></svg>' },
  { id: 'typography', label: 'Tipografia', icon: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="4 7 4 4 20 4 20 7"/><line x1="9" y1="20" x2="15" y2="20"/><line x1="12" y1="4" x2="12" y2="20"/></svg>' },
  { id: 'ai', label: 'AI Assistant', icon: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a3 3 0 013 3v1a3 3 0 01-6 0V5a3 3 0 013-3z"/><rect x="4" y="13" width="16" height="7" rx="3"/><circle cx="9" cy="17" r="1" fill="currentColor"/><circle cx="15" cy="17" r="1" fill="currentColor"/></svg>' },
  { id: 'api', label: 'API & Integrazioni', icon: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>' },
  { id: 'responsive', label: 'Responsive', icon: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="12" x="3" y="4" rx="2"/><line x1="8" x2="16" y1="20" y2="20"/><line x1="12" x2="12" y1="16" y2="20"/></svg>' },
];
</script>

<style>
/* ═══════════════════════════════════════════
   Dashboard Design System — warm, minimal
   ═══════════════════════════════════════════ */

/* ── Root ── */
.olo-cfg {
  max-width: 1160px;
  margin: 0;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  color: #1a1a1a;
  -webkit-font-smoothing: antialiased;
}

/* ── Layout ── */
.olo-cfg-layout {
  display: flex;
  gap: 28px;
  min-height: 520px;
}

/* ── Sidebar Nav ── */
.olo-cfg-nav {
  width: 210px;
  flex-shrink: 0;
}

.olo-cfg-nav-group {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.olo-cfg-nav-item {
  display: flex;
  align-items: center;
  gap: 10px;
  width: 100%;
  padding: 10px 14px;
  border: none;
  background: none;
  border-radius: 10px;
  color: #888;
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.15s ease;
  text-align: left;
  white-space: nowrap;
  position: relative;
}

.olo-cfg-nav-item:hover {
  background: #f5f5f5;
  color: #1a1a1a;
}

.olo-cfg-nav-item.active {
  background: #1a1a1a;
  color: #fff;
}

.olo-cfg-nav-dot {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 18px;
  flex-shrink: 0;
}

.olo-cfg-nav-label {
  flex: 1;
  overflow: hidden;
  text-overflow: ellipsis;
}

.olo-cfg-nav-arrow {
  display: flex;
  opacity: 0.5;
}

/* ── Main Content ── */
.olo-cfg-main {
  flex: 1;
  min-width: 0;
  background: #fff;
  border: 1px solid #e8e8e8;
  border-radius: 16px;
  padding: 32px 36px;
  box-shadow: 0 1px 2px rgba(0,0,0,0.03);
}

/* ── Toast ── */
.olo-cfg-toast {
  position: fixed;
  bottom: 32px;
  right: 32px;
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 22px;
  border-radius: 12px;
  font-size: 13px;
  font-weight: 600;
  box-shadow: 0 8px 30px rgba(0,0,0,0.12);
  z-index: 99999;
}
.olo-cfg-toast.success { background: #1a1a1a; color: #fff; }
.olo-cfg-toast.error { background: #e84430; color: #fff; }

.olo-toast-enter-active { animation: olo-toast-in .3s ease; }
.olo-toast-leave-active { animation: olo-toast-in .2s ease reverse; }
@keyframes olo-toast-in {
  from { opacity: 0; transform: translateY(12px) scale(0.95); }
  to { opacity: 1; transform: translateY(0) scale(1); }
}

/* ── Responsive ── */
@media (max-width: 768px) {
  .olo-cfg-layout {
    flex-direction: column;
  }
  .olo-cfg-nav {
    width: 100%;
  }
  .olo-cfg-nav-group {
    flex-direction: row;
    overflow-x: auto;
    gap: 0;
    padding-bottom: 8px;
    border-bottom: 1px solid #e8e8e8;
  }
  .olo-cfg-nav-item {
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 12px;
  }
  .olo-cfg-nav-arrow { display: none; }
  .olo-cfg-main {
    padding: 20px;
    border-radius: 12px;
  }
}
</style>
