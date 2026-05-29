// Build "Guida al Layout in Olobuild" — Word document
// Output: docs/guida-layout-flex-grid.docx
const fs = require('fs');
const path = require('path');
const {
  Document, Packer, Paragraph, TextRun, Table, TableRow, TableCell,
  AlignmentType, LevelFormat, HeadingLevel, BorderStyle, WidthType, ShadingType,
  VerticalAlign, PageNumber, PageBreak
} = require('docx');

// ── Helpers ─────────────────────────────────────────────────────────────────

const cellBorder = { style: BorderStyle.SINGLE, size: 4, color: 'CBD5E1' };
const cellBorders = { top: cellBorder, bottom: cellBorder, left: cellBorder, right: cellBorder };
const noBorder = { style: BorderStyle.NONE, size: 0, color: 'FFFFFF' };
const noBorders = { top: noBorder, bottom: noBorder, left: noBorder, right: noBorder };

function p(text, opts = {}) {
  return new Paragraph({
    spacing: opts.spacing ?? { before: 60, after: 60 },
    alignment: opts.align,
    children: [new TextRun({ text, bold: opts.bold, italics: opts.italics, color: opts.color, size: opts.size, font: 'Calibri' })],
  });
}

function h1(text) {
  return new Paragraph({
    heading: HeadingLevel.HEADING_1,
    spacing: { before: 360, after: 180 },
    children: [new TextRun({ text, bold: true, size: 36, font: 'Calibri' })],
  });
}

function h2(text) {
  return new Paragraph({
    heading: HeadingLevel.HEADING_2,
    spacing: { before: 280, after: 140 },
    children: [new TextRun({ text, bold: true, size: 30, font: 'Calibri', color: '1E3A8A' })],
  });
}

function h3(text) {
  return new Paragraph({
    heading: HeadingLevel.HEADING_3,
    spacing: { before: 200, after: 100 },
    children: [new TextRun({ text, bold: true, size: 24, font: 'Calibri', color: '475569' })],
  });
}

function bullet(text, level = 0) {
  return new Paragraph({
    numbering: { reference: 'bullets', level },
    spacing: { before: 40, after: 40 },
    children: [new TextRun({ text, font: 'Calibri', size: 22 })],
  });
}

function listNum(text) {
  return new Paragraph({
    numbering: { reference: 'numbers', level: 0 },
    spacing: { before: 40, after: 40 },
    children: [new TextRun({ text, font: 'Calibri', size: 22 })],
  });
}

function quote(text) {
  return new Paragraph({
    spacing: { before: 100, after: 100 },
    indent: { left: 360 },
    border: { left: { style: BorderStyle.SINGLE, size: 18, color: 'F59E0B', space: 12 } },
    children: [new TextRun({ text, italics: true, font: 'Calibri', size: 22, color: '78350F' })],
  });
}

// Coloured box "cell" used in diagrams — fixed width, single line of text
function box(label, fill, textColor, width) {
  return new TableCell({
    width: { size: width, type: WidthType.DXA },
    margins: { top: 80, bottom: 80, left: 100, right: 100 },
    verticalAlign: VerticalAlign.CENTER,
    borders: { top: cellBorder, bottom: cellBorder, left: cellBorder, right: cellBorder },
    shading: { fill, type: ShadingType.CLEAR },
    children: [new Paragraph({
      alignment: AlignmentType.CENTER,
      children: [new TextRun({ text: label, bold: true, color: textColor, font: 'Calibri', size: 22 })],
    })],
  });
}

// Empty spacer cell (no border, no fill) — used to push boxes around
function spacer(width) {
  return new TableCell({
    width: { size: width, type: WidthType.DXA },
    borders: noBorders,
    children: [new Paragraph('')],
  });
}

// Build a single-row diagram: array of [label, fill, color, width] OR null for spacer
function diagramRow(items, totalWidth) {
  return new Table({
    width: { size: totalWidth, type: WidthType.DXA },
    columnWidths: items.map(it => it ? it[3] : it === null ? 200 : 200),
    borders: noBorders,
    rows: [new TableRow({
      children: items.map(it => it === null ? spacer(200) : box(it[0], it[1], it[2], it[3])),
    })],
  });
}

function caption(text) {
  return new Paragraph({
    spacing: { before: 40, after: 200 },
    children: [new TextRun({ text, italics: true, color: '64748B', size: 20, font: 'Calibri' })],
  });
}

// Comparison table: header row + data rows
function dataTable(headers, rows) {
  const colCount = headers.length;
  const contentWidth = 9360;
  const colWidth = Math.floor(contentWidth / colCount);
  return new Table({
    width: { size: contentWidth, type: WidthType.DXA },
    columnWidths: Array(colCount).fill(colWidth),
    rows: [
      new TableRow({
        tableHeader: true,
        children: headers.map(h => new TableCell({
          width: { size: colWidth, type: WidthType.DXA },
          margins: { top: 100, bottom: 100, left: 120, right: 120 },
          shading: { fill: '1E3A8A', type: ShadingType.CLEAR },
          borders: cellBorders,
          children: [new Paragraph({
            children: [new TextRun({ text: h, bold: true, color: 'FFFFFF', font: 'Calibri', size: 22 })],
          })],
        })),
      }),
      ...rows.map(row => new TableRow({
        children: row.map(cellText => new TableCell({
          width: { size: colWidth, type: WidthType.DXA },
          margins: { top: 80, bottom: 80, left: 120, right: 120 },
          borders: cellBorders,
          children: [new Paragraph({
            children: [new TextRun({ text: cellText, font: 'Calibri', size: 22 })],
          })],
        })),
      })),
    ],
  });
}

// ── Diagrams ────────────────────────────────────────────────────────────────

// Gerarchia: 4-livelli nested tile representation (text-based ASCII-style table)
function hierarchyDiagram() {
  // Mostro la gerarchia come una tabella annidata: section > row > column > tile
  // Uso una tabella di una colonna con celle che si "indentano" e diventano più chiare
  const w = 8640;
  return new Table({
    width: { size: w, type: WidthType.DXA },
    columnWidths: [w],
    rows: [
      new TableRow({
        children: [new TableCell({
          width: { size: w, type: WidthType.DXA },
          margins: { top: 160, bottom: 160, left: 200, right: 200 },
          shading: { fill: 'F8FAFC', type: ShadingType.CLEAR },
          borders: { top: { ...cellBorder, color: '64748B' }, bottom: { ...cellBorder, color: '64748B' }, left: { ...cellBorder, color: '64748B' }, right: { ...cellBorder, color: '64748B' } },
          children: [
            new Paragraph({ children: [new TextRun({ text: 'SECTION', bold: true, color: '475569', size: 22, font: 'Calibri' })] }),
            new Paragraph({ spacing: { before: 100 }, children: [new TextRun({ text: '', size: 4 })] }),
            new Table({
              width: { size: w - 400, type: WidthType.DXA },
              columnWidths: [w - 400],
              rows: [new TableRow({
                children: [new TableCell({
                  width: { size: w - 400, type: WidthType.DXA },
                  margins: { top: 140, bottom: 140, left: 200, right: 200 },
                  shading: { fill: 'FEF3C7', type: ShadingType.CLEAR },
                  borders: { top: { ...cellBorder, color: 'F59E0B' }, bottom: { ...cellBorder, color: 'F59E0B' }, left: { ...cellBorder, color: 'F59E0B' }, right: { ...cellBorder, color: 'F59E0B' } },
                  children: [
                    new Paragraph({ children: [new TextRun({ text: 'ROW', bold: true, color: '92400E', size: 22, font: 'Calibri' })] }),
                    new Paragraph({ spacing: { before: 80 }, children: [new TextRun({ text: '', size: 4 })] }),
                    new Table({
                      width: { size: w - 800, type: WidthType.DXA },
                      columnWidths: [Math.floor((w - 800) / 2), Math.floor((w - 800) / 2)],
                      rows: [new TableRow({
                        children: [
                          buildColumnCell((w - 800) / 2, [
                            { label: 'tile (headline)', fill: 'FFFFFF', color: '3730A3' },
                            { label: 'tile (content)', fill: 'FFFFFF', color: '3730A3' },
                            { label: 'tile (button)', fill: 'FFFFFF', color: '3730A3' },
                          ]),
                          buildColumnCell((w - 800) / 2, [
                            { label: 'tile (image)', fill: 'FFFFFF', color: '3730A3' },
                          ]),
                        ],
                      })],
                    }),
                  ],
                })],
              })],
            }),
          ],
        })],
      }),
    ],
  });
}

function buildColumnCell(width, tiles) {
  return new TableCell({
    width: { size: width, type: WidthType.DXA },
    margins: { top: 120, bottom: 120, left: 140, right: 140 },
    shading: { fill: 'DBEAFE', type: ShadingType.CLEAR },
    borders: { top: { ...cellBorder, color: '3B82F6' }, bottom: { ...cellBorder, color: '3B82F6' }, left: { ...cellBorder, color: '3B82F6' }, right: { ...cellBorder, color: '3B82F6' } },
    children: [
      new Paragraph({ children: [new TextRun({ text: 'COLUMN', bold: true, color: '1E40AF', size: 20, font: 'Calibri' })] }),
      new Paragraph({ spacing: { before: 60 }, children: [new TextRun({ text: '', size: 2 })] }),
      ...tiles.map(t => new Paragraph({
        spacing: { before: 40, after: 40 },
        children: [new TextRun({ text: '▢  ' + t.label, color: t.color, size: 20, font: 'Calibri' })],
      })),
    ],
  });
}

// Flex visualization: 4 boxes in a row
function flexDiagram(labels, fill, textColor) {
  const totalW = 5000;
  const each = Math.floor(totalW / labels.length);
  return new Table({
    width: { size: totalW, type: WidthType.DXA },
    columnWidths: Array(labels.length).fill(each),
    borders: noBorders,
    rows: [new TableRow({
      children: labels.map(l => box(l, fill, textColor, each)),
    })],
  });
}

// Grid visualization: 6 boxes in 2 rows
function gridDiagram() {
  const totalW = 5000;
  const w = Math.floor(totalW / 3);
  return new Table({
    width: { size: totalW, type: WidthType.DXA },
    columnWidths: [w, w, w],
    borders: noBorders,
    rows: [
      new TableRow({ children: [box('A', 'FEF3C7', '92400E', w), box('B', 'FEF3C7', '92400E', w), box('C', 'FEF3C7', '92400E', w)] }),
      new TableRow({ children: [box('D', 'FEF3C7', '92400E', w), box('E', 'FEF3C7', '92400E', w), box('F', 'FEF3C7', '92400E', w)] }),
    ],
  });
}

// "Justify content" visualization: 3 small boxes with custom spacing
function justifyDiagram(positions) {
  // positions: array of 'start' | 'gap' | 'box' — describes column layout
  const totalW = 5000;
  const colW = Math.floor(totalW / positions.length);
  return new Table({
    width: { size: totalW, type: WidthType.DXA },
    columnWidths: Array(positions.length).fill(colW),
    borders: noBorders,
    rows: [new TableRow({
      children: positions.map(pos => {
        if (pos === 'box') return box(' ', '3B82F6', 'FFFFFF', colW);
        return spacer(colW);
      }),
    })],
  });
}

// Align items visualization: variable-height boxes
function alignDiagram(heights) {
  // heights: array of 'top' | 'middle' | 'bottom' | 'stretch'
  // Word doesn't easily allow variable-height boxes; we use vertical alignment in a row
  // Simulate via vertical alignment of small boxes in a fixed-height row
  // Simplification: use a 3x3 table where filled cells indicate position
  const totalW = 5000;
  const colW = Math.floor(totalW / heights.length);
  // For each column, create a 3-row vertical stack where one row has color
  return new Table({
    width: { size: totalW, type: WidthType.DXA },
    columnWidths: Array(heights.length).fill(colW),
    borders: noBorders,
    rows: [
      new TableRow({
        children: heights.map(h => new TableCell({
          width: { size: colW, type: WidthType.DXA },
          margins: { top: 20, bottom: 20, left: 40, right: 40 },
          shading: { fill: h === 'top' || h === 'stretch' ? 'F59E0B' : 'F8FAFC', type: ShadingType.CLEAR },
          borders: { top: cellBorder, bottom: { style: BorderStyle.NONE, size: 0, color: 'FFFFFF' }, left: cellBorder, right: cellBorder },
          children: [new Paragraph({ children: [new TextRun({ text: ' ', size: 12 })] })],
        })),
      }),
      new TableRow({
        children: heights.map(h => new TableCell({
          width: { size: colW, type: WidthType.DXA },
          margins: { top: 20, bottom: 20, left: 40, right: 40 },
          shading: { fill: h === 'middle' || h === 'stretch' ? 'F59E0B' : 'F8FAFC', type: ShadingType.CLEAR },
          borders: { top: noBorder, bottom: noBorder, left: cellBorder, right: cellBorder },
          children: [new Paragraph({ children: [new TextRun({ text: ' ', size: 12 })] })],
        })),
      }),
      new TableRow({
        children: heights.map(h => new TableCell({
          width: { size: colW, type: WidthType.DXA },
          margins: { top: 20, bottom: 20, left: 40, right: 40 },
          shading: { fill: h === 'bottom' || h === 'stretch' ? 'F59E0B' : 'F8FAFC', type: ShadingType.CLEAR },
          borders: { top: noBorder, bottom: cellBorder, left: cellBorder, right: cellBorder },
          children: [new Paragraph({ children: [new TextRun({ text: ' ', size: 12 })] })],
        })),
      }),
    ],
  });
}

// ── Build the document ─────────────────────────────────────────────────────

const children = [];

// Title
children.push(new Paragraph({
  alignment: AlignmentType.CENTER,
  spacing: { after: 100 },
  children: [new TextRun({ text: 'Guida al Layout in Olobuild', bold: true, size: 48, color: '1E3A8A', font: 'Calibri' })],
}));
children.push(new Paragraph({
  alignment: AlignmentType.CENTER,
  spacing: { after: 400 },
  children: [new TextRun({ text: 'Come disporre tile, colonne e sezioni usando Flex e Grid', italics: true, size: 24, color: '64748B', font: 'Calibri' })],
}));

// ── Sezione 1: Gerarchia ───────────────────────────────────────────────────
children.push(h1('La gerarchia di Olobuild'));
children.push(p('Prima di parlare di layout, devi avere chiara la struttura della pagina. Ogni pagina è composta da quattro livelli annidati:'));
children.push(hierarchyDiagram());
children.push(caption('Sezione contiene Riga; Riga contiene Colonne; ogni Colonna contiene Tile.'));

children.push(p('Regola d’oro: ogni livello controlla la disposizione dei propri figli diretti.', { bold: true }));

children.push(dataTable(
  ['Livello', 'Controlla la disposizione di…'],
  [
    ['Section', 'le righe al suo interno'],
    ['Row', 'le colonne al suo interno'],
    ['Column', 'i tile al suo interno'],
    ['Tile', 'i propri elementi interni (slide, voci, item)'],
  ]
));

children.push(quote(
  'Errore tipico: vuoi mettere due tile affianco e imposti “Riga inversa” sulla Row. La Row ha solo colonne come figli — se la colonna è una sola, niente cambia. Devi salire o scendere di un livello finché trovi il container che ha i due tile come figli diretti.'
));

// ── Sezione 2: Flex vs Grid ────────────────────────────────────────────────
children.push(new Paragraph({ children: [new PageBreak()] }));
children.push(h1('Flex vs Grid in un colpo d’occhio'));

children.push(h3('FLEX — una dimensione (riga oppure colonna)'));
children.push(flexDiagram(['1', '2', '3', '4'], 'DBEAFE', '1E40AF'));
children.push(caption('I figli si dispongono uno dopo l’altro in una sola direzione.'));

children.push(h3('GRID — due dimensioni (righe e colonne)'));
children.push(gridDiagram());
children.push(caption('I figli occupano celle di una tabella ideale a 2 dimensioni.'));

children.push(dataTable(
  ['Caratteristica', 'Flex', 'Grid'],
  [
    ['Dimensioni', '1D — riga oppure colonna', '2D — righe e colonne insieme'],
    ['Pensiero', '"metti questi elementi in fila"', '"disegna una tabella e posiziona elementi"'],
    ['Quando usarlo', 'nav, toolbar, riga di card, allineamento', 'gallery a griglia, dashboard, layout complessi'],
  ]
));

children.push(quote(
  'Si possono mixare? Sullo stesso elemento no — display ha un solo valore. Ma un grid item può a sua volta essere un flex container, e viceversa.'
));

// ── Sezione 3: Flex Direction ───────────────────────────────────────────────
children.push(new Paragraph({ children: [new PageBreak()] }));
children.push(h1('Flex Direction — come orientare la fila'));
children.push(p('Sul pannello “Layout Flex” di Olobuild il campo Direzione ha 4 opzioni:'));

children.push(h3('row (default) — sinistra → destra'));
children.push(flexDiagram(['1', '2', '3'], 'DBEAFE', '1E40AF'));

children.push(h3('row-reverse — destra → sinistra'));
children.push(flexDiagram(['3', '2', '1'], 'FEF3C7', '92400E'));

children.push(h3('column — alto → basso'));
children.push(new Table({
  width: { size: 2500, type: WidthType.DXA },
  columnWidths: [2500],
  borders: noBorders,
  rows: [
    new TableRow({ children: [box('1', 'DBEAFE', '1E40AF', 2500)] }),
    new TableRow({ children: [box('2', 'DBEAFE', '1E40AF', 2500)] }),
    new TableRow({ children: [box('3', 'DBEAFE', '1E40AF', 2500)] }),
  ],
}));

children.push(h3('column-reverse — basso → alto'));
children.push(new Table({
  width: { size: 2500, type: WidthType.DXA },
  columnWidths: [2500],
  borders: noBorders,
  rows: [
    new TableRow({ children: [box('3', 'FEF3C7', '92400E', 2500)] }),
    new TableRow({ children: [box('2', 'FEF3C7', '92400E', 2500)] }),
    new TableRow({ children: [box('1', 'FEF3C7', '92400E', 2500)] }),
  ],
}));

children.push(quote(
  'reverse inverte solo l’ordine visivo, non il dato salvato. Su mobile, una "row" potrebbe diventare "column" per effetto del responsive — ricordati di controllare entrambe le anteprime.'
));

// ── Sezione 4: Allineamento ────────────────────────────────────────────────
children.push(new Paragraph({ children: [new PageBreak()] }));
children.push(h1('Allineamento — giustificazione e allineamento verticale'));
children.push(p('Quando il container ha display: flex, due assi controllano dove vanno gli elementi.'));

children.push(h2('Giustificazione (asse principale — orizzontale per row)'));

children.push(h3('flex-start — tutto a sinistra'));
children.push(justifyDiagram(['box', 'box', 'box', null, null]));

children.push(h3('center — al centro'));
children.push(justifyDiagram([null, 'box', 'box', 'box', null]));

children.push(h3('flex-end — tutto a destra'));
children.push(justifyDiagram([null, null, 'box', 'box', 'box']));

children.push(h3('space-between — spazio fra gli elementi, niente ai bordi'));
children.push(justifyDiagram(['box', null, 'box', null, 'box']));

children.push(h3('space-around — spazio uguale attorno a ogni elemento'));
children.push(justifyDiagram([null, 'box', null, 'box', 'box', null]));

children.push(h2('Allineamento verticale (asse trasverso)'));

children.push(h3('flex-start — in alto'));
children.push(alignDiagram(['top', 'top', 'top']));

children.push(h3('center — al centro'));
children.push(alignDiagram(['middle', 'middle', 'middle']));

children.push(h3('flex-end — in basso'));
children.push(alignDiagram(['bottom', 'bottom', 'bottom']));

children.push(h3('stretch (default) — riempie l’altezza disponibile'));
children.push(alignDiagram(['stretch', 'stretch', 'stretch']));

children.push(p('Su column direction gli assi si invertono: la giustificazione diventa verticale e l’allineamento diventa orizzontale.', { italics: true }));

// ── Sezione 5: Wrap ────────────────────────────────────────────────────────
children.push(new Paragraph({ children: [new PageBreak()] }));
children.push(h1('A capo (wrap) — quando i figli non ci stanno'));

children.push(h3('nowrap (default)'));
children.push(p('Tutti i figli stanno sulla stessa riga, anche se devono stringersi.'));
children.push(flexDiagram(['1', '2', '3', '4', '!'], 'DBEAFE', '1E40AF'));

children.push(h3('wrap'));
children.push(p('I figli vanno a capo automaticamente quando non c’è più spazio.'));
children.push(new Table({
  width: { size: 5000, type: WidthType.DXA },
  columnWidths: [1250, 1250, 1250, 1250],
  borders: noBorders,
  rows: [
    new TableRow({ children: [box('1', 'FEF3C7', '92400E', 1250), box('2', 'FEF3C7', '92400E', 1250), box('3', 'FEF3C7', '92400E', 1250), box('4', 'FEF3C7', '92400E', 1250)] }),
    new TableRow({ children: [box('5', 'FEF3C7', '92400E', 1250), spacer(1250), spacer(1250), spacer(1250)] }),
  ],
}));

children.push(quote(
  'Attiva wrap ogni volta che usi max-width: 49% (o simili) sui figli. Senza wrap, i figli si stringono per stare tutti sulla stessa riga.'
));

// ── Sezione 6: Casi pratici ─────────────────────────────────────────────────
children.push(new Paragraph({ children: [new PageBreak()] }));
children.push(h1('Casi pratici risolti'));

children.push(h2('Caso 1 — due tile affiancate al 50%'));
children.push(p('Il modo più semplice: usa il layout della Row.'));
children.push(listNum('Seleziona la Row che contiene i due tile'));
children.push(listNum('Tab Contenuto → Layout → 50 / 50'));
children.push(listNum('Sposta i due tile, uno per colonna (drag&drop)'));
children.push(p('Nessun flex da configurare a mano: ogni colonna è già al 50%.', { italics: true }));

children.push(h2('Caso 2 — tile affiancate dentro la stessa colonna'));
children.push(p('Quando i tile devono stare nella stessa colonna (es. li gestisci come un gruppo):'));
children.push(listNum('Seleziona la Column (parent dei tile)'));
children.push(listNum('Tab Contenuto → blocco Layout Flex'));
children.push(bullet('Direzione: Riga', 1));
children.push(bullet('A capo: Sì', 1));
children.push(bullet('Gap orizzontale: 12 (a piacere)', 1));
children.push(listNum('Su ciascun tile → tab Stile → Larghezza massima: 49%'));
children.push(p('Risultato: tile in fila, vanno a capo automaticamente se l’utente restringe la finestra.', { italics: true }));

children.push(h2('Caso 3 — pulsante sempre a destra di un testo'));
children.push(listNum('Seleziona la Column che contiene [testo, pulsante]'));
children.push(listNum('Layout Flex:'));
children.push(bullet('Direzione: Riga', 1));
children.push(bullet('Giustificazione: Spazio tra (testo a sx, bottone a dx)', 1));
children.push(bullet('Allineamento verticale: Centro', 1));

children.push(h2('Caso 4 — card 3×3 sempre allineate'));
children.push(p('Quando vuoi una griglia 2D ordinata (3 colonne × 3 righe), usa la Riga in modalità Grid:'));
children.push(listNum('Seleziona la Row'));
children.push(listNum('Tab Contenuto → Modalità: Grid'));
children.push(listNum('grid-template-columns: repeat(3, 1fr)'));
children.push(listNum('Aggiungi colonne come al solito: ognuna occupa una cella'));
children.push(p('Con questo approccio non devi configurare il flex — il grid gestisce tutto.', { italics: true }));

// ── Sezione 7: Errori comuni ────────────────────────────────────────────────
children.push(new Paragraph({ children: [new PageBreak()] }));
children.push(h1('Errori comuni'));

children.push(h3('"Ho messo row-reverse ma non cambia nulla"'));
children.push(p('La direzione row-reverse inverte l’ordine dei figli diretti. Se il container ha un solo figlio, niente da invertire. Verifica di aver selezionato il livello giusto (es. la Column, non la Row, se vuoi invertire i tile).'));

children.push(h3('"max-width: 49% e le tile sono ancora una sotto l’altra"'));
children.push(p('max-width da solo non affianca: serve un parent flex container. Configura il "Layout Flex" della colonna parent con Direzione: Riga.'));

children.push(h3('"Su mobile tutto si rompe"'));
children.push(bullet('Per le Row: attiva Impila su mobile (così le colonne diventano 100% sotto i 480px)'));
children.push(bullet('Per le Column con flex orizzontale: aggiungi A capo: Sì così i figli vanno a capo invece di stringersi'));

children.push(h3('"Vorrei spazi tra le card ma il gap non funziona"'));
children.push(p('Controlla se il container è in modalità grid: in quel caso usa grid_gap, non il flex gap.'));

// ── Cheat-sheet ────────────────────────────────────────────────────────────
children.push(new Paragraph({ children: [new PageBreak()] }));
children.push(h1('Cheat-sheet finale'));

children.push(dataTable(
  ['Voglio…', 'Imposta su…', 'Setting'],
  [
    ['2 colonne 50/50', 'Row', 'Layout: 50/50'],
    ['3 card affiancate, larghezza fissa', 'Column con 3 tile', 'Flex direction: row, A capo: sì'],
    ['Pulsante allineato a destra', 'Column', 'Justify: flex-end'],
    ['Centrare verticalmente un tile', 'Column', 'Align items: center'],
    ['Inversione ordine su mobile', 'Column', 'column-reverse (responsive)'],
    ['Layout dashboard complesso', 'Row', 'Modalità: Grid'],
    ['Card uguali di altezza', 'Column', 'Align items: stretch (default)'],
  ]
));

children.push(new Paragraph({ spacing: { before: 400 }, alignment: AlignmentType.CENTER, children: [new TextRun({ text: 'Versione plugin di riferimento: Olobuild ≥ 3.52.18', italics: true, size: 20, color: '64748B', font: 'Calibri' })] }));

// ── Build document ─────────────────────────────────────────────────────────

const doc = new Document({
  creator: 'Olobuild',
  title: 'Guida al Layout in Olobuild',
  description: 'Come disporre tile, colonne e sezioni usando Flex e Grid',
  styles: {
    default: { document: { run: { font: 'Calibri', size: 22 } } },
    paragraphStyles: [
      { id: 'Heading1', name: 'Heading 1', basedOn: 'Normal', next: 'Normal', quickFormat: true,
        run: { size: 36, bold: true, font: 'Calibri', color: '1E3A8A' },
        paragraph: { spacing: { before: 360, after: 180 }, outlineLevel: 0 } },
      { id: 'Heading2', name: 'Heading 2', basedOn: 'Normal', next: 'Normal', quickFormat: true,
        run: { size: 30, bold: true, font: 'Calibri', color: '1E3A8A' },
        paragraph: { spacing: { before: 280, after: 140 }, outlineLevel: 1 } },
      { id: 'Heading3', name: 'Heading 3', basedOn: 'Normal', next: 'Normal', quickFormat: true,
        run: { size: 24, bold: true, font: 'Calibri', color: '475569' },
        paragraph: { spacing: { before: 200, after: 100 }, outlineLevel: 2 } },
    ],
  },
  numbering: {
    config: [
      { reference: 'bullets', levels: [
        { level: 0, format: LevelFormat.BULLET, text: '•', alignment: AlignmentType.LEFT,
          style: { paragraph: { indent: { left: 720, hanging: 360 } } } },
        { level: 1, format: LevelFormat.BULLET, text: '◦', alignment: AlignmentType.LEFT,
          style: { paragraph: { indent: { left: 1440, hanging: 360 } } } },
      ]},
      { reference: 'numbers', levels: [
        { level: 0, format: LevelFormat.DECIMAL, text: '%1.', alignment: AlignmentType.LEFT,
          style: { paragraph: { indent: { left: 720, hanging: 360 } } } },
      ]},
    ],
  },
  sections: [{
    properties: {
      page: {
        size: { width: 11906, height: 16838 }, // A4
        margin: { top: 1440, right: 1440, bottom: 1440, left: 1440 },
      },
    },
    children,
  }],
});

Packer.toBuffer(doc).then(buf => {
  const out = path.join(__dirname, '..', 'docs', 'guida-layout-flex-grid.docx');
  fs.writeFileSync(out, buf);
  console.log('Written:', out, '-', buf.length, 'bytes');
});
