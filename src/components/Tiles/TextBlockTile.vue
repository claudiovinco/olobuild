<template>
  <div class="olo-textblock-preview" :style="wrapStyle">
    <div
      :style="contentStyle"
      data-olo-editable="content"
      data-olo-richtext
      data-olo-multiline
      v-html="contentHtml"
    ></div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  content: '<p>Scrivi qui il tuo testo. Puoi formattare con <strong>grassetto</strong>, <em>corsivo</em>, elenchi, titoli e molto altro.</p>',
  text_color: '',
  font_size: '',
  line_height: '',
  max_width: '',
  padding: '16',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const contentHtml = computed(() => {
  return s.value.content || '<span class="olo-editable-ph">Scrivi qui il tuo testo...</span>';
});

const wrapStyle = computed(() => {
  const style = {};
  const pad = parseInt(s.value.padding) || 16;
  style.padding = pad + 'px';

  const mw = parseInt(s.value.max_width);
  if (mw > 0) style.maxWidth = mw + 'px';

  return style;
});

const contentStyle = computed(() => {
  const style = {};

  if (s.value.text_color) style.color = s.value.text_color;

  const fs = parseInt(s.value.font_size);
  if (fs > 0) style.fontSize = fs + 'px';

  if (s.value.line_height) style.lineHeight = s.value.line_height;

  return style;
});
</script>

<style scoped>
.olo-textblock-preview {
  font-size: 13px;
  line-height: 1.6;
}
.olo-textblock-preview :deep(h1),
.olo-textblock-preview :deep(h2),
.olo-textblock-preview :deep(h3),
.olo-textblock-preview :deep(h4),
.olo-textblock-preview :deep(h5),
.olo-textblock-preview :deep(h6) {
  margin: 0.8em 0 0.4em;
  font-weight: bold;
}
.olo-textblock-preview :deep(h1) { font-size: 2em; }
.olo-textblock-preview :deep(h2) { font-size: 1.5em; }
.olo-textblock-preview :deep(h3) { font-size: 1.25em; }
.olo-textblock-preview :deep(p) {
  margin: 0 0 0.8em;
}
.olo-textblock-preview :deep(ul) {
  list-style: disc outside;
  margin: 0 0 0.8em;
  padding-left: 1.5em;
}
.olo-textblock-preview :deep(ol) {
  list-style: decimal outside;
  margin: 0 0 0.8em;
  padding-left: 1.5em;
}
.olo-textblock-preview :deep(ul ul) { list-style: circle outside; }
.olo-textblock-preview :deep(ul ul ul) { list-style: square outside; }
.olo-textblock-preview :deep(ol ol) { list-style: lower-alpha outside; }
.olo-textblock-preview :deep(ol ol ol) { list-style: lower-roman outside; }
.olo-textblock-preview :deep(li) { margin: 0.2em 0; }
.olo-textblock-preview :deep(ul),
.olo-textblock-preview :deep(ol),
.olo-textblock-preview :deep(li) {
  font-family: inherit;
  font-size: inherit;
  line-height: inherit;
  color: inherit;
  letter-spacing: inherit;
}
.olo-textblock-preview :deep(li > p) { margin: 0; }
.olo-textblock-preview :deep(li > p + p) { margin-top: 0.4em; }
.olo-textblock-preview :deep(blockquote) {
  margin: 0.8em 0;
  padding-left: 1em;
  border-left: 3px solid rgba(255,255,255,0.3);
  opacity: 0.85;
}
.olo-textblock-preview :deep(a) {
  color: var(--olo-color-primary, #3b82f6);
  text-decoration: underline;
}
</style>
