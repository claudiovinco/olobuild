/* Patch Sterling: approach→process-steps (borderless), team→team tiles, cta→2° pulsante */
const fs = require('fs');
const f = 'tmp_gen_sterling.cjs';
let s = fs.readFileSync(f, 'utf8');

const i4 = s.indexOf('// ─── 4) APPROACH');
const i6 = s.indexOf('// ─── 6) TESTIMONIAL');
if (i4 < 0 || i6 < 0) { console.error('marker non trovati'); process.exit(1); }

const newMid = `// ─── 4) APPROACH — ProcessSteps (borderless, numeri oro) ────────────────────
home.push(sec(BG2, 'large', [
  row([ col('1-1', [ tile('section-header', {
    eyebrow_show: true, eyebrow_text: 'Our approach', eyebrow_color: GOLD, eyebrow_dot_color: GOLD, eyebrow_separator: '',
    headline_lines: [ { text: 'Considered,', color: CREAM, italic: false }, { text: 'every step', color: GOLD, italic: true } ],
    headline_font_family: 'serif', headline_font_size: 48, headline_font_weight: '500', headline_align: 'center', headline_inline: true,
    tagline_show: false, layout: 'center', gap: 16,
  }) ]) ]),
  row([ col('1-1', [ tile('process-steps', {
    columns: 4, gap: 16, align: 'left', auto_number: false, item_gap: 10,
    number_style: 'plain', number_color: GOLD, number_size: 40, number_font: 'serif', number_weight: '500',
    title_color: CREAM, title_size: 21, title_font: 'serif', title_weight: '600',
    desc_color: DIM, desc_size: 14,
    card_bg: '', card_border: '', card_padding: 0,
    items: [
      { number: '01', title: 'Listen', description: \`We start with your life, not your balance sheet. What's it all for?\` },
      { number: '02', title: 'Plan', description: 'A clear strategy, modelled and stress-tested against the things that worry you.' },
      { number: '03', title: 'Invest', description: \`Patient, diversified, low-cost where it counts — and always explained.\` },
      { number: '04', title: 'Review', description: 'We meet regularly, adjust as life changes, and never go quiet.' },
    ],
  }) ]) ]),
]));

// ─── 5) TEAM — section-header + 4 team tiles (avatar circolare + nome + ruolo) ─
const stMember = (name, role) => col('1-4', [ tile('team', {
  photo: '', name, role, bio: '', link_url: '', link_text: '',
  photo_size: '120', photo_shape: 'circle', photo_border_width: '0', photo_shadow: 'none', photo_gap: '16',
  info_bg_color: 'transparent', info_text_color: CREAM, role_color: GOLD, info_align: 'center',
  name_size: '21', name_weight: '600', role_size: '13',
  bg_color: 'transparent', tile_padding: { top: 0, right: 0, bottom: 0, left: 0 }, border_radius: '0',
}) ]);
home.push(sec(BG, 'large', [
  row([ col('1-1', [ tile('section-header', {
    eyebrow_show: true, eyebrow_text: 'Our people', eyebrow_color: GOLD, eyebrow_dot_color: GOLD, eyebrow_separator: '',
    headline_lines: [ { text: 'The same faces,', color: CREAM, italic: false }, { text: 'year after year', color: GOLD, italic: true } ],
    headline_font_family: 'serif', headline_font_size: 48, headline_font_weight: '500', headline_align: 'center', headline_inline: true,
    tagline_show: false, layout: 'center', gap: 16,
  }) ]) ]),
  row([ stMember('Eleanor Vance', 'Managing partner'), stMember('James Okonkwo', 'Head of planning'), stMember('Priya Anand', 'Investment director'), stMember('Hugo Bexcell', 'Estates & legacy') ], { gap: 16 }),
]));

`;

s = s.slice(0, i4) + newMid + s.slice(i6);

// CTA: aggiungi 2° pulsante "Our services"
s = s.replace(
  `  cta_text: 'Arrange a call',\n  cta_url: '#contact',\n  bg: { type: 'solid', color: INK },`,
  `  cta_text: 'Arrange a call',\n  cta_url: '#contact',\n  cta2_text: 'Our services', cta2_url: '#services', cta2_bg: 'transparent', cta2_color: CREAM, cta2_border: LINE2,\n  bg: { type: 'solid', color: INK },`
);

fs.writeFileSync(f, s);
console.log('patch applicata. cta2 presente:', s.includes('cta2_text'));
