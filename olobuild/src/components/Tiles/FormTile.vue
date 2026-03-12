<template>
  <div class="olo-form-preview" :style="containerStyle">
    <form @submit.prevent class="olo-form" :class="formClass">
      <!-- Multi-step indicator -->
      <div v-if="s.enable_multistep && totalSteps > 1" style="margin-bottom:16px;">
        <!-- Progress bar style -->
        <div v-if="s.step_style === 'progress'" style="display:flex;align-items:center;gap:4px;">
          <div v-for="(step, i) in totalSteps" :key="i" style="flex:1;height:4px;border-radius:2px;"
            :style="{ background: i <= currentStep ? (s.submit_bg || '#6366F1') : 'var(--olo-color-border, #E5E7EB)' }"></div>
        </div>
        <!-- Numbers style -->
        <div v-else-if="s.step_style === 'numbers'" style="display:flex;justify-content:center;gap:12px;">
          <div v-for="(step, i) in totalSteps" :key="i" style="display:flex;flex-direction:column;align-items:center;gap:4px;">
            <span style="width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;"
              :style="{ background: i <= currentStep ? (s.submit_bg || '#6366F1') : '#374151', color: '#fff' }">{{ i + 1 }}</span>
            <span style="font-size:9px;color:#9ca3af;">{{ stepLabels[i] || 'Step ' + (i+1) }}</span>
          </div>
        </div>
        <!-- Dots style -->
        <div v-else style="display:flex;justify-content:center;gap:8px;">
          <span v-for="(step, i) in totalSteps" :key="i" style="width:10px;height:10px;border-radius:50%;"
            :style="{ background: i <= currentStep ? (s.submit_bg || '#6366F1') : 'var(--olo-color-border, #E5E7EB)' }"></span>
        </div>
      </div>

      <div class="olo-form-grid" :style="gridStyle">
        <div
          v-for="(field, i) in visibleFields"
          :key="field.id || i"
          :style="fieldWidthStyle(field)"
          style="position:relative;"
        >
          <!-- Conditional logic indicator -->
          <span v-if="s.enable_conditions && field.condition_field" style="position:absolute;top:2px;right:2px;font-size:9px;color:var(--olo-color-primary, #6366F1);z-index:1;" title="Condizionale">&#9889;</span>

          <!-- Hidden -->
          <template v-if="field.field_type === 'hidden'">
            <div class="olo-form-hidden-badge">
              <span class="mb-text-xs mb-text-gray-500">Campo nascosto: {{ field.name || field.label }}</span>
            </div>
          </template>

          <!-- Text / Email / Tel / URL / Number / Date / Time -->
          <template v-else-if="isInputType(field.field_type)">
            <div class="olo-form-field">
              <label v-if="s.form_layout === 'stacked' && field.label" class="olo-form-label" :style="labelStyle">
                {{ field.label }}
                <span v-if="field.required" class="olo-form-required">*</span>
              </label>
              <div class="olo-form-input-wrap" :class="{ 'olo-form-floating': s.form_layout === 'floating' }">
                <!-- Icon wrapper -->
                <div v-if="field.icon" class="olo-form-icon-wrap">
                  <span class="olo-form-icon" :style="iconStyle">
                    <span class="dashicons" :class="'dashicons-' + mapIcon(field.icon)"></span>
                  </span>
                  <input
                    :type="field.field_type"
                    class="olo-form-input olo-form-input--icon"
                    :class="inputSizeClass"
                    :style="inputStyle"
                    :placeholder="s.form_layout === 'floating' ? ' ' : field.placeholder"
                    :required="field.required"
                    readonly
                  />
                </div>
                <!-- No icon -->
                <input
                  v-else
                  :type="field.field_type"
                  class="olo-form-input"
                  :class="inputSizeClass"
                  :style="inputStyle"
                  :placeholder="s.form_layout === 'floating' ? ' ' : field.placeholder"
                  :required="field.required"
                  readonly
                />
                <label v-if="s.form_layout === 'floating' && field.label" class="olo-form-float-label" :style="floatLabelStyle">
                  {{ field.label }}
                  <span v-if="field.required" class="olo-form-required">*</span>
                </label>
              </div>
            </div>
          </template>

          <!-- Textarea -->
          <template v-else-if="field.field_type === 'textarea'">
            <div class="olo-form-field">
              <label v-if="s.form_layout === 'stacked' && field.label" class="olo-form-label" :style="labelStyle">
                {{ field.label }}
                <span v-if="field.required" class="olo-form-required">*</span>
              </label>
              <div class="olo-form-input-wrap" :class="{ 'olo-form-floating': s.form_layout === 'floating' }">
                <textarea
                  class="olo-form-input olo-form-textarea"
                  :class="inputSizeClass"
                  :style="inputStyle"
                  :placeholder="s.form_layout === 'floating' ? ' ' : field.placeholder"
                  :required="field.required"
                  rows="4"
                  readonly
                ></textarea>
                <label v-if="s.form_layout === 'floating' && field.label" class="olo-form-float-label" :style="floatLabelStyle">
                  {{ field.label }}
                  <span v-if="field.required" class="olo-form-required">*</span>
                </label>
              </div>
            </div>
          </template>

          <!-- Select -->
          <template v-else-if="field.field_type === 'select'">
            <div class="olo-form-field">
              <label v-if="field.label" class="olo-form-label" :style="labelStyle">
                {{ field.label }}
                <span v-if="field.required" class="olo-form-required">*</span>
              </label>
              <select class="olo-form-input olo-form-select" :class="inputSizeClass" :style="inputStyle" disabled>
                <option>{{ field.placeholder || 'Seleziona...' }}</option>
                <option v-for="opt in parseOptions(field.options)" :key="opt">{{ opt }}</option>
              </select>
            </div>
          </template>

          <!-- Radio -->
          <template v-else-if="field.field_type === 'radio'">
            <div class="olo-form-field">
              <label v-if="field.label" class="olo-form-label" :style="labelStyle">
                {{ field.label }}
                <span v-if="field.required" class="olo-form-required">*</span>
              </label>
              <div class="olo-form-options">
                <label v-for="opt in parseOptions(field.options)" :key="opt" class="olo-form-option" :style="optionStyle">
                  <input type="radio" :name="'preview-radio-' + (field.id || i)" :style="checkInputStyle" disabled />
                  <span>{{ opt }}</span>
                </label>
              </div>
            </div>
          </template>

          <!-- Checkbox -->
          <template v-else-if="field.field_type === 'checkbox'">
            <div class="olo-form-field">
              <label v-if="field.label" class="olo-form-label" :style="labelStyle">
                {{ field.label }}
                <span v-if="field.required" class="olo-form-required">*</span>
              </label>
              <div class="olo-form-options">
                <label v-for="(opt, oi) in parseOptions(field.options)" :key="oi" class="olo-form-option" :style="optionStyle">
                  <input type="checkbox" :style="checkInputStyle" disabled />
                  <span>{{ opt }}</span>
                </label>
              </div>
            </div>
          </template>

          <!-- Datetime-local -->
          <template v-else-if="field.field_type === 'datetime'">
            <div class="olo-form-field">
              <label v-if="s.form_layout === 'stacked' && field.label" class="olo-form-label" :style="labelStyle">
                {{ field.label }}
                <span v-if="field.required" class="olo-form-required">*</span>
              </label>
              <input type="datetime-local" class="olo-form-input" :class="inputSizeClass" :style="inputStyle" disabled />
            </div>
          </template>

          <!-- File upload -->
          <template v-else-if="field.field_type === 'file'">
            <div class="olo-form-field">
              <label v-if="field.label" class="olo-form-label" :style="labelStyle">
                {{ field.label }}
                <span v-if="field.required" class="olo-form-required">*</span>
              </label>
              <div class="olo-form-input olo-form-file-drop" :style="{ ...inputStyle, display: 'flex', alignItems: 'center', gap: '8px', cursor: 'pointer', padding: '12px 14px' }">
                <span style="font-size:18px;">&#128206;</span>
                <span style="font-size:12px;opacity:0.7;">Scegli file... (max {{ s.file_max_size }}MB)</span>
              </div>
            </div>
          </template>

          <!-- Step separator -->
          <template v-else-if="field.field_type === 'step'">
            <div style="grid-column:1/-1;border-top:2px dashed var(--olo-color-border, #E5E7EB);margin:8px 0;position:relative;width:100%;">
              <span style="position:absolute;top:-10px;left:16px;background:var(--olo-color-background, #FFFFFF);padding:0 8px;font-size:10px;color:var(--olo-color-primary, #6366F1);font-weight:600;">STEP</span>
            </div>
          </template>
        </div>
      </div>

      <!-- Privacy checkbox -->
      <div v-if="s.privacy_checkbox" class="olo-form-privacy" :style="{ marginTop: gap + 'px' }">
        <label class="olo-form-option" :style="optionStyle">
          <input type="checkbox" :style="checkInputStyle" disabled />
          <span v-html="s.privacy_text"></span>
        </label>
      </div>

      <!-- Multi-step navigation -->
      <div v-if="s.enable_multistep && totalSteps > 1" :style="{ marginTop: (gap + 4) + 'px', display: 'flex', justifyContent: 'space-between', alignItems: 'center' }">
        <button
          v-if="currentStep > 0"
          type="button"
          class="olo-form-submit"
          :style="{ ...submitStyle, opacity: '0.8', fontSize: '13px', padding: '10px 20px' }"
          @click="currentStep--"
        >&#8592; Indietro</button>
        <span v-else></span>
        <button
          v-if="currentStep < totalSteps - 1"
          type="button"
          class="olo-form-submit"
          :style="{ ...submitStyle, fontSize: '13px', padding: '10px 20px' }"
          @click="currentStep++"
        >Avanti &#8594;</button>
      </div>

      <!-- Submit (hidden during intermediate steps in multistep) -->
      <div v-if="!s.enable_multistep || totalSteps <= 1 || currentStep === totalSteps - 1" :style="submitWrapStyle">
        <button
          type="button"
          class="olo-form-submit"
          :style="submitStyle"
          @mouseenter="submitHover = true"
          @mouseleave="submitHover = false"
        >
          <span v-if="s.submit_icon && s.submit_icon_pos !== 'right'" class="olo-form-submit-icon">{{ s.submit_icon }}</span>
          <span data-olo-editable="submit_text">{{ s.submit_text || 'Invia' }}</span>
          <span v-if="s.submit_icon && s.submit_icon_pos === 'right'" class="olo-form-submit-icon">{{ s.submit_icon }}</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  form_layout: 'stacked',
  form_max_width: '0',
  form_align: 'left',
  label_color: 'var(--olo-color-text, #374151)',
  label_size: '14',
  label_weight: '500',
  input_bg: 'var(--olo-color-background, #FFFFFF)',
  input_color: 'var(--olo-color-text, #374151)',
  input_border_color: 'var(--olo-color-border, #E5E7EB)',
  input_border_width: '1',
  input_radius: '6',
  input_size: 'default',
  gap: '16',
  submit_text: 'Invia messaggio',
  submit_icon: '',
  submit_icon_pos: 'left',
  submit_alignment: 'left',
  submit_full_width: false,
  submit_bg: '',
  submit_color: '#FFFFFF',
  submit_radius: '6',
  submit_padding_x: '32',
  submit_padding_y: '14',
  submit_font_size: '16',
  submit_font_weight: '600',
  submit_hover_bg: '',
  submit_border_width: '0',
  submit_border_color: '',
  submit_hover_border_color: '',
  submit_letter_spacing: '0.3',
  submit_text_transform: 'none',
  check_accent_color: '',
  check_bg: '',
  check_border_color: '',
  check_size: '18',
  check_label_gap: '8',
  privacy_checkbox: false,
  privacy_text: '',
  enable_multistep: false,
  step_style: 'progress',
  step_labels: '',
  enable_conditions: false,
  file_max_size: '5',
  file_types: '.pdf,.doc,.docx,.jpg,.png',
  store_submissions: false,
};
const s = computed(() => ({ ...defaults, ...props.settings }));
const submitHover = ref(false);

const formFields = computed(() => {
  const raw = s.value.fields;
  if (Array.isArray(raw)) return raw;
  return [];
});

// Multi-step
const currentStep = ref(0);

const steps = computed(() => {
  if (!s.value.enable_multistep) return [formFields.value];
  const result = [[]];
  for (const f of formFields.value) {
    if (f.field_type === 'step') {
      result.push([]);
    } else {
      result[result.length - 1].push(f);
    }
  }
  return result.filter(st => st.length > 0);
});

const stepLabels = computed(() => {
  const raw = s.value.step_labels || '';
  return raw.split('\n').map(l => l.trim()).filter(Boolean);
});

const totalSteps = computed(() => steps.value.length);

const visibleFields = computed(() => {
  if (!s.value.enable_multistep || totalSteps.value <= 1) return formFields.value;
  const idx = Math.min(currentStep.value, totalSteps.value - 1);
  return steps.value[idx] || [];
});

const gap = computed(() => parseInt(s.value.gap) || 16);

function isInputType(t) {
  return ['text', 'email', 'tel', 'url', 'number', 'date', 'time'].includes(t);
}

function parseOptions(opts) {
  if (!opts) return ['Opzione 1', 'Opzione 2'];
  return opts.split('\n').map(o => o.trim()).filter(Boolean);
}

function mapIcon(icon) {
  const map = { user: 'admin-users', mail: 'email', phone: 'phone', lock: 'lock', search: 'search', calendar: 'calendar-alt', link: 'admin-links', tag: 'tag', location: 'location', star: 'star-filled' };
  return map[icon] || 'marker';
}

function fieldWidthStyle(field) {
  const map = { '1-1': '100%', '1-2': 'calc(50% - ' + gap.value/2 + 'px)', '1-3': 'calc(33.33% - ' + gap.value*2/3 + 'px)', '2-3': 'calc(66.66% - ' + gap.value/3 + 'px)', '1-4': 'calc(25% - ' + gap.value*3/4 + 'px)', '3-4': 'calc(75% - ' + gap.value/4 + 'px)' };
  return { width: map[field.width] || '100%' };
}

const containerStyle = computed(() => {
  const style = { minHeight: '120px' };
  const maxW = parseInt(s.value.form_max_width) || 0;
  if (maxW > 0) {
    style.maxWidth = maxW + 'px';
    const align = s.value.form_align || 'left';
    if (align === 'center') { style.marginLeft = 'auto'; style.marginRight = 'auto'; }
    else if (align === 'right') { style.marginLeft = 'auto'; }
  }
  return style;
});

const formClass = computed(() => {
  return s.value.form_layout === 'floating' ? 'olo-form--floating' : 'olo-form--stacked';
});

const gridStyle = computed(() => ({
  display: 'flex',
  flexWrap: 'wrap',
  gap: gap.value + 'px',
}));

const labelStyle = computed(() => ({
  color: s.value.label_color || 'var(--olo-color-text, #374151)',
  fontSize: (parseInt(s.value.label_size) || 14) + 'px',
  fontWeight: s.value.label_weight || '500',
  marginBottom: '6px',
  display: 'block',
}));

const floatLabelStyle = computed(() => ({
  color: s.value.label_color || 'var(--olo-color-text, #374151)',
  fontSize: (parseInt(s.value.label_size) || 14) + 'px',
}));

const inputStyle = computed(() => {
  const bw = parseInt(s.value.input_border_width) || 1;
  return {
    backgroundColor: s.value.input_bg || 'var(--olo-color-background, #FFFFFF)',
    color: s.value.input_color || 'var(--olo-color-text, #374151)',
    border: bw + 'px solid ' + (s.value.input_border_color || 'var(--olo-color-border, #E5E7EB)'),
    borderRadius: ((v => isNaN(v) ? 6 : v)(parseInt(s.value.input_radius))) + 'px',
    width: '100%',
    boxSizing: 'border-box',
  };
});

const inputSizeClass = computed(() => {
  const size = s.value.input_size || 'default';
  if (size === 'small') return 'olo-form-input--small';
  if (size === 'large') return 'olo-form-input--large';
  return '';
});

const iconStyle = computed(() => ({
  color: s.value.input_color || 'var(--olo-color-text, #374151)',
  opacity: '0.5',
}));

const optionStyle = computed(() => ({
  color: s.value.label_color || 'var(--olo-color-text, #374151)',
  fontSize: (parseInt(s.value.label_size) || 14) + 'px',
  gap: (parseInt(s.value.check_label_gap) || 8) + 'px',
}));

const checkInputStyle = computed(() => {
  const size = parseInt(s.value.check_size) || 18;
  const style = {
    width: size + 'px',
    height: size + 'px',
    flexShrink: '0',
  };
  if (s.value.check_accent_color) style.accentColor = s.value.check_accent_color;
  if (s.value.check_bg) style.backgroundColor = s.value.check_bg;
  if (s.value.check_border_color) style.borderColor = s.value.check_border_color;
  return style;
});

const submitWrapStyle = computed(() => ({
  marginTop: (gap.value + 4) + 'px',
  textAlign: s.value.submit_alignment || 'left',
}));

const submitStyle = computed(() => {
  const bw = parseInt(s.value.submit_border_width) || 0;
  const borderColor = bw > 0 ? (s.value.submit_border_color || 'var(--olo-color-primary, #6366F1)') : 'transparent';
  const hoverBorderColor = bw > 0 && s.value.submit_hover_border_color ? s.value.submit_hover_border_color : borderColor;
  const ls = parseFloat(s.value.submit_letter_spacing) || 0;
  const tt = s.value.submit_text_transform || 'none';

  return {
    backgroundColor: submitHover.value
      ? (s.value.submit_hover_bg || 'var(--olo-color-primary-dark, #4F46E5)')
      : (s.value.submit_bg || 'var(--olo-color-primary, #6366F1)'),
    color: s.value.submit_color || '#FFFFFF',
    borderRadius: ((v => isNaN(v) ? 6 : v)(parseInt(s.value.submit_radius))) + 'px',
    padding: (parseInt(s.value.submit_padding_y) || 14) + 'px ' + (parseInt(s.value.submit_padding_x) || 32) + 'px',
    fontSize: (parseInt(s.value.submit_font_size) || 16) + 'px',
    fontWeight: s.value.submit_font_weight || '600',
    width: s.value.submit_full_width ? '100%' : 'auto',
    border: bw > 0 ? bw + 'px solid ' + (submitHover.value ? hoverBorderColor : borderColor) : 'none',
    letterSpacing: ls > 0 ? ls + 'px' : 'normal',
    textTransform: tt !== 'none' ? tt : 'none',
    cursor: 'pointer',
    transition: 'background-color 0.2s ease, border-color 0.2s ease',
    display: 'inline-flex',
    alignItems: 'center',
    gap: '8px',
  };
});
</script>

<style scoped>
.olo-form-preview {
  padding: 4px;
}

.olo-form-field {
  display: flex;
  flex-direction: column;
}

.olo-form-required {
  color: #EF4444;
  margin-left: 2px;
}

.olo-form-input {
  padding: 10px 14px;
  font-size: 14px;
  line-height: 1.5;
  outline: none;
  font-family: inherit;
}

.olo-form-input--small {
  padding: 7px 10px;
  font-size: 13px;
}

.olo-form-input--large {
  padding: 14px 18px;
  font-size: 16px;
}

.olo-form-input--icon {
  padding-left: 38px;
}

.olo-form-textarea {
  resize: vertical;
  min-height: 100px;
}

.olo-form-select {
  appearance: auto;
}

.olo-form-icon-wrap {
  position: relative;
  width: 100%;
}

.olo-form-icon {
  position: absolute;
  left: 10px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 16px;
  pointer-events: none;
  z-index: 1;
}

.olo-form-options {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-top: 4px;
}

.olo-form-option {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
}

.olo-form-option input {
  margin: 0;
}

.olo-form-hidden-badge {
  padding: 8px 12px;
  border: 1px dashed var(--olo-color-border, #E5E7EB);
  border-radius: 6px;
  text-align: center;
  background: rgba(75, 85, 99, 0.1);
}

.olo-form-privacy {
  padding-top: 4px;
}

.olo-form-privacy :deep(a) {
  color: var(--olo-color-primary, #6366F1);
  text-decoration: underline;
}

/* Floating label */
.olo-form-floating {
  position: relative;
}

.olo-form-float-label {
  position: absolute;
  top: 50%;
  left: 14px;
  transform: translateY(-50%);
  pointer-events: none;
  transition: all 0.2s ease;
  opacity: 0.6;
}

.olo-form-floating .olo-form-textarea ~ .olo-form-float-label {
  top: 14px;
  transform: none;
}

.olo-form-floating .olo-form-input:focus ~ .olo-form-float-label,
.olo-form-floating .olo-form-input:not(:placeholder-shown) ~ .olo-form-float-label {
  top: 4px;
  left: 12px;
  font-size: 11px !important;
  opacity: 1;
  transform: none;
}

.olo-form-submit {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  text-align: center;
  text-decoration: none;
}

.olo-form-submit-icon {
  font-size: 11px;
  opacity: 0.7;
}
</style>
