export default {
  type: 'killnextprev',
  name: 'Kill Next/Prev',
  icon: 'dashicons-hidden',
  category: 'navigation',
  defaults: {},
  sections: [
    {
      label: 'Info',
      fields: [
        {
          key: '_info',
          type: 'html',
          html: '<div style="padding:12px;background:#fef3c7;border-radius:8px;font-size:13px;color:#92400e;line-height:1.5"><strong>Tile utility</strong><br>Rimuove automaticamente la navigazione &laquo;Precedente / Successivo&raquo; di WordPress dalla pagina pubblica.<br><br>Non ha opzioni — basta inserirla nel template.</div>',
        },
      ],
    },
  ],
};
