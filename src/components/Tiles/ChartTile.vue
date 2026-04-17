<template>
  <div
    class="olo-chart-preview"
    :style="{ background: s.bg_color || 'transparent', minHeight: '120px', borderRadius: '8px', padding: '12px' }"
  >
    <canvas ref="canvasRef" :style="{ width: '100%', height: chartH + 'px', maxHeight: chartH + 'px' }"></canvas>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount, nextTick } from 'vue';
// Tree-shake chart.js: import only needed modules instead of all registerables (~200KB savings)
import {
  Chart,
  BarController, LineController, PieController, DoughnutController, RadarController, PolarAreaController,
  CategoryScale, LinearScale, RadialLinearScale,
  BarElement, LineElement, PointElement, ArcElement,
  Title, Tooltip, Legend, Filler
} from 'chart.js';

Chart.register(
  BarController, LineController, PieController, DoughnutController, RadarController, PolarAreaController,
  CategoryScale, LinearScale, RadialLinearScale,
  BarElement, LineElement, PointElement, ArcElement,
  Title, Tooltip, Legend, Filler
);

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  chart_type: 'bar',
  items: [
    { id: 'c-1', label: 'Gen', value: '65', color: '#6366F1' },
    { id: 'c-2', label: 'Feb', value: '45', color: '#8B5CF6' },
    { id: 'c-3', label: 'Mar', value: '80', color: '#A78BFA' },
    { id: 'c-4', label: 'Apr', value: '55', color: '#C4B5FD' },
  ],
  chart_height: '300',
  show_legend: true,
  legend_position: 'bottom',
  legend_align: 'center',
  legend_color: '',
  legend_font_size: '12',
  legend_font_weight: '400',
  legend_box_width: '40',
  legend_padding: '10',
  legend_point_style: false,
  show_title: false,
  chart_title: '',
  title_color: '',
  title_font_size: '16',
  title_font_weight: '700',
  title_padding: '10',
  show_subtitle: false,
  chart_subtitle: '',
  subtitle_color: '',
  subtitle_font_size: '12',
  tooltip_enabled: true,
  tooltip_bg: '#000000',
  tooltip_text_color: '#ffffff',
  tooltip_border_color: '',
  tooltip_border_width: '0',
  tooltip_corner_radius: '6',
  tooltip_font_size: '12',
  tooltip_padding: '8',
  animate: false,
  bg_color: 'transparent',
  border_width: '2',
  border_color_override: '',
  bar_radius: '0',
  bar_percentage: '0.8',
  category_percentage: '0.8',
  fill_area: false,
  point_radius: '4',
  point_hover_radius: '6',
  point_style: 'circle',
  tension: '0.4',
  doughnut_cutout: '50',
  grid_color: '#374151',
  grid_line_width: '1',
  axis_color: '',
  text_color: '#9CA3AF',
  tick_font_size: '11',
  show_x_grid: true,
  show_y_grid: true,
  show_x_border: true,
  show_y_border: true,
  begin_at_zero: true,
  y_min: '',
  y_max: '',
  y_step_size: '',
  index_axis: 'x',
  dataset_label: '',
  stacked: false,
  stepped_line: '',
  x_label: '',
  y_label: '',
  tooltip_prefix: '',
  tooltip_suffix: '',
  number_format: false,
};

const s = computed(() => ({ ...defaults, ...props.settings }));
const chartH = computed(() => Math.min(parseInt(s.value.chart_height) || 300, 300));

const canvasRef = ref(null);
let chartInstance = null;

function buildConfig() {
  const items = Array.isArray(s.value.items) ? s.value.items : defaults.items;
  const labels = items.map(i => i.label || '');
  const values = items.map(i => parseFloat(i.value) || 0);
  const bgColors = items.map(i => i.color || '#6366F1');
  const borderOverride = s.value.border_color_override || '';
  const borderColors = items.map(i => borderOverride || i.border_color || i.color || '#6366F1');
  const chartType = s.value.chart_type || 'bar';
  const hasGrid = ['bar', 'line'].includes(chartType);
  const textColor = s.value.text_color || '#9CA3AF';
  const gridColor = s.value.grid_color || '#374151';
  const axisColor = s.value.axis_color || gridColor;

  const dataset = {
    data: values,
    backgroundColor: bgColors,
    borderColor: borderColors,
    borderWidth: parseInt(s.value.border_width) || 2,
  };

  // Bar-specific
  if (chartType === 'bar') {
    dataset.borderRadius = parseInt(s.value.bar_radius) || 0;
    dataset.barPercentage = parseFloat(s.value.bar_percentage) || 0.8;
    dataset.categoryPercentage = parseFloat(s.value.category_percentage) || 0.8;
  }

  // Line-specific
  if (chartType === 'line') {
    dataset.fill = !!s.value.fill_area;
    dataset.tension = parseFloat(s.value.tension) || 0;
    dataset.pointBackgroundColor = bgColors;
    dataset.pointRadius = (v => isNaN(v) ? 4 : v)(parseInt(s.value.point_radius));
    dataset.pointHoverRadius = (v => isNaN(v) ? 6 : v)(parseInt(s.value.point_hover_radius));
    dataset.pointStyle = s.value.point_style || 'circle';
    if (s.value.stepped_line) {
      dataset.stepped = s.value.stepped_line;
    }
  }

  // Radar-specific
  if (chartType === 'radar') {
    dataset.fill = !!s.value.fill_area;
    dataset.tension = parseFloat(s.value.tension) || 0;
    dataset.pointRadius = (v => isNaN(v) ? 4 : v)(parseInt(s.value.point_radius));
    dataset.pointHoverRadius = (v => isNaN(v) ? 6 : v)(parseInt(s.value.point_hover_radius));
    dataset.pointStyle = s.value.point_style || 'circle';
    dataset.pointBackgroundColor = bgColors;
  }

  if (['bar', 'line', 'radar'].includes(chartType)) {
    dataset.label = s.value.dataset_label || '';
  }

  // Doughnut cutout
  const extraOptions = {};
  if (chartType === 'doughnut') {
    extraOptions.cutout = s.value.doughnut_cutout + '%';
  }

  // Legend config
  const legendLabels = {
    color: s.value.legend_color || textColor,
    font: {
      size: parseInt(s.value.legend_font_size) || 12,
      weight: s.value.legend_font_weight || '400',
    },
    boxWidth: parseInt(s.value.legend_box_width) || 40,
    padding: parseInt(s.value.legend_padding) || 10,
  };
  if (s.value.legend_point_style) {
    legendLabels.usePointStyle = true;
    legendLabels.pointStyleWidth = 12;
  }

  // Tooltip config
  const tooltip = {
    enabled: s.value.tooltip_enabled !== false,
    backgroundColor: s.value.tooltip_bg || '#000000',
    titleColor: s.value.tooltip_text_color || '#ffffff',
    bodyColor: s.value.tooltip_text_color || '#ffffff',
    borderColor: s.value.tooltip_border_color || 'transparent',
    borderWidth: parseInt(s.value.tooltip_border_width) || 0,
    cornerRadius: (v => isNaN(v) ? 6 : v)(parseInt(s.value.tooltip_corner_radius)),
    titleFont: { size: parseInt(s.value.tooltip_font_size) || 12 },
    bodyFont: { size: parseInt(s.value.tooltip_font_size) || 12 },
    padding: parseInt(s.value.tooltip_padding) || 8,
  };
  const ttPrefix = s.value.tooltip_prefix || '';
  const ttSuffix = s.value.tooltip_suffix || '';
  const numFmt = !!s.value.number_format;
  if (ttPrefix || ttSuffix || numFmt) {
    tooltip.callbacks = {
      label(ctx) {
        let v = ctx.parsed.y !== undefined ? ctx.parsed.y : ctx.parsed;
        if (numFmt) v = v.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        const lbl = ctx.dataset.label ? ctx.dataset.label + ': ' : '';
        return lbl + ttPrefix + v + ttSuffix;
      },
    };
  }

  // Title config
  const titleCfg = {
    display: !!(s.value.show_title && s.value.chart_title),
    text: s.value.chart_title || '',
    color: s.value.title_color || textColor,
    font: {
      size: parseInt(s.value.title_font_size) || 16,
      weight: s.value.title_font_weight || '700',
    },
    padding: { bottom: parseInt(s.value.title_padding) || 10 },
  };

  // Subtitle config
  const subtitleCfg = {
    display: !!(s.value.show_subtitle && s.value.chart_subtitle),
    text: s.value.chart_subtitle || '',
    color: s.value.subtitle_color || textColor,
    font: { size: parseInt(s.value.subtitle_font_size) || 12 },
  };

  const isStacked = !!s.value.stacked;

  // Y axis config
  const yConfig = {
    ticks: {
      color: textColor,
      font: { size: parseInt(s.value.tick_font_size) || 11 },
    },
    grid: {
      display: s.value.show_y_grid !== false,
      color: gridColor,
      lineWidth: parseFloat(s.value.grid_line_width) || 1,
    },
    border: {
      display: s.value.show_y_border !== false,
      color: axisColor,
    },
    beginAtZero: s.value.begin_at_zero !== false,
  };
  if (isStacked) yConfig.stacked = true;
  if (s.value.y_min !== '') yConfig.min = parseFloat(s.value.y_min);
  if (s.value.y_max !== '') yConfig.max = parseFloat(s.value.y_max);
  if (s.value.y_step_size !== '') yConfig.ticks.stepSize = parseFloat(s.value.y_step_size);
  if (s.value.y_label) yConfig.title = { display: true, text: s.value.y_label, color: textColor };

  // X axis config
  const xConfig = {
    ticks: {
      color: textColor,
      font: { size: parseInt(s.value.tick_font_size) || 11 },
    },
    grid: {
      display: s.value.show_x_grid !== false,
      color: gridColor,
      lineWidth: parseFloat(s.value.grid_line_width) || 1,
    },
    border: {
      display: s.value.show_x_border !== false,
      color: axisColor,
    },
  };
  if (isStacked) xConfig.stacked = true;
  if (s.value.x_label) xConfig.title = { display: true, text: s.value.x_label, color: textColor };

  return {
    type: chartType,
    data: { labels, datasets: [dataset] },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      animation: false,
      indexAxis: chartType === 'bar' ? (s.value.index_axis || 'x') : undefined,
      ...extraOptions,
      plugins: {
        legend: {
          display: !!s.value.show_legend,
          position: s.value.legend_position || 'bottom',
          align: s.value.legend_align || 'center',
          labels: legendLabels,
        },
        title: titleCfg,
        subtitle: subtitleCfg,
        tooltip,
      },
      ...(hasGrid ? { scales: { x: xConfig, y: yConfig } } : {}),
      ...(chartType === 'radar' ? {
        scales: {
          r: {
            ticks: { color: textColor, font: { size: parseInt(s.value.tick_font_size) || 9 }, backdropColor: 'transparent' },
            grid: { color: gridColor, lineWidth: parseFloat(s.value.grid_line_width) || 1 },
            angleLines: { color: gridColor },
            pointLabels: { color: textColor, font: { size: parseInt(s.value.tick_font_size) || 10 } },
          },
        },
      } : {}),
    },
  };
}

function renderChart() {
  if (!canvasRef.value) return;
  if (chartInstance) {
    chartInstance.destroy();
    chartInstance = null;
  }
  chartInstance = new Chart(canvasRef.value.getContext('2d'), buildConfig());
}

onMounted(() => {
  nextTick(() => renderChart());
});

onBeforeUnmount(() => {
  if (chartInstance) {
    chartInstance.destroy();
    chartInstance = null;
  }
});

watch(s, () => {
  nextTick(() => renderChart());
}, { deep: true });
</script>
