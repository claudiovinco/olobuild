export default {
  type: 'html',
  name: 'HTML / Codice',
  icon: 'dashicons-editor-code',
  category: 'content',
  defaults: {
    html_content: '<div style="padding:20px;text-align:center;color:#9CA3AF;">HTML personalizzato</div>',
    sandbox: false,
  },
  fields: [
    { key: 'html_content', label: 'Contenuto HTML', type: 'textarea' },
    { key: 'sandbox', label: 'Sandbox (iframe)', type: 'toggle' },
  ],
};
