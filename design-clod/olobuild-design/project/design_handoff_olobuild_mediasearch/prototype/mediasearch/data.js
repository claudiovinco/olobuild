// Ricerca Media — mock data & deterministic generators (no real API calls)

const MS_KEYS = {
  unsplash: true, pexels: true, pixabay: false, openverse: true,
  freesound: true, polyhaven: true, googlesv: true,
};

const MS_PROVIDERS = {
  photo: [
    { id: "unsplash",  label: "Unsplash",  key: MS_KEYS.unsplash },
    { id: "pexels",    label: "Pexels",    key: MS_KEYS.pexels },
    { id: "pixabay",   label: "Pixabay",   key: MS_KEYS.pixabay },
    { id: "openverse", label: "Openverse", key: MS_KEYS.openverse },
  ],
  video: [
    { id: "pexels",  label: "Pexels",  key: MS_KEYS.pexels },
    { id: "pixabay", label: "Pixabay", key: MS_KEYS.pixabay },
  ],
  photo360: [
    { id: "polyhaven", label: "Poly Haven · HDRI", key: MS_KEYS.polyhaven },
    { id: "googlesv",  label: "Google Street View", key: MS_KEYS.googlesv },
  ],
  video360: [
    { id: "pexels",  label: "Pexels",  key: MS_KEYS.pexels },
    { id: "pixabay", label: "Pixabay", key: MS_KEYS.pixabay },
  ],
  audio: [
    { id: "freesound", label: "Freesound", key: MS_KEYS.freesound },
  ],
};

const MS_PHOTO_FILTERS = {
  unsplash:  ["orientation"],
  pexels:    ["orientation", "size"],
  pixabay:   ["orientation", "min_width", "min_height"],
  openverse: ["orientation", "size"],
};

const MS_TABS = [
  { id: "photo",    label: "Foto" },
  { id: "video",    label: "Video" },
  { id: "photo360", label: "Foto 360" },
  { id: "video360", label: "Video 360" },
  { id: "audio",    label: "Audio" },
];

const MS_QUICK = ["hotel resort", "spiaggia tramonto", "colazione", "spa benessere", "piscina", "montagna"];

// ── deterministic pseudo-random from string seed ──
function msHash(str) {
  let h = 2166136261;
  for (let i = 0; i < str.length; i++) { h ^= str.charCodeAt(i); h = Math.imul(h, 16777619); }
  return (h >>> 0);
}
function msRng(seed) {
  let s = msHash(seed) || 1;
  return function () {
    s ^= s << 13; s ^= s >>> 17; s ^= s << 5; s >>>= 0;
    return s / 4294967296;
  };
}

const MS_AUTHORS = [
  "Elena Marchetti", "Jonas Weber", "Aiyana Lewis", "Marco Bellini", "Sofia Lindqvist",
  "Tomás Ferreira", "Yuki Tanaka", "Lucia Romano", "Pierre Dubois", "Anna Kowalska",
  "Diego Martín", "Ingrid Olsen",
];

const MS_AR = [ [4,3], [3,4], [16,9], [1,1], [3,2], [2,3], [21,9], [4,5] ];

function msPhotos(query, page, provider) {
  const rnd = msRng(provider + "|" + query + "|" + page);
  const out = [];
  for (let i = 0; i < 18; i++) {
    const ar = MS_AR[Math.floor(rnd() * MS_AR.length)];
    const w = 5200 + Math.floor(rnd() * 2800);
    const h = Math.round(w * ar[1] / ar[0]);
    out.push({
      id: "ph-" + page + "-" + i,
      seed: query.replace(/\s+/g, "-") + "-" + provider + "-" + page + "-" + i,
      arw: ar[0], arh: ar[1], w, h,
      photographer: MS_AUTHORS[Math.floor(rnd() * MS_AUTHORS.length)],
    });
  }
  return out;
}

function msVideos(query, page, provider) {
  const rnd = msRng("v|" + provider + "|" + query + "|" + page);
  const out = [];
  for (let i = 0; i < 12; i++) {
    out.push({
      id: "vd-" + page + "-" + i,
      seed: "vid-" + query.replace(/\s+/g, "-") + "-" + provider + "-" + page + "-" + i,
      duration: 6 + Math.floor(rnd() * 174),
      w: 3840, h: 2160,
      photographer: MS_AUTHORS[Math.floor(rnd() * MS_AUTHORS.length)],
    });
  }
  return out;
}

const MS_HDRI_NAMES = [
  "Venice Sunset", "Kloppenheim Dawn", "Spiaggia Pier", "Alpine Meadow", "Hotel Lobby",
  "Courtyard Night", "Studio Soft", "Harbor Morning", "Terrazza Estate", "Forest Path",
  "Rooftop Dusk", "Lakeside Calm",
];
const MS_HDRI_TAGS = ["outdoor", "sunset", "interno", "natura", "urbano", "skies", "4k+", "notte"];

function msHdris(query, page) {
  const rnd = msRng("h|" + query + "|" + page);
  return MS_HDRI_NAMES.map(function (name, i) {
    const tags = [];
    const n = 2 + Math.floor(rnd() * 2);
    while (tags.length < n) {
      const t = MS_HDRI_TAGS[Math.floor(rnd() * MS_HDRI_TAGS.length)];
      if (tags.indexOf(t) < 0) tags.push(t);
    }
    return {
      id: "hd-" + page + "-" + i,
      seed: "hdri-" + name.replace(/\s+/g, "-") + "-" + page,
      name: name, tags: tags,
      author: rnd() > 0.5 ? "Greg Zaal" : "Sergej Majboroda",
    };
  });
}

const MS_AUDIO_NAMES = [
  ["Ambient Lounge Loop", "loop · stereo"], ["Onde sulla riva", "field recording"],
  ["Soft Piano Intro", "musica"], ["Vento tra i pini", "field recording"],
  ["Uplifting Corporate", "musica"], ["Campanelle Spa", "fx"],
  ["Caffè del mattino", "ambience"], ["Deep House Bed", "loop · 124 bpm"],
  ["Gabbiani al porto", "field recording"], ["Notification Soft", "fx"],
];

function msAudios(query, page) {
  const rnd = msRng("a|" + query + "|" + page);
  return MS_AUDIO_NAMES.map(function (pair, i) {
    const bars = [];
    for (let b = 0; b < 64; b++) {
      const env = Math.sin((b / 63) * Math.PI);
      bars.push(Math.max(0.08, Math.min(1, env * (0.35 + rnd() * 0.75))));
    }
    return {
      id: "au-" + page + "-" + i,
      name: pair[0], kind: pair[1],
      duration: 4 + Math.floor(rnd() * 236),
      license: rnd() > 0.4 ? "CC0" : "CC-BY 4.0",
      author: MS_AUTHORS[Math.floor(rnd() * MS_AUTHORS.length)],
      downloads: 120 + Math.floor(rnd() * 9000),
      bars: bars,
    };
  });
}

function msTotal(query, tab, provider) {
  const h = msHash(tab + "|" + provider + "|" + query);
  if (tab === "audio") return 180 + (h % 1400);
  if (tab === "photo360") return 24 + (h % 420);
  if (tab.indexOf("video") === 0) return 90 + (h % 2200);
  return 400 + (h % 11000);
}

function msFmtDur(sec) {
  const m = Math.floor(sec / 60), s = sec % 60;
  return m + ":" + (s < 10 ? "0" : "") + s;
}
function msFmtNum(n) {
  return String(n).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

Object.assign(window, {
  MS_KEYS, MS_PROVIDERS, MS_PHOTO_FILTERS, MS_TABS, MS_QUICK,
  msPhotos, msVideos, msHdris, msAudios, msTotal, msFmtDur, msFmtNum, msRng,
});
