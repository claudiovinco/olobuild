<template>
  <div class="olo-sitemap-tile" :style="containerStyle">
    <div :style="gridStyle">
      <!-- Pagine -->
      <div v-if="s.show_pages" class="olo-sitemap-group">
        <component :is="s.title_tag" :style="titleStyle">Pagine</component>
        <ul :style="listStyle">
          <li><a :style="linkStyle" href="#">{{ t('Home') }}</a></li>
          <li><a :style="linkStyle" href="#">{{ t('Chi siamo') }}</a></li>
          <li><a :style="linkStyle" href="#">{{ t('Servizi') }}</a></li>
          <li><a :style="linkStyle" href="#">{{ t('Contatti') }}</a></li>
        </ul>
      </div>

      <!-- Articoli -->
      <div v-if="s.show_posts" class="olo-sitemap-group">
        <component :is="s.title_tag" :style="titleStyle">Articoli</component>
        <ul :style="listStyle">
          <li><a :style="linkStyle" href="#">{{ t('Primo articolo di esempio') }}</a></li>
          <li><a :style="linkStyle" href="#">{{ t('Secondo articolo di esempio') }}</a></li>
          <li><a :style="linkStyle" href="#">{{ t('Terzo articolo di esempio') }}</a></li>
        </ul>
      </div>

      <!-- Categorie -->
      <div v-if="s.show_categories" class="olo-sitemap-group">
        <component :is="s.title_tag" :style="titleStyle">Categorie</component>
        <ul :style="listStyle">
          <li><a :style="linkStyle" href="#">{{ t('Notizie') }}</a></li>
          <li><a :style="linkStyle" href="#">{{ t('Tutorial') }}</a></li>
          <li><a :style="linkStyle" href="#">{{ t('Risorse') }}</a></li>
        </ul>
      </div>

      <!-- CPT -->
      <div v-if="s.show_cpt" class="olo-sitemap-group">
        <component :is="s.title_tag" :style="titleStyle" data-olo-editable="cpt_names">{{ s.cpt_names || 'Custom Post Type' }}</component>
        <ul :style="listStyle">
          <li><a :style="linkStyle" href="#">{{ t('Elemento CPT 1') }}</a></li>
          <li><a :style="linkStyle" href="#">{{ t('Elemento CPT 2') }}</a></li>
        </ul>
      </div>

      <!-- Empty state -->
      <div
        v-if="!s.show_pages && !s.show_posts && !s.show_categories && !s.show_cpt"
        class="mb-border-2 mb-border-dashed mb-border-gray-600 mb-rounded-lg mb-p-8 mb-text-center mb-text-gray-500"
        style="grid-column: 1 / -1;"
      >
        <div class="mb-text-3xl mb-mb-2">&#128466;</div>
        <div class="mb-text-sm">{{ t('Seleziona almeno una sezione da mostrare') }}</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { t } from '@/i18n';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  show_pages: true,
  show_posts: true,
  show_cpt: false,
  cpt_names: '',
  show_categories: true,
  columns: '2',
  title_tag: 'h3',
  title_color: '',
  link_color: '',
  hover_color: '',
  list_style: 'disc',
  indent: '20',
  exclude_ids: '',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const containerStyle = computed(() => ({
  padding: '16px',
}));

const gridStyle = computed(() => ({
  display: 'grid',
  gridTemplateColumns: `repeat(${parseInt(s.value.columns) || 2}, 1fr)`,
  gap: '24px',
}));

const titleStyle = computed(() => ({
  color: s.value.title_color || 'var(--olo-color-text, #374151)',
  margin: '0 0 8px 0',
  fontSize: '1.1em',
  fontWeight: '600',
}));

const listStyle = computed(() => ({
  listStyleType: s.value.list_style || 'disc',
  paddingLeft: (parseInt(s.value.indent) || 20) + 'px',
  margin: '0',
  lineHeight: '1.8',
}));

const linkStyle = computed(() => ({
  color: s.value.link_color || 'var(--olo-color-primary, #6366F1)',
  textDecoration: 'none',
}));
</script>

<style scoped>
.olo-sitemap-group ul li a:hover {
  text-decoration: underline;
}
</style>
