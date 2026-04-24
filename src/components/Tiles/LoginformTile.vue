<template>
  <div class="olo-loginform-preview" :style="wrapStyle">

    <!-- State toggle (builder only) -->
    <div style="display:flex;justify-content:flex-end;margin-bottom:8px;">
      <button type="button" @click="showLoggedIn = !showLoggedIn" style="font-size:10px;padding:2px 8px;border-radius:4px;border:1px solid var(--olo-color-border, #E5E7EB);background:transparent;color:var(--olo-color-text-muted, #9CA3AF);cursor:pointer;">
        {{ showLoggedIn ? 'Mostra form' : 'Mostra logged-in' }}
      </button>
    </div>

    <!-- ═══ Logged-in state ═══ -->
    <div v-if="showLoggedIn" style="text-align:center;padding:20px 0;">
      <div v-if="s.show_avatar" style="width:64px;height:64px;border-radius:50%;background:var(--olo-color-muted, #F3F4F6);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#9CA3AF" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </div>
      <div style="font-weight:700;font-size:17px;" :style="{ color: s.text_color || 'var(--olo-color-text, #374151)' }">
        <span data-olo-editable="logged_in_message">{{ s.logged_in_message || 'Bentornato!' }}</span>
      </div>
      <div style="font-size:14px;opacity:0.6;margin-top:4px;" :style="{ color: s.text_color || 'var(--olo-color-text-muted, #9CA3AF)' }">{{ t('Mario Rossi') }}</div>
      <a href="#" style="font-size:13px;margin-top:16px;display:inline-block;text-decoration:none;font-weight:500;" :style="{ color: linkColor }">{{ t('Esci') }}</a>
    </div>

    <!-- ═══ Form state ═══ -->
    <template v-if="!showLoggedIn">
      <!-- Tabs -->
      <div v-if="s.mode === 'both'" :style="tabsWrapStyle">
        <button
          v-for="tab in tabs"
          :key="tab.key"
          type="button"
          :style="activeTab === tab.key ? tabActiveStyle : tabInactiveStyle"
          @click="activeTab = tab.key"
        >{{ tab.label }}</button>
      </div>

      <!-- Header -->
      <div style="text-align:center;margin-bottom:24px;">
        <div style="font-size:22px;font-weight:700;line-height:1.3;" :style="{ color: s.text_color || 'var(--olo-color-text, #1F2937)' }">
          <span :data-olo-editable="showLogin ? 'login_title' : 'register_title'">{{ showLogin ? (s.login_title || 'Bentornato') : (s.register_title || 'Crea un account') }}</span>
        </div>
        <div v-if="currentSubtitle" style="font-size:14px;margin-top:6px;opacity:0.6;" :style="{ color: s.text_color || 'var(--olo-color-text-muted, #9CA3AF)' }">
          <span :data-olo-editable="showLogin ? 'login_subtitle' : 'register_subtitle'">{{ currentSubtitle }}</span>
        </div>
      </div>

      <!-- Social buttons -->
      <div v-if="s.show_social_divider && hasSocial" style="margin-bottom:20px;">
        <div style="display:flex;flex-direction:column;gap:10px;">
          <button v-if="s.social_google" type="button" :style="socialBtnStyle">
            <svg width="18" height="18" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18A10.96 10.96 0 0 0 1 12c0 1.77.42 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
            <span>{{ t('Continua con Google') }}</span>
          </button>
          <button v-if="s.social_facebook" type="button" :style="socialBtnStyle">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            <span>{{ t('Continua con Facebook') }}</span>
          </button>
          <button v-if="s.social_apple" type="button" :style="{ ...socialBtnStyle, backgroundColor: '#000', color: '#FFF', borderColor: '#000' }">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#FFF"><path d="M17.05 20.28c-.98.95-2.05.88-3.08.4-1.09-.5-2.08-.48-3.24 0-1.44.62-2.2.44-3.06-.4C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/></svg>
            <span>{{ t('Continua con Apple') }}</span>
          </button>
        </div>
        <div style="display:flex;align-items:center;gap:12px;margin-top:20px;">
          <div :style="dividerLineStyle"></div>
          <span style="font-size:12px;white-space:nowrap;opacity:0.5;" :style="{ color: s.text_color || 'var(--olo-color-text-muted, #9CA3AF)' }">{{ s.social_divider_text || 'oppure' }}</span>
          <div :style="dividerLineStyle"></div>
        </div>
      </div>

      <!-- ═══ Login form ═══ -->
      <form v-if="showLogin" @submit.prevent>
        <!-- Username/Email -->
        <div style="margin-bottom:16px;">
          <label v-if="true" :style="labelStyle">{{ t('Nome utente o email') }}</label>
          <div :style="inputWrapStyle">
            <span v-if="s.show_input_icons" :style="inputIconStyle">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </span>
            <input type="text" :placeholder="t('nome@esempio.it')" :style="inputFieldStyle" disabled />
          </div>
        </div>
        <!-- Password -->
        <div style="margin-bottom:16px;">
          <label :style="labelStyle">{{ t('Password') }}</label>
          <div :style="inputWrapStyle">
            <span v-if="s.show_input_icons" :style="inputIconStyle">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            </span>
            <input :type="showPwLogin ? 'text' : 'password'" :placeholder="t('La tua password')" :style="inputFieldStyle" disabled />
            <button v-if="s.show_password_toggle" type="button" @click="showPwLogin = !showPwLogin" :style="pwToggleStyle">
              <svg v-if="!showPwLogin" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
            </button>
          </div>
        </div>
        <!-- Remember + Lost password row -->
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
          <label v-if="s.show_remember_me" style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;" :style="{ color: s.text_color || 'var(--olo-color-text, #374151)' }">
            <input type="checkbox" disabled :style="{ accentColor: submitBg }" />
            <span>{{ t('Ricordami') }}</span>
          </label>
          <span v-else></span>
          <a v-if="s.show_lost_password" href="#" style="font-size:13px;text-decoration:none;font-weight:500;" :style="{ color: linkColor }">{{ t('Password dimenticata?') }}</a>
        </div>
        <button type="button" :style="submitStyle">{{ s.login_button_text || 'Accedi' }}</button>
        <!-- Switch link -->
        <div v-if="s.mode === 'both'" style="text-align:center;margin-top:16px;font-size:13px;" :style="{ color: s.text_color || 'var(--olo-color-text-muted, #9CA3AF)' }">
          Non hai un account? <a href="#" @click.prevent="activeTab = 'register'" style="text-decoration:none;font-weight:600;" :style="{ color: linkColor }">{{ t('Registrati') }}</a>
        </div>
      </form>

      <!-- ═══ Register form ═══ -->
      <form v-if="showRegister" @submit.prevent>
        <div style="display:flex;flex-wrap:wrap;gap:16px 12px;">
          <template v-for="(rf, ri) in regFields" :key="rf.id || ri">
            <!-- ── Username (built-in) ── -->
            <div v-if="rf.field_type === 'username'" :style="fieldWidthStyle(rf)">
              <label :style="labelStyle">{{ rf.label || 'Nome utente' }}{{ rf.required ? ' *' : '' }}</label>
              <div :style="inputWrapStyle">
                <span v-if="s.show_input_icons" :style="inputIconStyle"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
                <input type="text" :placeholder="rf.placeholder || ''" :style="inputFieldStyle" disabled />
              </div>
            </div>
            <!-- ── Email (built-in) ── -->
            <div v-else-if="rf.field_type === 'user_email'" :style="fieldWidthStyle(rf)">
              <label :style="labelStyle">{{ rf.label || 'Email' }}{{ rf.required ? ' *' : '' }}</label>
              <div :style="inputWrapStyle">
                <span v-if="s.show_input_icons" :style="inputIconStyle"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg></span>
                <input type="email" :placeholder="rf.placeholder || ''" :style="inputFieldStyle" disabled />
              </div>
            </div>
            <!-- ── Password (built-in) ── -->
            <div v-else-if="rf.field_type === 'user_password'" :style="fieldWidthStyle(rf)" style="margin-bottom:0 !important;">
              <label :style="labelStyle">{{ rf.label || 'Password' }}{{ rf.required ? ' *' : '' }}</label>
              <div :style="inputWrapStyle">
                <span v-if="s.show_input_icons" :style="inputIconStyle"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                <input :type="showPwRegister ? 'text' : 'password'" :placeholder="rf.placeholder || ''" :style="inputFieldStyle" disabled />
                <button v-if="s.show_password_toggle" type="button" @click="showPwRegister = !showPwRegister" :style="pwToggleStyle">
                  <svg v-if="!showPwRegister" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                </button>
              </div>
              <!-- Password strength (after password) -->
              <div v-if="s.show_password_strength" style="margin-top:8px;">
                <div style="display:flex;gap:4px;">
                  <div v-for="i in 4" :key="i" :style="{ flex:1, height:'4px', borderRadius:'2px', backgroundColor: i <= 2 ? '#F59E0B' : (s.input_border_color || 'var(--olo-color-border, #E5E7EB)') }"></div>
                </div>
                <div style="font-size:11px;margin-top:4px;color:#F59E0B;">{{ t('Media') }}</div>
              </div>
              <!-- Password requirements -->
              <div v-if="pwRequirements.length" style="margin-top:8px;">
                <div v-for="(req, ri) in pwRequirements" :key="ri" style="display:flex;align-items:center;gap:6px;font-size:11px;margin-bottom:3px;" :style="{ color: s.text_color || 'var(--olo-color-text-muted, #9CA3AF)' }">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="opacity:0.5;"><circle cx="12" cy="12" r="10"/></svg>
                  <span>{{ req }}</span>
                </div>
              </div>
            </div>
            <!-- ── Confirm password (built-in) ── -->
            <div v-else-if="rf.field_type === 'confirm_password'" :style="fieldWidthStyle(rf)">
              <label :style="labelStyle">{{ rf.label || 'Conferma password' }}{{ rf.required ? ' *' : '' }}</label>
              <div :style="inputWrapStyle">
                <span v-if="s.show_input_icons" :style="inputIconStyle"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                <input type="password" :placeholder="rf.placeholder || ''" :style="inputFieldStyle" disabled />
              </div>
            </div>
            <!-- ── Text/Email/Tel/Number/Date/URL ── -->
            <div v-else-if="['text','email','tel','number','date','url'].includes(rf.field_type || 'text')" :style="fieldWidthStyle(rf)">
              <label :style="labelStyle">{{ rf.label || 'Campo' }}{{ rf.required ? ' *' : '' }}</label>
              <div :style="inputWrapStyle">
                <input :type="rf.field_type || 'text'" :placeholder="rf.placeholder || ''" :style="inputFieldStyle" disabled />
              </div>
            </div>
            <!-- ── Textarea ── -->
            <div v-else-if="rf.field_type === 'textarea'" :style="fieldWidthStyle(rf)">
              <label :style="labelStyle">{{ rf.label || 'Campo' }}{{ rf.required ? ' *' : '' }}</label>
              <textarea :placeholder="rf.placeholder || ''" :style="{ ...textareaStyle }" disabled></textarea>
            </div>
            <!-- ── Select ── -->
            <div v-else-if="rf.field_type === 'select'" :style="fieldWidthStyle(rf)">
              <label :style="labelStyle">{{ rf.label || 'Campo' }}{{ rf.required ? ' *' : '' }}</label>
              <select :style="selectStyle" disabled>
                <option value="">{{ rf.placeholder || 'Seleziona...' }}</option>
                <option v-for="(opt, oi) in parseOpts(rf.options)" :key="oi">{{ opt }}</option>
              </select>
            </div>
            <!-- ── Checkbox ── -->
            <div v-else-if="rf.field_type === 'checkbox'" :style="fieldWidthStyle(rf)">
              <label style="display:flex;align-items:center;gap:8px;font-size:14px;cursor:pointer;" :style="{ color: s.text_color || 'var(--olo-color-text, #374151)' }">
                <input type="checkbox" disabled :style="{ accentColor: submitBg }" />
                <span>{{ rf.placeholder || rf.label }}</span>
              </label>
            </div>
            <!-- ── Radio ── -->
            <div v-else-if="rf.field_type === 'radio'" :style="fieldWidthStyle(rf)">
              <label :style="labelStyle">{{ rf.label || 'Campo' }}{{ rf.required ? ' *' : '' }}</label>
              <div style="display:flex;flex-direction:column;gap:6px;">
                <label v-for="(opt, oi) in parseOpts(rf.options)" :key="oi" style="display:flex;align-items:center;gap:8px;font-size:14px;cursor:pointer;" :style="{ color: s.text_color || 'var(--olo-color-text, #374151)' }">
                  <input type="radio" :name="'rf-' + ri" disabled :style="{ accentColor: submitBg }" />
                  <span>{{ opt }}</span>
                </label>
              </div>
            </div>
          </template>
        </div>

        <!-- Terms -->
        <label v-if="s.show_terms" style="display:flex;align-items:flex-start;gap:8px;margin-top:16px;margin-bottom:16px;font-size:13px;cursor:pointer;" :style="{ color: s.text_color || 'var(--olo-color-text, #374151)' }">
          <input type="checkbox" disabled :style="{ accentColor: submitBg, marginTop: '2px' }" />
          <span>
            <template v-if="s.terms_url">
              <a :href="s.terms_url" target="_blank" style="text-decoration:underline;" :style="{ color: linkColor }">{{ s.terms_text || 'Accetto i Termini e le Condizioni' }}</a>
            </template>
            <template v-else>{{ s.terms_text || 'Accetto i Termini e le Condizioni' }}</template>
          </span>
        </label>

        <button type="button" :style="submitStyle" style="margin-top:16px;">{{ s.register_button_text || 'Registrati' }}</button>
        <!-- Switch link -->
        <div v-if="s.mode === 'both'" style="text-align:center;margin-top:16px;font-size:13px;" :style="{ color: s.text_color || 'var(--olo-color-text-muted, #9CA3AF)' }">
          Hai già un account? <a href="#" @click.prevent="activeTab = 'login'" style="text-decoration:none;font-weight:600;" :style="{ color: linkColor }">Accedi</a>
        </div>
      </form>
    </template>

  </div>
</template>

<script setup>
import { t } from '@/i18n';
import { computed, ref } from 'vue';

const props = defineProps({
  settings: { type: Object, default: () => ({}) },
});

const defaults = {
  mode: 'login',
  show_remember_me: true,
  show_lost_password: true,
  show_avatar: true,
  logged_in_message: 'Bentornato!',
  login_title: 'Bentornato',
  login_subtitle: 'Accedi al tuo account',
  register_title: 'Crea un account',
  register_subtitle: 'Registrati in pochi secondi',
  login_button_text: 'Accedi',
  register_button_text: 'Registrati',
  show_input_icons: true,
  show_password_toggle: true,
  show_password_strength: true,
  password_min_length: 8,
  password_require_uppercase: false,
  password_require_number: false,
  password_require_special: false,
  password_min_strength: 0,
  show_terms: false,
  terms_text: 'Accetto i Termini e le Condizioni',
  terms_url: '',
  show_social_divider: false,
  social_divider_text: 'oppure',
  social_google: false,
  social_facebook: false,
  social_apple: false,
  tab_style: 'underline',
  form_padding: '32',
  register_fields: [
    { id: 'rf-username', label: 'Nome utente', field_type: 'username', placeholder: 'Scegli un nome utente', required: true, width: '100' },
    { id: 'rf-email', label: 'Email', field_type: 'user_email', placeholder: 'nome@esempio.it', required: true, width: '100' },
    { id: 'rf-password', label: 'Password', field_type: 'user_password', placeholder: 'Min. 8 caratteri', required: true, width: '100' },
  ],
  form_bg: '',
  text_color: '',
  label_color: '',
  input_bg: '',
  input_color: '',
  input_border_color: '',
  input_focus_color: '',
  input_padding: '11',
  input_radius: '8',
  submit_bg: '',
  submit_color: '#FFFFFF',
  submit_radius: '8',
  submit_hover_bg: '',
  link_color: '',
  icon_color: '',
  border_radius: '12',
  border_width: '0',
  border_color: '',
};

const s = computed(() => ({ ...defaults, ...props.settings }));

const activeTab = ref('login');
const showLoggedIn = ref(false);
const showPwLogin = ref(false);
const showPwRegister = ref(false);

const submitBg = computed(() => s.value.submit_bg || 'var(--olo-color-primary, #6366F1)');
const linkColor = computed(() => s.value.link_color || s.value.submit_bg || 'var(--olo-color-primary, #6366F1)');
const inputPad = computed(() => (parseInt(s.value.input_padding) || 11) + 'px');
const inputR = computed(() => ((v => isNaN(v) ? 8 : v)(parseInt(s.value.input_radius))) + 'px');
const submitR = computed(() => ((v => isNaN(v) ? 8 : v)(parseInt(s.value.submit_radius))) + 'px');
const formR = computed(() => ((v => isNaN(v) ? 12 : v)(parseInt(s.value.border_radius))) + 'px');
const formPad = computed(() => (parseInt(s.value.form_padding) || 32) + 'px');

const hasSocial = computed(() => s.value.social_google || s.value.social_facebook || s.value.social_apple);

const tabs = [
  { key: 'login', label: 'Accedi' },
  { key: 'register', label: 'Registrati' },
];

const showLogin = computed(() => {
  if (showLoggedIn.value) return false;
  if (s.value.mode === 'login') return true;
  if (s.value.mode === 'both') return activeTab.value === 'login';
  return false;
});

const showRegister = computed(() => {
  if (showLoggedIn.value) return false;
  if (s.value.mode === 'register') return true;
  if (s.value.mode === 'both') return activeTab.value === 'register';
  return false;
});

const currentSubtitle = computed(() => {
  if (showLogin.value) return s.value.login_subtitle || '';
  return s.value.register_subtitle || '';
});

const regFields = computed(() => {
  const raw = s.value.register_fields;
  return Array.isArray(raw) ? raw : [];
});

const pwRequirements = computed(() => {
  const reqs = [];
  const minLen = parseInt(s.value.password_min_length) || 8;
  if (minLen > 0) reqs.push(`Almeno ${minLen} caratteri`);
  if (s.value.password_require_uppercase) reqs.push('Almeno una lettera maiuscola');
  if (s.value.password_require_number) reqs.push('Almeno un numero');
  if (s.value.password_require_special) reqs.push('Almeno un carattere speciale (!@#$...)');
  const minStr = parseInt(s.value.password_min_strength) || 0;
  if (minStr > 0) {
    const labels = { 1: 'Debole', 2: 'Media', 3: 'Buona', 4: 'Forte' };
    reqs.push(`Forza minima: ${labels[minStr] || minStr}`);
  }
  return reqs;
});

function fieldWidthStyle(rf) {
  if (rf.width === '33') return { width: 'calc(33.333% - 8px)', flexShrink: 0 };
  if (rf.width === '50') return { width: 'calc(50% - 6px)', flexShrink: 0 };
  return { width: '100%', flexShrink: 0 };
}

function parseOpts(str) {
  if (!str) return [];
  return str.split('\n').map(s => s.trim()).filter(Boolean);
}

// ─── Styles ───

const wrapStyle = computed(() => {
  const st = {
    padding: formPad.value,
    borderRadius: formR.value,
    minHeight: '120px',
  };
  if (s.value.form_bg) st.backgroundColor = s.value.form_bg;
  const bw = parseInt(s.value.border_width);
  if (bw > 0) {
    st.border = bw + 'px solid ' + (s.value.border_color || 'var(--olo-color-border, #E5E7EB)');
  }
  return st;
});

const labelStyle = computed(() => ({
  display: 'block',
  marginBottom: '6px',
  fontSize: '13px',
  fontWeight: '600',
  color: s.value.label_color || s.value.text_color || 'var(--olo-color-text, #374151)',
}));

const inputWrapStyle = computed(() => ({
  display: 'flex',
  alignItems: 'center',
  backgroundColor: s.value.input_bg || 'var(--olo-color-background, #FFFFFF)',
  border: '1px solid ' + (s.value.input_border_color || 'var(--olo-color-border, #E5E7EB)'),
  borderRadius: inputR.value,
  overflow: 'hidden',
  transition: 'border-color 0.2s',
}));

const inputFieldStyle = computed(() => ({
  display: 'block',
  width: '100%',
  boxSizing: 'border-box',
  padding: s.value.show_input_icons ? `${inputPad.value} 14px ${inputPad.value} 0` : `${inputPad.value} 14px`,
  fontSize: '14px',
  fontFamily: 'inherit',
  backgroundColor: 'transparent',
  color: s.value.input_color || 'var(--olo-color-text, #374151)',
  border: 'none',
  outline: 'none',
}));

const inputIconStyle = computed(() => ({
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  padding: '0 0 0 14px',
  color: s.value.icon_color || 'var(--olo-color-text-muted, #9CA3AF)',
  flexShrink: '0',
}));

const pwToggleStyle = computed(() => ({
  display: 'flex',
  alignItems: 'center',
  padding: '0 12px',
  background: 'none',
  border: 'none',
  cursor: 'pointer',
  color: s.value.icon_color || 'var(--olo-color-text-muted, #9CA3AF)',
  flexShrink: '0',
}));

const submitStyle = computed(() => ({
  display: 'block',
  width: '100%',
  padding: '12px 24px',
  fontSize: '15px',
  fontWeight: '600',
  fontFamily: 'inherit',
  backgroundColor: submitBg.value,
  color: s.value.submit_color || '#FFFFFF',
  borderRadius: submitR.value,
  border: 'none',
  textAlign: 'center',
  cursor: 'pointer',
  transition: 'background-color 0.2s, transform 0.15s',
}));

const textareaStyle = computed(() => ({
  display: 'block',
  width: '100%',
  boxSizing: 'border-box',
  padding: `${inputPad.value} 14px`,
  fontSize: '14px',
  fontFamily: 'inherit',
  backgroundColor: s.value.input_bg || 'var(--olo-color-background, #FFFFFF)',
  color: s.value.input_color || 'var(--olo-color-text, #374151)',
  border: '1px solid ' + (s.value.input_border_color || 'var(--olo-color-border, #E5E7EB)'),
  borderRadius: inputR.value,
  outline: 'none',
  minHeight: '72px',
  resize: 'vertical',
}));

const selectStyle = computed(() => ({
  display: 'block',
  width: '100%',
  boxSizing: 'border-box',
  padding: `${inputPad.value} 14px`,
  fontSize: '14px',
  fontFamily: 'inherit',
  backgroundColor: s.value.input_bg || 'var(--olo-color-background, #FFFFFF)',
  color: s.value.input_color || 'var(--olo-color-text, #374151)',
  border: '1px solid ' + (s.value.input_border_color || 'var(--olo-color-border, #E5E7EB)'),
  borderRadius: inputR.value,
  outline: 'none',
}));

const socialBtnStyle = computed(() => ({
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  gap: '10px',
  width: '100%',
  padding: '10px 16px',
  fontSize: '14px',
  fontWeight: '500',
  fontFamily: 'inherit',
  backgroundColor: s.value.input_bg || 'var(--olo-color-background, #FFFFFF)',
  color: s.value.text_color || 'var(--olo-color-text, #374151)',
  border: '1px solid ' + (s.value.input_border_color || 'var(--olo-color-border, #E5E7EB)'),
  borderRadius: inputR.value,
  cursor: 'pointer',
  transition: 'background-color 0.2s',
}));

const dividerLineStyle = computed(() => ({
  flex: '1',
  height: '1px',
  backgroundColor: s.value.input_border_color || 'var(--olo-color-border, #E5E7EB)',
}));

// Tab styles
const tabsWrapStyle = computed(() => {
  const base = { display: 'flex', marginBottom: '24px' };
  if (s.value.tab_style === 'pill') {
    base.backgroundColor = s.value.input_bg || 'var(--olo-color-muted, #F3F4F6)';
    base.borderRadius = inputR.value;
    base.padding = '4px';
    base.gap = '4px';
  } else {
    base.gap = '0';
    base.borderBottom = '2px solid ' + (s.value.input_border_color || 'var(--olo-color-border, #E5E7EB)');
  }
  return base;
});

const tabActiveStyle = computed(() => {
  const base = {
    flex: '1',
    padding: '10px 16px',
    fontSize: '14px',
    fontWeight: '600',
    fontFamily: 'inherit',
    border: 'none',
    cursor: 'pointer',
    textAlign: 'center',
    transition: 'all 0.2s',
  };
  if (s.value.tab_style === 'pill') {
    base.backgroundColor = submitBg.value;
    base.color = s.value.submit_color || '#FFFFFF';
    base.borderRadius = ((v => isNaN(v) ? 6 : v)(parseInt(s.value.input_radius) - 2)) + 'px';
  } else if (s.value.tab_style === 'classic') {
    base.backgroundColor = submitBg.value;
    base.color = s.value.submit_color || '#FFFFFF';
    base.borderRadius = inputR.value + ' ' + inputR.value + ' 0 0';
  } else {
    base.backgroundColor = 'transparent';
    base.color = submitBg.value;
    base.borderBottom = '2px solid ' + submitBg.value;
    base.marginBottom = '-2px';
  }
  return base;
});

const tabInactiveStyle = computed(() => {
  const base = {
    flex: '1',
    padding: '10px 16px',
    fontSize: '14px',
    fontWeight: '500',
    fontFamily: 'inherit',
    border: 'none',
    cursor: 'pointer',
    textAlign: 'center',
    transition: 'all 0.2s',
    backgroundColor: 'transparent',
    color: s.value.text_color || 'var(--olo-color-text-muted, #9CA3AF)',
  };
  if (s.value.tab_style === 'pill') {
    base.borderRadius = ((v => isNaN(v) ? 6 : v)(parseInt(s.value.input_radius) - 2)) + 'px';
  } else if (s.value.tab_style !== 'classic') {
    base.borderBottom = '2px solid transparent';
    base.marginBottom = '-2px';
  }
  return base;
});
</script>

<style scoped>
.olo-loginform-preview {
  font-family: inherit;
}
.olo-loginform-preview a:hover {
  text-decoration: underline !important;
}
</style>
