<template>
  <!-- La SEO del sito in un punto solo (1.4.1265): in cima i passi della prima configurazione, ognuno col suo stato;
       sotto, un riquadro per passo, fino all'elenco delle pagine con titolo e descrizione da sistemare lì.
       Le righe sono scritte una per una (non in un v-for): l'indice della ricerca nelle Impostazioni legge dal
       template etichette e titoli scritti per esteso (scripts/build-settings-search-index.cjs). -->
  <div class="cfg-page-head">
    <div>
      <h1>SEO <em>{{ t('del sito') }}</em></h1>
      <p>{{ t('Tutto quello che serve perché Google e le intelligenze artificiali trovino il sito e lo presentino bene. Parti dal primo passo e scendi: ognuno dice se è fatto.') }}</p>
    </div>
    <div class="head-actions">
      <a class="cfg-btn cfg-btn-secondary" :href="provaGoogle" target="_blank" rel="noopener">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
        {{ t('Prova su Google') }}
      </a>
    </div>
  </div>

  <div v-if="riepilogo.altro_plugin" class="seo-avviso" role="alert">
    {{ t('%s è attivo: la testata delle pagine la scrive lui e queste impostazioni non agiscono. Per usare la SEO di OLObuild disattivalo.').replace('%s', riepilogo.altro_plugin) }}
  </div>

  <!-- ─── I passi ─── -->
  <div class="cfg-card seo-passi">
    <div class="cfg-card-head">
      <div class="head-ic">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
      </div>
      <div>
        <h3>{{ t('La SEO in dieci passi') }}</h3>
        <p>{{ fattiTesto }}</p>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="seo-barra" role="progressbar" :aria-valuenow="fatti" aria-valuemin="0" :aria-valuemax="passi.length" :aria-label="fattiTesto">
        <span :style="{ width: (fatti / passi.length * 100) + '%' }"></span>
      </div>
      <ol class="seo-elenco">
        <li v-for="p in passi" :key="p.n" :class="'is-' + p.stato">
          <span class="seo-segno" aria-hidden="true">
            <svg v-if="p.stato === 'ok'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
            <svg v-else-if="p.stato === 'attenzione'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"><path d="M12 7v6M12 17h.01"/></svg>
            <span v-else>{{ p.n }}</span>
          </span>
          <span class="seo-passo-testi">
            <span class="seo-passo-nome">{{ p.titolo }}</span>
            <span class="seo-passo-stato">{{ p.testo }}</span>
          </span>
          <button type="button" class="cfg-btn cfg-btn-ghost seo-vai" @click="vai(p.n)">{{ p.stato === 'ok' ? t('Rivedi') : t('Vai') }}</button>
        </li>
      </ol>
    </div>
  </div>

  <!-- ─── 1. Visibile ai motori ─── -->
  <div id="seo-passo-1" class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic seo-num">1</div>
      <div>
        <h3>{{ t('Il sito si vede sui motori') }} <InfoTip :titolo="t('Il sito si vede sui motori')" :testo="t('Mentre costruisci il sito puoi tenerlo nascosto. Quando è pronto, aprilo.')" /></h3>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Visibile ai motori') }}</label>
          <InfoTip :titolo="t('Visibile ai motori')" :testo="t('Spento, il sito chiede a Google e agli altri motori di non mostrarlo. È la stessa scelta di Impostazioni › Lettura di WordPress.')" />
        </div>
        <div class="control-col seo-riga">
          <button class="cfg-switch" :class="{ 'is-on': titles.robots === 'index' }" role="switch" :aria-checked="titles.robots === 'index' ? 'true' : 'false'"
            :aria-label="t('Visibile ai motori')" @click="setT('robots', titles.robots === 'index' ? 'noindex' : 'index')"></button>
          <span class="seo-nota" :class="titles.robots === 'index' ? 'is-ok' : 'is-att'">{{ titles.robots === 'index' ? t('Il sito si può trovare.') : t('Il sito è nascosto.') }}</span>
        </div>
      </div>
    </div>
  </div>

  <!-- ─── 2. Chi pubblica il sito ─── -->
  <div id="seo-passo-2" class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic seo-num">2</div>
      <div>
        <h3>{{ t('Chi pubblica il sito') }} <InfoTip :titolo="t('Chi pubblica il sito')" :testo="t('Chi sei, dove sei e come ti si trova. Google e le intelligenze artificiali lo leggono in ogni pagina e collegano il sito a te.')" /></h3>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Chi sei') }}</label>
          <InfoTip :titolo="t('Chi sei')" :testo="infoTipo" />
        </div>
        <div class="control-col">
          <CfgSelect size="md" searchable :model-value="titles.kg_type" :options="opzioniTipo" @update:model-value="setT('kg_type', $event)" />
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Nome') }}</label>
          <InfoTip :titolo="t('Nome')" :testo="t('Come ti chiamano le persone. Se resta vuoto, il nome del sito.')" />
        </div>
        <div class="control-col">
          <div class="cfg-input cfg-w-md"><input type="text" :value="titles.kg_name" :placeholder="riepilogo.nome_sito" :aria-label="t('Nome')" @input="setT('kg_name', $event.target.value)" /></div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Logo') }}</label>
          <InfoTip :titolo="t('Logo')" :testo="t('Quadrato o orizzontale, largo almeno 112 px, su fondo chiaro.')" />
        </div>
        <div class="control-col seo-riga">
          <span class="seo-logo" :class="{ 'is-vuoto': !logo }">
            <img v-if="logo" :src="logo" :alt="t('Logo')" />
            <span v-else>{{ t('Nessun logo') }}</span>
          </span>
          <span v-if="!titles.kg_logo && riepilogo.logo_tema" class="seo-nota">{{ t('Ora esce il logo del tema.') }}</span>
          <button type="button" class="cfg-btn cfg-btn-secondary" @click="scegliImmagine((url) => setT('kg_logo', url), t('Scegli il logo'))">{{ titles.kg_logo ? t('Sostituisci') : t('Scegli il logo') }}</button>
          <button v-if="titles.kg_logo" type="button" class="cfg-btn cfg-btn-ghost" @click="setT('kg_logo', '')">{{ t('Togli') }}</button>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Telefono ed email') }}</label>
          <InfoTip :titolo="t('Telefono ed email')" :testo="t('I contatti che vuoi mostrare. Il telefono col prefisso internazionale: +39 …')" />
        </div>
        <div class="control-col seo-griglia due">
          <div class="cfg-input"><input type="tel" :value="locale.phone" :placeholder="t('Telefono')" :aria-label="t('Telefono')" @input="setL('phone', $event.target.value)" /></div>
          <div class="cfg-input"><input type="email" :value="locale.email" :placeholder="t('Email')" :aria-label="t('Email')" @input="setL('email', $event.target.value)" /></div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Indirizzo') }}</label>
          <InfoTip :titolo="t('Indirizzo')" :testo="t('Comincia a scrivere la via: scegli l\'indirizzo giusto fra quelli proposti e CAP, città e posizione sulla mappa si compilano da soli.')" />
        </div>
        <div class="control-col seo-griglia indirizzo">
          <!-- Indirizzi proposti mentre si scrive (Olobuild_Seo_Centro::rest_indirizzi, dati di OpenStreetMap) -->
          <div class="via seo-via">
            <div class="cfg-input">
              <input type="text" :value="locale.address.street" :placeholder="t('Via e numero')" :aria-label="t('Via e numero')"
                autocomplete="off" role="combobox" aria-autocomplete="list" aria-controls="seo-proposte" :aria-expanded="proposteAperte ? 'true' : 'false'"
                :aria-activedescendant="proposteAperte && evidenziata >= 0 ? 'seo-proposta-' + evidenziata : null"
                @input="onVia($event.target.value)" @keydown="tastiVia" @focus="proposte.length && (proposteAperte = true)" @blur="chiudiProposte" />
              <span v-if="cercaInd" class="seo-giro" aria-hidden="true"></span>
            </div>
            <ul v-if="proposteAperte && (proposte.length || erroreInd)" id="seo-proposte" class="seo-proposte" role="listbox" :aria-label="t('Indirizzi trovati')">
              <li v-for="(pr, i) in proposte" :id="'seo-proposta-' + i" :key="i" role="option" :aria-selected="i === evidenziata ? 'true' : 'false'"
                :class="{ 'is-on': i === evidenziata }" @mousedown.prevent="scegliIndirizzo(pr)" @mousemove="evidenziata = i">
                <b>{{ pr.via || pr.nome || pr.citta }}<span v-if="pr.nome && pr.via"> · {{ pr.nome }}</span></b>
                <small>{{ pr.dove }}</small>
              </li>
              <li v-if="erroreInd" class="seo-proposte-errore" role="presentation">{{ erroreInd }}</li>
              <li class="seo-proposte-fonte" role="presentation">{{ t('Indirizzi da OpenStreetMap') }}</li>
            </ul>
          </div>
          <div class="cfg-input"><input type="text" :value="locale.address.zip" :placeholder="t('CAP')" :aria-label="t('CAP')" @input="setInd('zip', $event.target.value)" /></div>
          <div class="cfg-input"><input type="text" :value="locale.address.city" :placeholder="t('Città')" :aria-label="t('Città')" @input="setInd('city', $event.target.value)" /></div>
          <div class="cfg-input"><input type="text" :value="locale.address.state" :placeholder="t('Provincia')" :aria-label="t('Provincia')" @input="setInd('state', $event.target.value)" /></div>
          <div class="cfg-input"><input type="text" maxlength="2" :value="locale.address.country" :placeholder="t('Paese')" :aria-label="t('Paese')" @input="setInd('country', $event.target.value.toUpperCase())" /></div>
        </div>
      </div>
      <template v-if="tipoScelto.sede">
        <div class="cfg-row">
          <div class="label-col">
            <label>{{ t('Orari') }}</label>
            <InfoTip :titolo="t('Orari')" :testo="t('Quando la sede è aperta. Lascia vuoti i giorni di chiusura.')" />
          </div>
          <div class="control-col">
            <div class="seo-orari">
              <div v-for="g in GIORNI" :key="g.k" class="seo-giorno">
                <span class="seo-giorno-nome">{{ g.nome }}</span>
                <span class="cfg-input"><input type="time" :value="(locale.hours[g.k] || {}).open || ''" :aria-label="g.nome + ' — ' + t('apre')" @input="setOra(g.k, 'open', $event.target.value)" /></span>
                <span class="seo-trattino">–</span>
                <span class="cfg-input"><input type="time" :value="(locale.hours[g.k] || {}).close || ''" :aria-label="g.nome + ' — ' + t('chiude')" @input="setOra(g.k, 'close', $event.target.value)" /></span>
              </div>
            </div>
            <button type="button" class="cfg-btn cfg-btn-ghost seo-copia" @click="copiaLunedi">{{ t('Copia il lunedì sugli altri giorni') }}</button>
          </div>
        </div>
        <div class="cfg-row">
          <div class="label-col">
            <label>{{ t('Posizione sulla mappa') }}</label>
            <InfoTip :titolo="t('Posizione sulla mappa')" :testo="t('Si compila da sola quando scegli l\'indirizzo. Se il punto non è giusto, su Google Maps fai clic destro sul punto e incolla qui i due numeri.')" />
          </div>
          <div class="control-col seo-griglia due">
            <div class="cfg-input mono"><input type="text" inputmode="decimal" :value="locale.geo_lat" placeholder="44.4056" :aria-label="t('Latitudine')" @input="setL('geo_lat', $event.target.value)" @paste="incollaCoordinate" /></div>
            <div class="cfg-input mono"><input type="text" inputmode="decimal" :value="locale.geo_lng" placeholder="8.9463" :aria-label="t('Longitudine')" @input="setL('geo_lng', $event.target.value)" /></div>
            <div class="seo-mappa-riga">
              <button type="button" class="cfg-btn cfg-btn-secondary" :disabled="cercaInd || !indirizzoTesto" @click="trovaCoordinate">{{ t('Trova dall\'indirizzo') }}</button>
              <a v-if="haCoordinate" class="seo-link" :href="'https://www.openstreetmap.org/?mlat=' + locale.geo_lat + '&mlon=' + locale.geo_lng + '#map=18/' + locale.geo_lat + '/' + locale.geo_lng" target="_blank" rel="noopener">{{ t('Apri la mappa →') }}</a>
            </div>
            <iframe v-if="haCoordinate" class="seo-mappa" :src="urlMappa" loading="lazy" referrerpolicy="no-referrer" :title="t('La sede sulla mappa')"></iframe>
          </div>
        </div>
        <div class="cfg-row">
          <div class="label-col">
            <label>{{ t('Fascia di prezzo') }}</label>
            <InfoTip :titolo="t('Fascia di prezzo')" :testo="t('Quanto si spende, da € a €€€€.')" />
          </div>
          <div class="control-col">
            <div class="cfg-segment">
              <button v-for="f in FASCE" :key="f" :class="{ 'is-on': locale.price_range === f }" @click="setL('price_range', locale.price_range === f ? '' : f)">{{ f || t('Nessuna') }}</button>
            </div>
          </div>
        </div>
      </template>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Profili') }}</label>
          <InfoTip :titolo="t('Profili')" :testo="t('L\'indirizzo intero di ogni profilo. Servono a dire che il sito e i profili sono della stessa persona o attività.')" />
        </div>
        <div class="control-col seo-griglia due">
          <div v-for="pr in PROFILI" :key="pr.k" class="cfg-input with-prefix">
            <span class="prefix">{{ pr.nome }}</span>
            <input type="url" :value="social[pr.k]" :placeholder="pr.es" :aria-label="pr.nome" @input="setS(pr.k, $event.target.value.trim())" />
          </div>
          <div class="cfg-input with-prefix">
            <span class="prefix">X @</span>
            <input type="text" :value="(social.twitter_user || '').replace(/^@/, '')" :placeholder="t('nome del profilo')" :aria-label="t('Profilo X')" @input="setS('twitter_user', $event.target.value.replace(/^@/, '').trim())" />
          </div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Altri profili') }}</label>
          <InfoTip :titolo="t('Altri profili')" :testo="t('Uno per riga: la pagina di Wikipedia, Wikidata, un albo professionale, una guida. Più legami, più i motori sono sicuri di chi sei.')" />
        </div>
        <div class="control-col">
          <div class="cfg-textarea"><textarea rows="2" :value="social.altri_profili" placeholder="https://it.wikipedia.org/wiki/…" :aria-label="t('Altri profili')" @input="setS('altri_profili', $event.target.value)"></textarea></div>
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Come ti vedono') }}</label>
          <InfoTip :titolo="t('Come ti vedono')" :testo="t('Quello che Google e le intelligenze artificiali leggono di te in ogni pagina del sito.')" />
        </div>
        <div class="control-col">
          <div class="seo-scheda">
            <div class="seo-scheda-testa">
              <img v-if="logo" :src="logo" alt="" class="seo-scheda-logo" />
              <div>
                <div class="seo-scheda-nome">{{ titles.kg_name || riepilogo.nome_sito }}</div>
                <div class="seo-scheda-tipo">{{ tipoScelto.label }}</div>
              </div>
            </div>
            <dl class="seo-scheda-dati">
              <template v-if="indirizzoTesto"><dt>{{ t('Indirizzo') }}</dt><dd>{{ indirizzoTesto }}</dd></template>
              <template v-if="locale.phone"><dt>{{ t('Telefono') }}</dt><dd>{{ locale.phone }}</dd></template>
              <template v-if="tipoScelto.sede && orariTesto.length"><dt>{{ t('Orari') }}</dt><dd><span v-for="o in orariTesto" :key="o" class="seo-scheda-ora">{{ o }}</span></dd></template>
              <template v-if="tipoScelto.sede && locale.price_range"><dt>{{ t('Prezzi') }}</dt><dd>{{ locale.price_range }}</dd></template>
              <template v-if="numeroProfili"><dt>{{ t('Profili') }}</dt><dd>{{ numeroProfili }}</dd></template>
            </dl>
            <p v-if="!indirizzoTesto && !locale.phone" class="seo-scheda-vuota">{{ t('Aggiungi indirizzo e telefono: sono le prime cose che una persona cerca.') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ─── 3. Come appare su Google ─── -->
  <div id="seo-passo-3" class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic seo-num">3</div>
      <div>
        <h3>{{ t('Come appare su Google') }} <InfoTip :titolo="t('Come appare su Google')" :testo="t('Il titolo e la descrizione che compaiono nei risultati della ricerca, quando una pagina non ne ha di suoi.')" /></h3>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Schema del titolo') }}</label>
          <InfoTip :titolo="t('Schema del titolo')" :testo="t('{page} è il titolo della pagina, {sep} il separatore, {site} il nome del sito. La home usa il nome del sito e il motto.')" />
        </div>
        <div class="control-col">
          <div class="cfg-input mono cfg-w-md"><input type="text" :value="titles.pattern" placeholder="{page} {sep} {site}" :aria-label="t('Schema del titolo')" @input="setT('pattern', $event.target.value)" /></div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Separatore') }}</label>
          <InfoTip :titolo="t('Separatore')" :testo="t('Il segno fra il titolo della pagina e il nome del sito.')" />
        </div>
        <div class="control-col">
          <div class="cfg-segment seo-separatori">
            <button v-for="s in SEPARATORI" :key="s" :class="{ 'is-on': titles.separator === s }" :aria-label="t('Separatore') + ' ' + s" @click="setT('separator', s)">{{ s }}</button>
          </div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Descrizione del sito') }}</label>
          <InfoTip :titolo="t('Descrizione del sito')" :testo="t('Due frasi su chi sei e cosa offri, fino a 160 caratteri. Vale per la home e per le pagine senza una descrizione loro.')" />
        </div>
        <div class="control-col">
          <div class="cfg-textarea seo-testo"><textarea rows="3" :value="titles.description" :aria-label="t('Descrizione del sito')" @input="setT('description', $event.target.value.slice(0, 160))"></textarea></div>
          <div class="seo-conta" :class="contaClasse((titles.description || '').length, 70, 160)">{{ (titles.description || '').length }} / 160</div>
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Anteprima della home') }}</label>
          <InfoTip :titolo="t('Anteprima della home')" :testo="t('Così la home compare fra i risultati di Google.')" />
        </div>
        <div class="control-col">
          <div class="seo-serp">
            <div class="seo-serp-sito"><span class="seo-serp-ico">{{ (titles.kg_name || riepilogo.nome_sito || '?').charAt(0) }}</span><span><b>{{ titles.kg_name || riepilogo.nome_sito }}</b><br>{{ riepilogo.host }}</span></div>
            <div class="seo-serp-titolo">{{ homeTitolo }}</div>
            <div class="seo-serp-desc" :class="{ 'is-vuota': !homeDescrizione }">{{ homeDescrizione || t('Nessuna descrizione: Google ne prende un pezzo a caso dalla pagina.') }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ─── 4. Quando lo condividi ─── -->
  <div id="seo-passo-4" class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic seo-num">4</div>
      <div>
        <h3>{{ t('Come appare quando lo condividi') }} <InfoTip :titolo="t('Come appare quando lo condividi')" :testo="t('L\'immagine e il titolo che compaiono quando qualcuno manda un link del sito su WhatsApp, Facebook, LinkedIn o X.')" /></h3>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Immagine disegnata') }}</label>
          <InfoTip :titolo="t('Immagine disegnata')" :testo="t('Per ogni pagina senza un\'immagine sua: il titolo, il nome del sito e la foto in evidenza, nel colore e nel carattere del sito.')" />
        </div>
        <div class="control-col">
          <button class="cfg-switch" :class="{ 'is-on': social.og_disegnata }" role="switch" :aria-checked="social.og_disegnata ? 'true' : 'false'"
            :aria-label="t('Immagine disegnata')" @click="setS('og_disegnata', !social.og_disegnata)"></button>
          <template v-if="social.og_disegnata">
            <div class="cfg-segment og-stili">
              <button v-for="s in OG_STILI" :key="s.value" :class="{ 'is-on': (social.og_stile || 'foto') === s.value }" @click="setS('og_stile', s.value)">{{ s.label }}</button>
            </div>
            <div class="og-disegnata">
              <img v-if="ogAnteprima.url" :src="ogAnteprima.url" :alt="t('Anteprima dell\'immagine disegnata')" width="1200" height="630" />
              <span v-else class="og-disegnata-vuota">{{ ogAnteprima.errore || t('Disegno l\'anteprima…') }}</span>
            </div>
            <div v-if="ogAnteprima.titolo" class="seo-conta">{{ t('Prova su: %s').replace('%s', ogAnteprima.titolo) }}</div>
          </template>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Immagine di riserva') }}</label>
          <InfoTip :titolo="t('Immagine di riserva')" :testo="t('1200 × 630 px. Esce quando una pagina non ha un\'immagine sua e l\'immagine disegnata è spenta.')" />
        </div>
        <div class="control-col">
          <div class="og-preview-wrap">
            <div class="og-preview" :style="ogPreviewStyle">
              <span v-if="!social.og_default_image">{{ t('Nessuna') }}</span>
            </div>
            <div class="og-actions">
              <button class="cfg-btn cfg-btn-secondary" @click="scegliImmagine((url) => setS('og_default_image', url), t('Scegli l\'immagine'))">
                {{ social.og_default_image ? t('Sostituisci') : t('Scegli l\'immagine') }}
              </button>
              <button v-if="social.og_default_image" type="button" class="cfg-btn cfg-btn-ghost" @click="setS('og_default_image', '')">{{ t('Togli') }}</button>
            </div>
          </div>
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Formato su X') }}</label>
          <InfoTip :titolo="t('Formato su X')" :testo="t('Grande: l\'immagine occupa tutta la larghezza. Piccola: un quadratino accanto al titolo.')" />
        </div>
        <div class="control-col">
          <div class="cfg-segment">
            <button :class="{ 'is-on': social.twitter_card_type !== 'summary' }" @click="setS('twitter_card_type', 'summary_large_image')">{{ t('Grande') }}</button>
            <button :class="{ 'is-on': social.twitter_card_type === 'summary' }" @click="setS('twitter_card_type', 'summary')">{{ t('Piccola') }}</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ─── 5. Cosa leggono i motori ─── -->
  <div id="seo-passo-5" class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic seo-num">5</div>
      <div>
        <h3>{{ t('Cosa leggono i motori') }} <InfoTip :titolo="t('Cosa leggono i motori')" :testo="t('La mappa del sito che i motori leggono, e le parti che non vale la pena mostrare.')" /></h3>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Sitemap') }}</label>
          <InfoTip :titolo="t('Sitemap')" :testo="t('L\'elenco di tutte le pagine da mostrare, aggiornato da solo. Le pagine escluse non ci sono.')" />
        </div>
        <div class="control-col seo-riga">
          <button class="cfg-switch" :class="{ 'is-on': sitemap.enabled }" role="switch" :aria-checked="sitemap.enabled ? 'true' : 'false'" :aria-label="t('Sitemap')" @click="setSM('enabled', !sitemap.enabled)"></button>
          <a v-if="sitemap.enabled" class="seo-link mono" :href="riepilogo.url.sitemap" target="_blank" rel="noopener">{{ riepilogo.url.sitemap }}</a>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Tipi di contenuto') }}</label>
          <InfoTip :titolo="t('Tipi di contenuto')" :testo="t('Spegni un tipo per toglierlo dai motori con tutte le sue schede, per esempio un tipo che serve solo dentro altre pagine.')" />
        </div>
        <div class="control-col">
          <div class="seo-scelte">
            <div v-for="tc in riepilogo.tipi_contenuto" :key="tc.slug" class="seo-scelta">
              <button class="cfg-switch" :class="{ 'is-on': !advanced.noindex_pt[tc.slug] }" role="switch" :aria-checked="!advanced.noindex_pt[tc.slug] ? 'true' : 'false'" :aria-label="tc.label" @click="setPt(tc.slug, !advanced.noindex_pt[tc.slug])"></button>
              <span>{{ tc.label }} <small>{{ tc.count }}</small></span>
            </div>
          </div>
        </div>
      </div>
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Archivi') }}</label>
          <InfoTip :titolo="t('Archivi')" :testo="t('Le pagine che WordPress crea da sé per raccogliere i contenuti. Accese, i motori le mostrano.')" />
        </div>
        <div class="control-col">
          <div class="seo-scelte">
            <div v-for="a in archivi" :key="a.k" class="seo-scelta">
              <button class="cfg-switch" :class="{ 'is-on': !advanced[a.k] }" role="switch" :aria-checked="!advanced[a.k] ? 'true' : 'false'" :aria-label="a.nome" @click="setA(a.k, !advanced[a.k])"></button>
              <span>{{ a.nome }} <small>{{ a.consiglio }}</small></span>
            </div>
          </div>
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Righe in più nel robots.txt') }}</label>
          <InfoTip :titolo="t('Righe in più nel robots.txt')" :testo="t('Solo se sai cosa scrivere: le righe vanno in cima al file che dice ai robot cosa leggere.')" />
        </div>
        <div class="control-col">
          <div class="cfg-textarea mono"><textarea rows="3" :value="advanced.robots_txt" placeholder="Disallow: /area-riservata/" :aria-label="t('Righe in più nel robots.txt')" @input="setA('robots_txt', $event.target.value)"></textarea></div>
          <div class="seo-conta">
            <span v-if="riepilogo.sottocartella" class="is-att">{{ t('Il sito sta in una sottocartella: i robot leggono solo il robots.txt della radice del dominio.') }}</span>
            <a v-else class="seo-link" :href="riepilogo.url.robots" target="_blank" rel="noopener">{{ t('Apri il robots.txt →') }}</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ─── 6. Le intelligenze artificiali ─── -->
  <div id="seo-passo-6" class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic seo-num">6</div>
      <div>
        <h3>{{ t('Le intelligenze artificiali') }} <InfoTip :titolo="t('Le intelligenze artificiali')" :testo="t('ChatGPT, Claude, Perplexity e gli altri leggono i siti per rispondere a chi chiede. Qui scegli chi può farlo e gli prepari un riassunto.')" /></h3>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Chi può leggere il sito') }}</label>
          <InfoTip :titolo="t('Chi può leggere il sito')" :testo="t('«Solo per rispondere» lascia entrare le IA quando cercano una risposta e tiene fuori chi copia i siti per addestrare i modelli.')" />
        </div>
        <div class="control-col">
          <div class="cfg-segment">
            <button :class="{ 'is-on': advanced.ia_bot === 'tutti' }" @click="setA('ia_bot', 'tutti')">{{ t('Tutte') }}</button>
            <button :class="{ 'is-on': advanced.ia_bot === 'senza-addestramento' }" @click="setA('ia_bot', 'senza-addestramento')">{{ t('Solo per rispondere') }}</button>
            <button :class="{ 'is-on': advanced.ia_bot === 'nessuno' }" @click="setA('ia_bot', 'nessuno')">{{ t('Nessuna') }}</button>
          </div>
          <div class="seo-conta">
            <template v-if="botFuori.length">{{ t('Fuori:') }} <span class="mono">{{ botFuori.join(' · ') }}</span></template>
            <template v-else>{{ t('Tutte le IA possono leggere il sito. Le pagine arrivano intere, anche le tile che le persone vedono scorrendo.') }}</template>
          </div>
          <div v-if="riepilogo.sottocartella && botFuori.length" class="seo-conta is-att">{{ t('Il sito sta in una sottocartella: la scelta vale solo se la scrivi nel robots.txt della radice del dominio.') }}</div>
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Riassunto per le IA') }}</label>
          <InfoTip :titolo="t('Riassunto per le IA')" :testo="t('Il file llms.txt: chi sei, i contatti e le pagine del sito con la loro descrizione, scritti in modo che un\'IA li legga in un colpo.')" />
        </div>
        <div class="control-col seo-riga">
          <button class="cfg-switch" :class="{ 'is-on': advanced.llms }" role="switch" :aria-checked="advanced.llms ? 'true' : 'false'" :aria-label="t('Riassunto per le IA')" @click="setA('llms', !advanced.llms)"></button>
          <a v-if="advanced.llms && llmsSalvato" class="seo-link mono" :href="riepilogo.url.llms" target="_blank" rel="noopener">{{ riepilogo.url.llms }}</a>
          <span v-else-if="advanced.llms" class="seo-nota is-att">{{ t('Salva per pubblicarlo.') }}</span>
        </div>
      </div>
    </div>
  </div>

  <!-- ─── 7. Verifica dei motori ─── -->
  <div id="seo-passo-7" class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic seo-num">7</div>
      <div>
        <h3>{{ t('Verifica dei motori') }} <InfoTip :titolo="t('Verifica dei motori')" :testo="t('Dimostri a Google e a Bing che il sito è tuo. Da lì vedi con quali ricerche arrivano le persone e cosa non va.')" /></h3>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Google Search Console') }}</label>
          <InfoTip :titolo="t('Google Search Console')" :testo="t('Il modo migliore è la proprietà «Dominio» col record TXT nel DNS: qui non serve niente. Se non puoi toccare il DNS, scegli «Prefisso URL» e la verifica «Tag HTML», e incolla qui il codice del meta tag.')" />
        </div>
        <div class="control-col">
          <div class="cfg-input mono cfg-w-md">
            <input type="text" :value="webmaster.google" :aria-label="t('Google Search Console')"
              :placeholder="t('Incolla il codice o tutto il meta tag')"
              @input="setW('google', $event.target.value)" @change="pulisciW('google', $event)" />
          </div>
          <div class="seo-conta verifica-stato">
            <span v-if="dns.google.length" class="verifica-ok verifica-dns">{{ testoDns }}</span>
            <span v-if="statoVerifica('google') === 'ok'" class="verifica-ok">{{ t('Il codice è nella testata di ogni pagina.') }}</span>
            <span v-else-if="statoVerifica('google') === 'metti'" class="verifica-attesa">{{ t('Salva per metterlo nella testata.') }}</span>
            <span v-else-if="statoVerifica('google') === 'togli'" class="verifica-attesa">{{ t('Salva per toglierlo dalla testata.') }}</span>
            <a href="https://search.google.com/search-console" target="_blank" rel="noopener">{{ t('Apri la Search Console →') }}</a>
          </div>
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Bing Webmaster Tools') }}</label>
          <InfoTip :titolo="t('Bing Webmaster Tools')" :testo="t('Vale anche per Copilot. In Bing Webmaster Tools scegli la verifica col meta tag; puoi anche importare il sito dalla Search Console.')" />
        </div>
        <div class="control-col">
          <div class="cfg-input mono cfg-w-md">
            <input type="text" :value="webmaster.bing" :aria-label="t('Bing Webmaster Tools')"
              :placeholder="t('Incolla il codice o tutto il meta tag')"
              @input="setW('bing', $event.target.value)" @change="pulisciW('bing', $event)" />
          </div>
          <div class="seo-conta verifica-stato">
            <span v-if="statoVerifica('bing') === 'ok'" class="verifica-ok">{{ t('Il codice è nella testata di ogni pagina.') }}</span>
            <span v-else-if="statoVerifica('bing') === 'metti'" class="verifica-attesa">{{ t('Salva per metterlo nella testata.') }}</span>
            <span v-else-if="statoVerifica('bing') === 'togli'" class="verifica-attesa">{{ t('Salva per toglierlo dalla testata.') }}</span>
            <a href="https://www.bing.com/webmasters" target="_blank" rel="noopener">{{ t('Apri Bing Webmaster Tools →') }}</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ─── 8. IndexNow ─── -->
  <div id="seo-passo-8" class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic seo-num">8</div>
      <div>
        <h3>{{ t('Avvisa i motori quando cambi qualcosa') }} <InfoTip :titolo="t('Avvisa i motori quando cambi qualcosa')" :testo="t('Con IndexNow, ogni volta che pubblichi o aggiorni una pagina, Bing, Yandex e gli altri motori aderenti lo sanno subito. Google legge la sitemap.')" /></h3>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row" :class="{ 'no-divider': !chiaveValida }">
        <div class="label-col">
          <label>{{ t('Chiave IndexNow') }}</label>
          <InfoTip :titolo="t('Chiave IndexNow')" :testo="t('Premi «Genera»: la chiave è solo tua e non va registrata da nessuna parte.')" />
        </div>
        <div class="control-col seo-riga">
          <div class="cfg-input mono cfg-w-md" :class="{ 'is-error': advanced.indexnow_key && !chiaveValida }">
            <input type="text" :value="advanced.indexnow_key" placeholder="abcd1234efgh5678…" :aria-label="t('Chiave IndexNow')" @input="setA('indexnow_key', $event.target.value.trim())" />
          </div>
          <button type="button" class="cfg-btn cfg-btn-secondary" @click="generaChiave">{{ t('Genera') }}</button>
        </div>
      </div>
      <div v-if="chiaveValida" class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('File della chiave') }}</label>
          <InfoTip :titolo="t('File della chiave')" :testo="t('OLObuild lo pubblica da sé. Dopo aver salvato, aprilo: deve mostrare la chiave.')" />
        </div>
        <div class="control-col">
          <a class="seo-link mono" :href="fileChiave" target="_blank" rel="noopener">{{ fileChiave }}</a>
        </div>
      </div>
    </div>
  </div>

  <!-- ─── 9. Pagina per pagina ─── -->
  <div id="seo-passo-9" class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic seo-num">9</div>
      <div>
        <h3>{{ t('Pagina per pagina') }} <InfoTip :titolo="t('Pagina per pagina')" :testo="t('Ogni pagina del sito col titolo e la descrizione che escono su Google. Apri una riga per scriverli; per l\'analisi del testo apri la pagina nel builder.')" /></h3>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="seo-filtri">
        <div class="cfg-segment">
          <button :class="{ 'is-on': filtro === '' }" @click="cambiaFiltro('')">{{ t('Tutte') }} <small>{{ conteggi.totale }}</small></button>
          <button :class="{ 'is-on': filtro === 'da-sistemare' }" @click="cambiaFiltro('da-sistemare')">{{ t('Da sistemare') }} <small>{{ conteggi.da_sistemare }}</small></button>
          <button :class="{ 'is-on': filtro === 'escluse' }" @click="cambiaFiltro('escluse')">{{ t('Escluse') }} <small>{{ conteggi.escluse }}</small></button>
        </div>
        <CfgSelect size="sm" :model-value="tipoFiltro" :options="opzioniTipoContenuto" @update:model-value="tipoFiltro = $event; caricaPagine(1)" />
        <div class="cfg-input seo-cerca"><input type="search" :value="cerca" :placeholder="t('Cerca una pagina')" :aria-label="t('Cerca una pagina')" @input="onCerca($event.target.value)" /></div>
      </div>
      <p v-if="pagineErrore" class="seo-conta is-att">{{ pagineErrore }}</p>
      <p v-else-if="!righe.length && !pagineCarica" class="seo-vuoto">{{ filtro === 'da-sistemare' ? t('Niente da sistemare: tutte le pagine hanno titolo e descrizione a posto.') : t('Nessuna pagina.') }}</p>
      <ul class="seo-pagine" :class="{ 'is-carica': pagineCarica }">
        <li v-for="r in righe" :key="r.id" :class="['is-' + statoRiga(r).stato, { 'is-aperta': aperta === r.id }]">
          <button type="button" class="seo-pagina-testa" :aria-expanded="aperta === r.id ? 'true' : 'false'" @click="aperta = aperta === r.id ? 0 : r.id">
            <span class="seo-pallino" aria-hidden="true"></span>
            <span class="seo-pagina-nome">
              <b>{{ r.titolo || t('(senza titolo)') }}</b>
              <small>{{ r.home ? t('Home') : r.tipo_nome }} · {{ r.percorso }}</small>
            </span>
            <span class="seo-pagina-problemi">
              <span v-for="pb in statoRiga(r).problemi" :key="pb" class="seo-chip">{{ pb }}</span>
              <span v-if="modifiche[r.id]" class="seo-chip is-mod">{{ t('Da salvare') }}</span>
            </span>
            <svg class="seo-chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
          </button>
          <div v-if="aperta === r.id" class="seo-pagina-corpo">
            <div class="seo-serp">
              <div class="seo-serp-sito"><span class="seo-serp-ico">{{ (titles.kg_name || riepilogo.nome_sito || '?').charAt(0) }}</span><span><b>{{ titles.kg_name || riepilogo.nome_sito }}</b><br>{{ urlRiga(r) }}</span></div>
              <div class="seo-serp-titolo">{{ titoloRiga(r) }}</div>
              <div class="seo-serp-desc" :class="{ 'is-vuota': !descrizioneRiga(r) }">{{ descrizioneRiga(r) || t('Nessuna descrizione: Google ne prende un pezzo a caso dalla pagina.') }}</div>
            </div>
            <div class="seo-campo">
              <span class="seo-campo-testa"><span>{{ t('Titolo su Google') }}</span><span :class="contaClasse(titoloRiga(r).length, 30, 60)">{{ titoloRiga(r).length }} / 60</span></span>
              <span class="cfg-input"><input type="text" :value="valore(r, 'title')" :placeholder="titoloAutomatico(r)" :aria-label="t('Titolo su Google')" @input="modifica(r, 'title', $event.target.value)" /></span>
            </div>
            <div v-if="r.slug" class="seo-campo">
              <span class="seo-campo-testa"><span>{{ t('Indirizzo') }}</span><span v-if="valore(r, 'slug') !== r.slug" class="is-att">{{ t('Il vecchio porterà al nuovo') }}</span></span>
              <span class="cfg-input with-prefix seo-indirizzo">
                <span class="prefix">{{ baseBreve(r) }}</span>
                <input type="text" :value="valore(r, 'slug')" :aria-label="t('Indirizzo')" spellcheck="false" @input="modifica(r, 'slug', pulisciSlug($event.target.value))" />
              </span>
            </div>
            <div class="seo-campo">
              <span class="seo-campo-testa"><span>{{ t('Descrizione') }}</span><span :class="contaClasse(valore(r, 'description').length, 70, 160)">{{ valore(r, 'description').length }} / 160</span></span>
              <span class="cfg-textarea"><textarea rows="3" :value="valore(r, 'description')" :placeholder="r.descrizione_auto || descrizioneSito" :aria-label="t('Descrizione')" @input="modifica(r, 'description', $event.target.value)"></textarea></span>
              <span v-if="!valore(r, 'description') && r.descrizione_auto" class="seo-conta">{{ t('Ora esce quella presa dal riassunto o dal testo, in grigio qui sopra. Scrivila tu per sceglierla.') }}</span>
              <span v-else-if="usaQuellaDelSito(r) && !r.home" class="seo-conta is-att">{{ t('Ora esce la descrizione del sito, uguale per tutte le pagine così. Scrivine una per questa pagina.') }}</span>
            </div>
            <div class="seo-pagina-piede">
              <span class="seo-riga">
                <button class="cfg-switch" :class="{ 'is-on': valore(r, 'noindex') }" role="switch" :aria-checked="valore(r, 'noindex') ? 'true' : 'false'" :aria-label="t('Escludi dai motori')" :disabled="r.noindex_tipo" @click="modifica(r, 'noindex', !valore(r, 'noindex'))"></button>
                <span>{{ r.noindex_tipo ? t('Esclusa con tutto il suo tipo di contenuto') : t('Escludi dai motori') }}</span>
              </span>
              <span class="seo-riga">
                <a class="cfg-btn cfg-btn-ghost" :href="r.url" target="_blank" rel="noopener">{{ t('Vedi la pagina') }}</a>
                <a v-if="r.modifica" class="cfg-btn cfg-btn-secondary" :href="r.modifica">{{ testoModifica(r) }}</a>
              </span>
            </div>
          </div>
        </li>
      </ul>
      <div v-if="trovate > PER" class="seo-pagine-nav">
        <button type="button" class="cfg-btn cfg-btn-ghost" :disabled="pagina <= 1" @click="caricaPagine(pagina - 1)">{{ t('‹ Indietro') }}</button>
        <span>{{ t('Pagina %1 di %2').replace('%1', pagina).replace('%2', Math.ceil(trovate / PER)) }}</span>
        <button type="button" class="cfg-btn cfg-btn-ghost" :disabled="pagina * PER >= trovate" @click="caricaPagine(pagina + 1)">{{ t('Avanti ›') }}</button>
      </div>
    </div>
  </div>

  <!-- ─── 10. Redirect e pagine non trovate ─── -->
  <div id="seo-passo-10" class="cfg-card">
    <div class="cfg-card-head">
      <div class="head-ic seo-num">10</div>
      <div>
        <h3>{{ t('Indirizzi cambiati e pagine non trovate') }} <InfoTip :titolo="t('Indirizzi cambiati e pagine non trovate')" :testo="t('Quando una pagina cambia indirizzo, chi arriva dal vecchio deve trovarla lo stesso: le persone e i motori.')" /></h3>
      </div>
    </div>
    <div class="cfg-card-body tight">
      <div class="cfg-row">
        <div class="label-col">
          <label>{{ t('Redirect automatico') }}</label>
          <InfoTip :titolo="t('Redirect automatico')" :testo="t('Se cambi l\'indirizzo di una pagina pubblicata, il vecchio porta al nuovo da solo.')" />
        </div>
        <div class="control-col">
          <button class="cfg-switch" :class="{ 'is-on': advanced.redirect_auto }" role="switch" :aria-checked="advanced.redirect_auto ? 'true' : 'false'" :aria-label="t('Redirect automatico')" @click="setA('redirect_auto', !advanced.redirect_auto)"></button>
        </div>
      </div>
      <div class="cfg-row no-divider">
        <div class="label-col">
          <label>{{ t('Redirect e pagine non trovate') }}</label>
          <InfoTip :titolo="t('Redirect e pagine non trovate')" :testo="t('I redirect scritti a mano e gli indirizzi che qualcuno ha cercato senza trovare niente.')" />
        </div>
        <div class="control-col seo-riga">
          <span class="seo-numeri"><b>{{ riepilogo.redirect.attivi }}</b> {{ t('redirect') }}</span>
          <span class="seo-numeri" :class="{ 'is-att': riepilogo.redirect.n404 > 0 }"><b>{{ riepilogo.redirect.n404 }}</b> {{ t('non trovate') }}</span>
          <button type="button" class="cfg-btn cfg-btn-secondary" @click="apriScheda('redirects')">{{ t('Apri Redirect & 404') }}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, inject, watch, onMounted, onBeforeUnmount } from 'vue';
import { t } from '@/i18n';
import InfoTip from '@/components/Builder/InfoTip.vue';
import CfgSelect from './controls/CfgSelect.vue';
import { okOrThrow, cfgJob, assertLoaded, reloadJob, conIniziali } from './cfgSave';
import { useCfgLettura } from './composables/useCfgLettura';

const TAB_ID = 'seo';
const showToast = inject('showToast', () => {});
const shellDirty = inject('setDirty', () => {});
const apriScheda = inject('apriScheda', () => {});
const setDirty = (v) => shellDirty(v, TAB_ID);
const loaded = ref(false);
// Prima lettura: finché non riesce la shell mostra scheletro o errore al posto della scheda.
const lettura = useCfgLettura(TAB_ID, loaded, () => loadSettings());

const SEPARATORI = ['—', '–', '|', '·', '-'];
const FASCE = ['', '€', '€€', '€€€', '€€€€'];
const GIORNI = [
  { k: 'monday', nome: t('Lunedì') }, { k: 'tuesday', nome: t('Martedì') }, { k: 'wednesday', nome: t('Mercoledì') },
  { k: 'thursday', nome: t('Giovedì') }, { k: 'friday', nome: t('Venerdì') }, { k: 'saturday', nome: t('Sabato') }, { k: 'sunday', nome: t('Domenica') },
];
const GIORNI_BREVI = [t('lun'), t('mar'), t('mer'), t('gio'), t('ven'), t('sab'), t('dom')];
// Le chiavi che Olobuild_Seo_Head::profili_social legge (Olobuild_Seo_Settings::PROFILI)
const PROFILI = [
  { k: 'facebook_url', nome: 'Facebook', es: 'https://facebook.com/…' },
  { k: 'instagram_url', nome: 'Instagram', es: 'https://instagram.com/…' },
  { k: 'youtube_url', nome: 'YouTube', es: 'https://youtube.com/@…' },
  { k: 'linkedin_url', nome: 'LinkedIn', es: 'https://linkedin.com/company/…' },
  { k: 'tiktok_url', nome: 'TikTok', es: 'https://tiktok.com/@…' },
  { k: 'pinterest_url', nome: 'Pinterest', es: 'https://pinterest.com/…' },
  { k: 'threads_url', nome: 'Threads', es: 'https://threads.net/@…' },
];
const OG_STILI = [
  { value: 'foto',   label: t('Foto con titolo') },
  { value: 'meta',   label: t('Metà e metà') },
  { value: 'colore', label: t('Solo colore') },
];

// Valori di partenza: una sezione mai salvata torna [] dal server, e «Annulla modifiche» deve riportare a QUESTI.
// Sono gli stessi che il server usa quando la chiave manca (Olobuild_Seo_Head): una scelta mai salvata qui mostra
// quello che il sito fa davvero. «Visibile ai motori» arriva SEMPRE dal server (è blog_public).
const INIZIALE_TITLES = { pattern: '{page} {sep} {site}', separator: '—', description: '', robots: 'index', kg_type: 'Organization', kg_name: '', kg_logo: '' };
const INIZIALE_SOCIAL = {
  twitter_user: '', twitter_card_type: 'summary_large_image', og_default_image: '', og_disegnata: false, og_stile: 'foto',
  facebook_url: '', instagram_url: '', youtube_url: '', linkedin_url: '', tiktok_url: '', pinterest_url: '', threads_url: '', altri_profili: '',
};
const INIZIALE_LOCALE = () => ({ address: { street: '', city: '', state: '', zip: '', country: 'IT' }, phone: '', email: '', price_range: '', geo_lat: '', geo_lng: '', hours: {} });
const INIZIALE_ADVANCED = () => ({
  noindex_categories: false, noindex_tags: false, noindex_author: false, noindex_date: true, noindex_pt: {},
  robots_txt: '', ia_bot: 'tutti', llms: false, redirect_auto: true, indexnow_key: '',
});
const INIZIALE_SITEMAP = { enabled: true };
const INIZIALE_WEBMASTER = { google: '', bing: '' };
const RIEPILOGO_VUOTO = {
  nome_sito: '', motto: '', host: '', tipi_chi: [], tipi_contenuto: [], tassonomie: {}, sottocartella: false,
  url: { sitemap: '', robots: '', llms: '' }, bot: { addestramento: [], risposte: [] },
  pagine: { totale: 0, da_sistemare: 0, senza_descrizione: 0, escluse: 0 }, redirect: { attivi: 0, n404: 0 }, home: null, altro_plugin: '', logo_tema: '',
};

const titles    = ref({ ...INIZIALE_TITLES });
const social    = ref({ ...INIZIALE_SOCIAL });
const locale    = ref(INIZIALE_LOCALE());
const advanced  = ref(INIZIALE_ADVANCED());
const sitemap   = ref({ ...INIZIALE_SITEMAP });
const webmaster = ref({ ...INIZIALE_WEBMASTER });
const riepilogo = ref({ ...RIEPILOGO_VUOTO });
// Quello che il server ha davvero: «è nella testata» e «llms.txt pubblicato» solo per ciò che è salvato.
const webmasterSalvato = ref({ ...INIZIALE_WEBMASTER });
const llmsSalvato = ref(false);

function setT(k, v)  { titles.value[k] = v; setDirty(true); }
function setS(k, v)  { social.value[k] = v; setDirty(true); }
function setL(k, v)  { locale.value[k] = v; setDirty(true); }
function setSM(k, v) { sitemap.value[k] = v; setDirty(true); }
function setW(k, v)  { webmaster.value[k] = v; setDirty(true); }
function setA(k, v)  { advanced.value[k] = v; setDirty(true); }
function setInd(k, v) { locale.value.address = { ...locale.value.address, [k]: v }; setDirty(true); }
function setPt(slug, escluso) {
  const pt = { ...advanced.value.noindex_pt };
  if (escluso) pt[slug] = true; else delete pt[slug];
  setA('noindex_pt', pt);
}
function setOra(giorno, quale, v) {
  const h = { ...locale.value.hours, [giorno]: { ...(locale.value.hours[giorno] || {}), [quale]: v } };
  setL('hours', h);
}
function copiaLunedi() {
  const l = locale.value.hours.monday || {};
  if (!l.open || !l.close) { showToast(t('Scrivi prima gli orari del lunedì'), 'error'); return; }
  const h = {};
  GIORNI.forEach((g) => { h[g.k] = { open: l.open, close: l.close }; });
  setL('hours', h);
}
// ─── Indirizzi proposti mentre si scrive (Olobuild_Seo_Centro::rest_indirizzi) ───
const proposte = ref([]);
const proposteAperte = ref(false);
const evidenziata = ref(-1);
const cercaInd = ref(false);
const erroreInd = ref('');
let attesaInd = 0;
let richiestaInd = 0;
async function cercaIndirizzi(q) {
  const mia = ++richiestaInd;
  cercaInd.value = true;
  try {
    const r = await fetch(`${window.oloData.restUrl}seo/indirizzi?q=${encodeURIComponent(q)}`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    const j = await r.json().catch(() => ({}));
    if (mia !== richiestaInd) return null;
    if (!r.ok) { erroreInd.value = j.message || t('La ricerca degli indirizzi non risponde.'); return []; }
    erroreInd.value = '';
    return Array.isArray(j.indirizzi) ? j.indirizzi : [];
  } catch (e) {
    if (mia === richiestaInd) erroreInd.value = t('La ricerca degli indirizzi non risponde.');
    return [];
  } finally {
    if (mia === richiestaInd) cercaInd.value = false;
  }
}
function onVia(v) {
  setInd('street', v);
  clearTimeout(attesaInd);
  const q = v.trim();
  if (q.length < 3) { proposte.value = []; proposteAperte.value = false; return; }
  // La città già scritta aiuta a trovare la via giusta
  const conCitta = locale.value.address.city && !q.toLowerCase().includes(locale.value.address.city.toLowerCase()) ? q + ' ' + locale.value.address.city : q;
  attesaInd = setTimeout(async () => {
    const l = await cercaIndirizzi(conCitta);
    if (l === null) return;
    proposte.value = l;
    evidenziata.value = l.length ? 0 : -1;
    proposteAperte.value = true;
  }, 350);
}
function tastiVia(e) {
  if (!proposteAperte.value || !proposte.value.length) return;
  if (e.key === 'ArrowDown') { e.preventDefault(); evidenziata.value = (evidenziata.value + 1) % proposte.value.length; }
  else if (e.key === 'ArrowUp') { e.preventDefault(); evidenziata.value = (evidenziata.value - 1 + proposte.value.length) % proposte.value.length; }
  else if (e.key === 'Enter' && evidenziata.value >= 0) { e.preventDefault(); scegliIndirizzo(proposte.value[evidenziata.value]); }
  else if (e.key === 'Escape') { proposteAperte.value = false; }
}
function chiudiProposte() { setTimeout(() => { proposteAperte.value = false; }, 150); }
// Il numero civico scritto: un numero del testo che non fa parte del nome della via trovata («Via 4 Novembre») e che non
// è un CAP (cinque cifre). Scritto prima o dopo la città, conta lo stesso.
function civicoScritto(scritto, viaTrovata) {
  const nums = [...String(scritto).matchAll(/(?:^|[\s,])(\d{1,4}[a-zA-Z]?(?:\/\d{1,4}[a-zA-Z]?)?)(?=[\s,]|$)/g)].map((m) => m[1]);
  const nome = ' ' + String(viaTrovata || '').toLowerCase() + ' ';
  const fuori = nums.filter((n) => !nome.includes(' ' + n.toLowerCase() + ' '));
  return fuori.length ? fuori[fuori.length - 1] : '';
}
// Scelto un indirizzo: si compilano i campi; col civico, una seconda ricerca dà il punto del portone invece della via
async function scegliIndirizzo(pr) {
  const scritto = String(locale.value.address.street || '');
  let via = pr.via || pr.nome || '';
  const civico = /\d/.test(via) ? '' : civicoScritto(scritto, via);
  if (civico && via) via += ' ' + civico;
  locale.value.address = {
    ...locale.value.address,
    street: via,
    zip: pr.cap || locale.value.address.zip,
    city: pr.citta || locale.value.address.city,
    state: pr.provincia || locale.value.address.state,
    country: pr.paese || locale.value.address.country,
  };
  if (pr.lat && pr.lng) { locale.value.geo_lat = String(pr.lat); locale.value.geo_lng = String(pr.lng); }
  proposte.value = [];
  proposteAperte.value = false;
  setDirty(true);
  if (civico) {
    const l = await cercaIndirizzi(via + ', ' + [pr.cap, pr.citta].filter(Boolean).join(' '));
    const esatto = (l || []).find((x) => String(x.via).toLowerCase() === via.toLowerCase() && (!pr.citta || x.citta === pr.citta));
    if (esatto && locale.value.address.street === via) {
      locale.value.geo_lat = String(esatto.lat);
      locale.value.geo_lng = String(esatto.lng);
      if (esatto.cap) locale.value.address = { ...locale.value.address, zip: esatto.cap };
    }
  }
}
// «Trova dall'indirizzo»: le coordinate dell'indirizzo scritto a mano
async function trovaCoordinate() {
  const a = locale.value.address || {};
  const l = await cercaIndirizzi([a.street, a.zip, a.city, a.state].filter(Boolean).join(' '));
  if (!l) return;
  if (!l.length) { showToast(erroreInd.value || t('Questo indirizzo non c\'è sulla mappa: incolla le coordinate da Google Maps.'), 'error', 6000); return; }
  locale.value.geo_lat = String(l[0].lat);
  locale.value.geo_lng = String(l[0].lng);
  setDirty(true);
}
const haCoordinate = computed(() => /^-?\d+(\.\d+)?$/.test(String(locale.value.geo_lat || '').trim()) && /^-?\d+(\.\d+)?$/.test(String(locale.value.geo_lng || '').trim()));
// La mappa di OpenStreetMap col segnaposto, larga qualche isolato
const urlMappa = computed(() => {
  const la = parseFloat(locale.value.geo_lat), lo = parseFloat(locale.value.geo_lng);
  const bb = [lo - 0.004, la - 0.0022, lo + 0.004, la + 0.0022].map((n) => n.toFixed(5)).join(',');
  return 'https://www.openstreetmap.org/export/embed.html?bbox=' + bb + '&layer=mapnik&marker=' + la + ',' + lo;
});

// «44.4056, 8.9463» incollato nella latitudine riempie anche la longitudine (come lo copia Google Maps)
function incollaCoordinate(ev) {
  const s = (ev.clipboardData || window.clipboardData)?.getData('text') || '';
  const m = s.match(/(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)/);
  if (!m) return;
  ev.preventDefault();
  locale.value.geo_lat = m[1];
  locale.value.geo_lng = m[2];
  setDirty(true);
}

// ─── Chi pubblica il sito ───
const opzioniTipo = computed(() => riepilogo.value.tipi_chi.map((x) => ({ value: x.value, label: x.label })));
const tipoScelto = computed(() => riepilogo.value.tipi_chi.find((x) => x.value === titles.value.kg_type) || { value: titles.value.kg_type, label: titles.value.kg_type, sede: false, gruppo: '' });
// Il logo che esce davvero: quello scelto qui, o quello del tema (Olobuild_Seo_Head::schema_knowledge_graph)
const logo = computed(() => titles.value.kg_logo || riepilogo.value.logo_tema || '');
const infoTipo = computed(() => tipoScelto.value.sede
  ? t('Un\'attività con una sede: compaiono anche orari, posizione sulla mappa e fascia di prezzo.')
  : t('Scegli quello che ti somiglia di più. Per un\'attività con una sede compaiono anche orari e mappa.'));
const indirizzoTesto = computed(() => {
  const a = locale.value.address || {};
  return [a.street, [a.zip, a.city].filter(Boolean).join(' '), a.state].filter(Boolean).join(', ');
});
// Gli orari raggruppati: «mar–dom 10:00–19:00»
const orariTesto = computed(() => {
  const h = locale.value.hours || {};
  const out = [];
  let i = 0;
  while (i < 7) {
    const o = h[GIORNI[i].k];
    if (!o || !o.open || !o.close) { i++; continue; }
    let j = i;
    while (j + 1 < 7) {
      const n = h[GIORNI[j + 1].k];
      if (n && n.open === o.open && n.close === o.close) j++; else break;
    }
    out.push((j > i ? GIORNI_BREVI[i] + '–' + GIORNI_BREVI[j] : GIORNI_BREVI[i]) + ' ' + o.open + '–' + o.close);
    i = j + 1;
  }
  return out;
});
const numeroProfili = computed(() => {
  const n = PROFILI.filter((p) => social.value[p.k]).length + (social.value.twitter_user ? 1 : 0)
    + String(social.value.altri_profili || '').split(/\n+/).filter((r) => r.trim()).length;
  return n ? (n === 1 ? t('1 profilo collegato') : t('%d profili collegati').replace('%d', n)) : '';
});

// ─── Come appare su Google ───
const sep = computed(() => titles.value.separator || '—');
const homeTitolo = computed(() => {
  const nome = riepilogo.value.nome_sito;
  if (riepilogo.value.home?.seo_title) return riepilogo.value.home.seo_title;
  return riepilogo.value.motto ? nome + ' ' + sep.value + ' ' + riepilogo.value.motto : nome;
});
// Come Olobuild_Seo_Head::get_seo_description per la home: la sua, il riassunto, quella del sito, il motto
const homeDescrizione = computed(() => {
  const h = riepilogo.value.home || {};
  return h.descrizione || h.descrizione_auto || titles.value.description || riepilogo.value.motto || '';
});
function contaClasse(n, min, max) {
  if (!n) return '';
  return n > max ? 'is-att' : (n < min ? 'is-poco' : 'is-ok');
}

// ─── Immagini ───
const ogAnteprima = ref({ url: '', titolo: '', errore: '' });
let ogRichiesta = 0;
async function aggiornaOgAnteprima() {
  if (!social.value.og_disegnata) return;
  const mia = ++ogRichiesta;
  ogAnteprima.value = { url: '', titolo: '', errore: '' };
  try {
    const r = await fetch(`${window.oloData.restUrl}seo/og-anteprima?stile=${encodeURIComponent(social.value.og_stile || 'foto')}`,
      { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    const j = await r.json().catch(() => ({}));
    if (mia !== ogRichiesta) return;
    ogAnteprima.value = r.ok ? { url: j.url || '', titolo: j.titolo || '', errore: '' } : { url: '', titolo: '', errore: j.message || t('Anteprima non disponibile') };
  } catch (e) {
    if (mia === ogRichiesta) ogAnteprima.value = { url: '', titolo: '', errore: t('Anteprima non disponibile') };
  }
}
watch(() => [social.value.og_disegnata, social.value.og_stile], aggiornaOgAnteprima);
const ogPreviewStyle = computed(() => ({
  width: '160px', height: '84px', borderRadius: '8px', display: 'grid', placeItems: 'center',
  color: 'var(--c-text-faint)', fontSize: '12px',
  background: social.value.og_default_image ? `center / cover no-repeat url(${social.value.og_default_image})` : 'var(--c-bg)',
}));
function scegliImmagine(fatto, titolo) {
  if (!window.wp || !window.wp.media) { showToast(t('Libreria media non disponibile'), 'error'); return; }
  const frame = window.wp.media({ title: titolo, button: { text: t('Usa questa immagine') }, multiple: false, library: { type: 'image' } });
  frame.on('select', () => fatto(frame.state().get('selection').first().toJSON().url));
  frame.open();
}

// ─── Cosa leggono i motori ───
const archivi = computed(() => {
  const out = [];
  if (riepilogo.value.tassonomie?.category) out.push({ k: 'noindex_categories', nome: t('Categorie'), consiglio: '' });
  if (riepilogo.value.tassonomie?.post_tag) out.push({ k: 'noindex_tags', nome: t('Tag'), consiglio: '' });
  out.push({ k: 'noindex_author', nome: t('Autori'), consiglio: t('spegnilo se scrivi da solo') });
  out.push({ k: 'noindex_date', nome: t('Archivi per data'), consiglio: t('meglio spento') });
  return out;
});

// ─── Intelligenze artificiali ───
const botFuori = computed(() => {
  const b = riepilogo.value.bot || {};
  if (advanced.value.ia_bot === 'nessuno') return [...(b.addestramento || []), ...(b.risposte || [])];
  return advanced.value.ia_bot === 'senza-addestramento' ? (b.addestramento || []) : [];
});

// ─── Verifica ───
function statoVerifica(k) {
  const ora = codiceVerifica(webmaster.value[k]);
  const salvato = webmasterSalvato.value[k] || '';
  if (ora === salvato) return ora ? 'ok' : '';
  return ora ? 'metti' : 'togli';
}
const dns = ref({ dominio: '', google: [] });
async function leggiDns() {
  try {
    const r = await fetch(`${window.oloData.restUrl}seo/verifica-dns`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    const j = r.ok ? await r.json() : null;
    dns.value = { dominio: String(j?.dominio || ''), google: Array.isArray(j?.google) ? j.google : [] };
  } catch (e) { /* niente riga */ }
}
const testoDns = computed(() => t('Codice di Google trovato nel DNS di') + ' ' + dns.value.dominio + '.'
  + (codiceVerifica(webmaster.value.google) ? '' : ' ' + t('Questo campo può restare vuoto.')));
function codiceVerifica(v) {
  const s = String(v || '').trim();
  const m = s.match(/content\s*=\s*["']([^"']*)["']/i);
  return (m ? m[1] : s).replace(/[^A-Za-z0-9_\-]/g, '').slice(0, 100);
}
function pulisciW(k, ev) {
  const pulito = codiceVerifica(ev.target.value);
  ev.target.value = pulito;
  setW(k, pulito);
}

// ─── IndexNow ───
const chiaveValida = computed(() => /^[A-Za-z0-9-]{8,128}$/.test(advanced.value.indexnow_key || ''));
const fileChiave = computed(() => (window.oloData?.siteUrl || '/') + advanced.value.indexnow_key + '.txt');
function generaChiave() {
  const b = new Uint8Array(16);
  crypto.getRandomValues(b);
  setA('indexnow_key', Array.from(b, (x) => x.toString(16).padStart(2, '0')).join(''));
}

// ─── Pagina per pagina ───
const PER = 20;
const righe = ref([]);
const trovate = ref(0);
const pagina = ref(1);
const filtro = ref('');
const tipoFiltro = ref('');
const cerca = ref('');
const aperta = ref(0);
const pagineCarica = ref(false);
const pagineErrore = ref('');
const conteggi = computed(() => riepilogo.value.pagine || {});
// Le righe modificate e non ancora salvate: id → { title, description, noindex }. Si salvano con «Salva impostazioni».
const modifiche = ref({});
const opzioniTipoContenuto = computed(() => [{ value: '', label: t('Tutti i tipi') }, ...riepilogo.value.tipi_contenuto.map((x) => ({ value: x.slug, label: x.label }))]);

let richiestaPagine = 0;
async function caricaPagine(n = pagina.value) {
  const mia = ++richiestaPagine;
  pagineCarica.value = true;
  try {
    const q = new URLSearchParams({ pagina: String(n), per: String(PER), filtro: filtro.value, tipo: tipoFiltro.value, cerca: cerca.value });
    const r = await fetch(`${window.oloData.restUrl}seo/pagine?${q}`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
    const j = await r.json();
    if (mia !== richiestaPagine) return;
    if (!r.ok) throw new Error(j?.message || String(r.status));
    righe.value = Array.isArray(j.righe) ? j.righe : [];
    trovate.value = j.trovate || 0;
    pagina.value = j.pagina || n;
    if (j.conteggi) riepilogo.value = { ...riepilogo.value, pagine: j.conteggi };
    pagineErrore.value = '';
  } catch (e) {
    if (mia === richiestaPagine) pagineErrore.value = t('Elenco delle pagine non caricato') + ' (' + ((e && e.message) || '') + ')';
  } finally {
    if (mia === richiestaPagine) pagineCarica.value = false;
  }
}
function cambiaFiltro(f) { filtro.value = f; aperta.value = 0; caricaPagine(1); }
let attesaCerca = 0;
function onCerca(v) { cerca.value = v; clearTimeout(attesaCerca); attesaCerca = setTimeout(() => caricaPagine(1), 300); }

function valore(r, k) {
  const m = modifiche.value[r.id];
  if (m && k in m) return m[k];
  if (k === 'title') return r.seo_title || '';
  if (k === 'description') return r.descrizione || '';
  if (k === 'noindex') return !!r.noindex && !r.noindex_tipo;
  if (k === 'slug') return r.slug || '';
  return '';
}
// La parte dell'indirizzo prima del nome della pagina, senza il dominio: «/eventi/»
function baseBreve(r) { return String(r.base || '/').replace(/^https?:\/\/[^/]+/, '') || '/'; }
function urlRiga(r) {
  const s = valore(r, 'slug');
  return r.slug && s && s !== r.slug ? r.base + s + (String(r.url).endsWith('/') ? '/' : '') : r.url;
}
// Come lo scriverà WordPress (sanitize_title): minuscole, senza accenti, parole unite dal trattino
function pulisciSlug(v) {
  return String(v || '').normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase()
    .replace(/[^a-z0-9\-\s]/g, '').replace(/[\s_]+/g, '-').replace(/-+/g, '-');
}
function modifica(r, k, v) {
  modifiche.value = { ...modifiche.value, [r.id]: { ...(modifiche.value[r.id] || {}), [k]: v } };
  setDirty(true);
}
// Il titolo che esce su Google, con lo schema scritto ora (Olobuild_Seo_Head::titolo_per)
function titoloAutomatico(r) {
  const nome = riepilogo.value.nome_sito;
  if (r.home) return riepilogo.value.motto ? nome + ' ' + sep.value + ' ' + riepilogo.value.motto : nome;
  const p = String(titles.value.pattern || '');
  if (p.includes('{page}')) return p.replace('{page}', r.titolo).replace('{sep}', sep.value).replace('{site}', nome).trim();
  return r.titolo + ' ' + sep.value + ' ' + nome;
}
function titoloRiga(r) { return valore(r, 'title') || titoloAutomatico(r); }
// La descrizione che esce su Google: la sua, il riassunto, se no quella del sito (Olobuild_Seo_Head::get_seo_description)
const descrizioneSito = computed(() => titles.value.description || riepilogo.value.motto || '');
function descrizioneRiga(r) { return valore(r, 'description') || r.descrizione_auto || descrizioneSito.value; }
function usaQuellaDelSito(r) { return !valore(r, 'description') && !r.descrizione_auto && !!descrizioneSito.value; }
// Lo stato della riga con i valori a video: come Olobuild_Seo_Centro::righe()
function statoRiga(r) {
  if (valore(r, 'noindex') || r.noindex_tipo) return { stato: 'escluso', problemi: [r.noindex_tipo ? t('Tipo escluso') : t('Esclusa')] };
  const problemi = [];
  let stato = 'ok';
  const d = descrizioneRiga(r);
  const sua = valore(r, 'description');
  if (!d) { problemi.push(t('Nessuna descrizione')); stato = 'errore'; }
  else if (usaQuellaDelSito(r) && !r.home) { problemi.push(t('Usa la descrizione del sito')); stato = 'attenzione'; }
  else if (sua && sua.length > 160) { problemi.push(t('Descrizione lunga')); stato = 'attenzione'; }
  else if (sua && sua.length < 50) { problemi.push(t('Descrizione corta')); stato = 'attenzione'; }
  if (titoloRiga(r).length > 60) { problemi.push(t('Titolo lungo')); if (stato === 'ok') stato = 'attenzione'; }
  if (r.doppio && !valore(r, 'title')) { problemi.push(t('Titolo uguale a un\'altra pagina')); if (stato === 'ok') stato = 'attenzione'; }
  return { stato, problemi };
}
function testoModifica(r) {
  if (String(r.modifica).includes('olobuilder-templates')) return t('Apri nel builder');
  if (String(r.modifica).includes('olo-redazione')) return t('Apri nella Redazione');
  return t('Modifica');
}

// ─── I passi ───
const passi = computed(() => {
  const ti = titles.value;
  const lo = locale.value;
  const so = social.value;
  const ad = advanced.value;
  const rp = riepilogo.value;
  const p = [];
  const metti = (n, titolo, stato, testo) => p.push({ n, titolo, stato, testo });

  metti(1, t('Il sito si vede sui motori'), ti.robots === 'index' ? 'ok' : 'dafare',
    ti.robots === 'index' ? t('Il sito si può trovare.') : t('Il sito è nascosto: aprilo quando è pronto.'));

  const haLogo = !!logo.value;
  const haContatti = !!(lo.phone || lo.email);
  const haIndirizzo = !!(lo.address?.street && lo.address?.city);
  const sedeOk = !tipoScelto.value.sede || (haIndirizzo && orariTesto.value.length > 0);
  const chiOk = haLogo && haContatti && sedeOk;
  const mancano = [];
  if (!haLogo) mancano.push(t('il logo'));
  if (!haContatti) mancano.push(t('un contatto'));
  if (tipoScelto.value.sede && !haIndirizzo) mancano.push(t('l\'indirizzo'));
  if (tipoScelto.value.sede && !orariTesto.value.length) mancano.push(t('gli orari'));
  metti(2, t('Chi pubblica il sito'), chiOk ? 'ok' : (haLogo || haContatti || haIndirizzo ? 'attenzione' : 'dafare'),
    chiOk ? (ti.kg_name || rp.nome_sito) + ' · ' + tipoScelto.value.label
      : (mancano.length === 1 && mancano[0] !== t('gli orari') ? t('Manca %s.') : t('Mancano %s.')).replace('%s', mancano.length > 1 ? mancano.slice(0, -1).join(', ') + ' ' + t('e') + ' ' + mancano[mancano.length - 1] : mancano[0]));

  const descOk = !!(ti.description || rp.home?.descrizione);
  metti(3, t('Come appare su Google'), descOk ? 'ok' : 'dafare',
    descOk ? t('Titolo e descrizione pronti.') : t('Manca la descrizione del sito.'));

  const condOk = !!(so.og_disegnata || so.og_default_image);
  metti(4, t('Come appare quando lo condividi'), condOk ? 'ok' : (rp.logo_tema ? 'attenzione' : 'dafare'),
    condOk ? (so.og_disegnata ? t('Immagine disegnata per ogni pagina.') : t('Immagine di riserva scelta.'))
      : (rp.logo_tema ? t('Per ora esce il logo: meglio un\'immagine vera.') : t('Nessuna immagine per i link condivisi.')));

  metti(5, t('Cosa leggono i motori'), sitemap.value.enabled ? 'ok' : 'attenzione',
    sitemap.value.enabled ? t('Sitemap attiva.') : t('La sitemap è spenta.'));

  metti(6, t('Le intelligenze artificiali'), ad.llms ? 'ok' : 'dafare',
    ad.llms ? (ad.ia_bot === 'nessuno' ? t('Riassunto pronto, ma le IA sono tenute fuori.') : t('Riassunto pronto per le IA.')) : t('Prepara il riassunto per le IA.'));

  const googleOk = !!(codiceVerifica(webmaster.value.google) || dns.value.google.length);
  metti(7, t('Verifica dei motori'), googleOk ? 'ok' : 'dafare',
    googleOk ? (codiceVerifica(webmaster.value.bing) ? t('Google e Bing verificati.') : t('Google verificato. Manca Bing.')) : t('Il sito non è ancora verificato su Google.'));

  metti(8, t('Avvisa i motori quando cambi qualcosa'), chiaveValida.value ? 'ok' : 'dafare',
    chiaveValida.value ? t('IndexNow attivo.') : t('Genera la chiave IndexNow.'));

  const ds = rp.pagine?.da_sistemare || 0;
  metti(9, t('Pagina per pagina'), ds ? 'attenzione' : 'ok',
    ds ? (ds === 1 ? t('1 pagina da sistemare.') : t('%d pagine da sistemare.').replace('%d', ds)) : t('Tutte le pagine sono a posto.'));

  const n404 = rp.redirect?.n404 || 0;
  metti(10, t('Indirizzi cambiati e pagine non trovate'), !ad.redirect_auto ? 'dafare' : (n404 ? 'attenzione' : 'ok'),
    !ad.redirect_auto ? t('Il redirect automatico è spento.') : (n404 ? t('%d indirizzi cercati senza trovare niente.').replace('%d', n404) : t('Redirect automatico acceso.')));
  return p;
});
const fatti = computed(() => passi.value.filter((p) => p.stato === 'ok').length);
const fattiTesto = computed(() => fatti.value === passi.value.length
  ? t('Tutti i passi sono fatti.')
  : t('%1 passi fatti su %2. Le modifiche valgono dopo «Salva impostazioni».').replace('%1', fatti.value).replace('%2', passi.value.length));

// Scorre il pannello delle Impostazioni fino al riquadro del passo e lo illumina (come «Vai al campo»)
function vai(n) {
  const el = document.getElementById('seo-passo-' + n);
  const host = el && el.closest('.cfg-content');
  if (!el || !host) return;
  const top = el.getBoundingClientRect().top - host.getBoundingClientRect().top + host.scrollTop - 16;
  host.scrollTo({ top: Math.max(0, top), behavior: 'smooth' });
  el.classList.remove('cfg-field-flash');
  void el.offsetWidth;
  el.classList.add('cfg-field-flash');
  setTimeout(() => el.classList.remove('cfg-field-flash'), 2400);
}

const provaGoogle = computed(() => 'https://search.google.com/test/rich-results?url=' + encodeURIComponent(window.oloData?.siteUrl || '/'));

// ─── Lettura e salvataggio ───
const oggetto = (v) => (v && typeof v === 'object' && !Array.isArray(v) ? v : {});

async function leggiRiepilogo() {
  const r = await lettura.fetch(`${window.oloData.restUrl}seo/riepilogo`, { headers: { 'X-WP-Nonce': window.oloData.nonce } });
  if (!r.ok) return null;
  const j = await r.json();
  riepilogo.value = { ...RIEPILOGO_VUOTO, ...j, url: { ...RIEPILOGO_VUOTO.url, ...(j.url || {}) }, redirect: { ...RIEPILOGO_VUOTO.redirect, ...(j.redirect || {}) } };
  return j;
}

async function loadSettings() {
  try {
    const headers = { 'X-WP-Nonce': window.oloData.nonce };
    const base = `${window.oloData.restUrl}seo/`;
    const get = (p) => lettura.fetch(base + p, { headers }).then((r) => (r.ok ? r.json() : null)).catch(() => null);
    const [tRes, sRes, smRes, wRes, lRes, aRes, rRes] = await Promise.all([
      get('titles'), get('social'), get('sitemap'), get('webmaster'), get('local_business'), get('advanced'), leggiRiepilogo().catch(() => null),
    ]);
    if (tRes)  titles.value  = conIniziali(INIZIALE_TITLES, tRes);
    if (sRes)  social.value  = conIniziali(INIZIALE_SOCIAL, sRes);
    if (smRes) sitemap.value = conIniziali(INIZIALE_SITEMAP, smRes);
    if (lRes) {
      const l = conIniziali(INIZIALE_LOCALE(), lRes);
      l.address = { ...INIZIALE_LOCALE().address, ...oggetto(lRes.address) };
      l.hours = oggetto(lRes.hours);
      locale.value = l;
    }
    if (aRes) {
      const a = conIniziali(INIZIALE_ADVANCED(), aRes);
      a.noindex_pt = oggetto(aRes.noindex_pt);
      advanced.value = a;
      llmsSalvato.value = !!a.llms;
    }
    if (wRes) {
      webmaster.value = conIniziali(INIZIALE_WEBMASTER, wRes);
      webmasterSalvato.value = { google: webmaster.value.google || '', bing: webmaster.value.bing || '' };
    }
    modifiche.value = {};
    // Si salva solo se ogni lettura è riuscita: basta una lettura mancata per spedire i default.
    if (tRes && sRes && smRes && wRes && lRes && aRes && rRes) loaded.value = true;
    caricaPagine(1);
  } catch (e) { /* defaults */ }
}

async function saveSettings() {
  assertLoaded(loaded);
  const base = `${window.oloData.restUrl}seo/`;
  const headers = { 'Content-Type': 'application/json', 'X-WP-Nonce': window.oloData.nonce };
  const manda = (p, dati) => okOrThrow(fetch(base + p, { method: 'POST', headers, body: JSON.stringify(dati) }));
  const codici = { google: codiceVerifica(webmaster.value.google), bing: codiceVerifica(webmaster.value.bing) };
  if (advanced.value.indexnow_key && !chiaveValida.value) throw new Error(t('chiave IndexNow non valida'));
  // Uno dopo l'altro: il salvataggio dei titoli tocca anche l'opzione delle avanzate.
  await manda('titles', titles.value);
  await manda('advanced', advanced.value);
  await manda('social', social.value);
  await manda('local_business', locale.value);
  await manda('sitemap', sitemap.value);
  await manda('webmaster', codici);
  // Le pagine modificate nell'elenco, con la rotta della SEO di ogni pagina (la stessa del builder)
  for (const [id, m] of Object.entries(modifiche.value)) {
    const r = await okOrThrow(fetch(`${window.oloData.restUrl}page-seo/${id}`, { method: 'POST', headers, body: JSON.stringify(m) }));
    const j = await r.json().catch(() => ({}));
    if (j && j.errors && Object.keys(j.errors).length) throw new Error(Object.values(j.errors).join(' · '));
  }
  webmasterSalvato.value = codici;
  llmsSalvato.value = !!advanced.value.llms;
  modifiche.value = {};
  // Conteggi e righe come li vede il server dopo il salvataggio
  await leggiRiepilogo().catch(() => null);
  caricaPagine(pagina.value);
}

const onSave = cfgJob(TAB_ID, saveSettings);
const onDiscard = cfgJob(TAB_ID, reloadJob(loaded, loadSettings));

onMounted(() => {
  lettura.leggi();
  leggiDns();
  window.addEventListener('olo-cfg-save', onSave);
  window.addEventListener('olo-cfg-discard', onDiscard);
});
onBeforeUnmount(() => {
  window.removeEventListener('olo-cfg-save', onSave);
  window.removeEventListener('olo-cfg-discard', onDiscard);
});
</script>

<style scoped>
.seo-avviso { margin: 0 0 16px; padding: 12px 16px; border-radius: 10px; background: var(--c-warning-soft); color: var(--c-warning); font-size: 13px; font-weight: 600; }

/* I passi */
.seo-barra { height: 6px; border-radius: 3px; background: var(--c-line-soft); overflow: hidden; margin: 6px 0 10px; }
.seo-barra span { display: block; height: 100%; background: var(--c-success); border-radius: 3px; transition: width .4s ease; }
.seo-elenco { list-style: none; margin: 0; padding: 0; display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0 28px; }
.seo-elenco li { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid var(--c-line-soft); min-width: 0; }
.seo-segno { flex: 0 0 26px; width: 26px; height: 26px; border-radius: 50%; display: grid; place-items: center; font-size: 12px; font-weight: 700;
  background: var(--c-bg); color: var(--c-text-mute); box-shadow: inset 0 0 0 1.5px var(--c-line); }
.seo-segno svg { width: 14px; height: 14px; }
.is-ok .seo-segno { background: var(--c-success); color: #fff; box-shadow: none; }
.is-attenzione .seo-segno { background: var(--c-warning); color: #fff; box-shadow: none; }
.seo-passo-testi { flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 1px; }
.seo-passo-nome { font-size: 13px; font-weight: 600; color: var(--c-text); }
.seo-passo-stato { font-size: 12px; color: var(--c-text-mute); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.is-attenzione .seo-passo-stato { color: var(--c-warning); }
.seo-vai { flex: 0 0 auto; }

/* Numero del passo al posto dell'icona */
.head-ic.seo-num { font-size: 15px; font-weight: 800; color: var(--c-red); }

.seo-riga { display: flex; align-items: center; flex-wrap: wrap; gap: 10px 12px; }
.seo-nota { font-size: 12.5px; font-weight: 600; }
.is-ok { color: var(--c-success); }
.is-att { color: var(--c-warning); }
.is-poco { color: var(--c-text-faint); }
.seo-conta { margin-top: 6px; font-size: 12px; color: var(--c-text-faint); }
.seo-conta.is-att, .seo-conta .is-att { color: var(--c-warning); }
.seo-link { font-size: 12px; color: var(--c-red); text-decoration: none; word-break: break-all; }
.seo-link:hover { text-decoration: underline; }
.seo-link:focus-visible, .verifica-stato a:focus-visible { outline: 2px solid var(--c-red); outline-offset: 2px; border-radius: 2px; }
.mono { font-family: var(--c-mono); }

.seo-griglia { display: grid; gap: 8px; max-width: 580px; }
.seo-griglia.due { grid-template-columns: repeat(2, minmax(0, 1fr)); }
.seo-griglia.indirizzo { grid-template-columns: 96px minmax(0, 1fr) 110px 84px; }
.seo-griglia.indirizzo .via { grid-column: 1 / -1; }
/* più forte di «.cfg-row .control-col .cfg-input { max-width: 440px }» della shell */
.cfg-row .control-col.seo-griglia .cfg-input { max-width: none; width: auto; }
.seo-griglia .with-prefix .prefix { min-width: 74px; }
.seo-separatori button { min-width: 44px; font-size: 16px; }

/* Indirizzi proposti */
.seo-via { position: relative; }
.seo-via .cfg-input { position: relative; }
.seo-giro { width: 14px; height: 14px; border-radius: 50%; border: 2px solid var(--c-line); border-top-color: var(--c-red); animation: seo-giro .7s linear infinite; flex: 0 0 auto; margin-left: 8px; }
@keyframes seo-giro { to { transform: rotate(360deg); } }
@media (prefers-reduced-motion: reduce) { .seo-giro { animation-duration: 2s; } }
.seo-proposte { position: absolute; z-index: 30; left: 0; right: 0; top: calc(100% + 4px); margin: 0; padding: 4px; list-style: none; background: #fff;
  border-radius: 10px; box-shadow: 0 0 0 1px var(--c-line), 0 12px 28px rgba(15, 23, 42, .14); max-height: 320px; overflow: auto; }
.seo-proposte li[role=option] { display: flex; flex-direction: column; gap: 1px; padding: 8px 10px; border-radius: 7px; cursor: pointer; }
.seo-proposte li[role=option].is-on { background: var(--c-red-soft); }
.seo-proposte b { font-size: 13px; font-weight: 600; color: var(--c-text); }
.seo-proposte b span { font-weight: 400; color: var(--c-text-mute); }
.seo-proposte small { font-size: 12px; color: var(--c-text-mute); }
.seo-proposte-errore { padding: 8px 10px; font-size: 12px; color: var(--c-warning); }
.seo-proposte-fonte { padding: 4px 10px 2px; font-size: 10.5px; color: var(--c-text-faint); text-align: right; }
.seo-mappa-riga { grid-column: 1 / -1; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.seo-mappa { grid-column: 1 / -1; width: 100%; height: 220px; border: 0; border-radius: 10px; box-shadow: 0 0 0 1px var(--c-line); background: var(--c-bg); }

.seo-logo { width: 120px; height: 56px; border-radius: 8px; background: #fff; box-shadow: inset 0 0 0 1px var(--c-line); display: grid; place-items: center; overflow: hidden; }
.seo-logo img { max-width: 100%; max-height: 100%; object-fit: contain; padding: 6px; box-sizing: border-box; }
.seo-logo.is-vuoto { font-size: 11px; color: var(--c-text-faint); background: var(--c-bg); }

.seo-orari { display: grid; gap: 6px; }
.seo-giorno { display: grid; grid-template-columns: 96px 120px 14px 120px; align-items: center; gap: 6px; }
.seo-giorno-nome { font-size: 13px; color: var(--c-text); }
.seo-giorno .cfg-input input { font-variant-numeric: tabular-nums; }
.seo-trattino { text-align: center; color: var(--c-text-faint); }
.seo-copia { margin-top: 8px; }

/* La scheda di chi pubblica */
.seo-scheda { max-width: 420px; border-radius: 12px; background: #fff; box-shadow: 0 0 0 1px var(--c-line), 0 6px 18px rgba(15, 23, 42, .06); padding: 16px 18px; }
.seo-scheda-testa { display: flex; align-items: center; gap: 12px; }
.seo-scheda-logo { width: 48px; height: 48px; object-fit: contain; border-radius: 8px; box-shadow: inset 0 0 0 1px var(--c-line-soft); padding: 4px; box-sizing: border-box; }
.seo-scheda-nome { font-size: 17px; font-weight: 700; color: var(--c-text); }
.seo-scheda-tipo { font-size: 12.5px; color: var(--c-text-mute); }
.seo-scheda-dati { display: grid; grid-template-columns: 74px 1fr; gap: 6px 12px; margin: 12px 0 0; font-size: 12.5px; }
.seo-scheda-dati dt { color: var(--c-text-faint); }
.seo-scheda-dati dd { margin: 0; color: var(--c-text); }
.seo-scheda-ora { display: block; }
.seo-scheda-vuota { margin: 10px 0 0; font-size: 12px; color: var(--c-warning); }

/* L'anteprima di Google */
.seo-serp { max-width: 600px; border-radius: 12px; background: #fff; box-shadow: 0 0 0 1px var(--c-line); padding: 14px 16px; font-family: Arial, sans-serif; }
.seo-serp-sito { display: flex; align-items: center; gap: 10px; font-size: 12px; color: #4d5156; line-height: 1.35; }
.seo-serp-sito b { color: #202124; font-weight: 400; font-size: 14px; }
.seo-serp-ico { width: 26px; height: 26px; border-radius: 50%; background: var(--c-bg); box-shadow: inset 0 0 0 1px var(--c-line); display: grid; place-items: center; font-size: 12px; font-weight: 700; color: var(--c-text-mute); flex: 0 0 auto; }
.seo-serp-titolo { margin-top: 6px; font-size: 20px; line-height: 1.3; color: #1a0dab; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.seo-serp-desc { margin-top: 3px; font-size: 14px; line-height: 1.58; color: #4d5156; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.seo-serp-desc.is-vuota { color: var(--c-warning); font-style: italic; }

/* Immagini per i social */
.og-preview-wrap { display: flex; gap: 12px; align-items: flex-start; }
.og-preview { box-shadow: inset 0 0 0 1px rgba(0,0,0,.06); position: relative; overflow: hidden; flex: 0 0 auto; }
.og-actions { display: flex; flex-wrap: wrap; gap: 8px; }
.og-stili { margin-top: 12px; }
.og-disegnata { margin-top: 12px; width: 100%; max-width: 360px; aspect-ratio: 1200 / 630; border-radius: 8px; overflow: hidden;
  background: var(--c-bg); box-shadow: inset 0 0 0 1px rgba(0,0,0,.06); display: grid; place-items: center; }
.og-disegnata img { display: block; width: 100%; height: 100%; object-fit: cover; }
.og-disegnata-vuota { font-size: 12px; color: var(--c-text-faint); padding: 0 12px; text-align: center; }

/* Interruttori in fila */
.seo-scelte { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px 24px; max-width: 560px; }
.seo-scelta { display: flex; align-items: center; gap: 10px; font-size: 13px; color: var(--c-text); }
.seo-scelta small { color: var(--c-text-faint); font-size: 11.5px; margin-left: 4px; }

.cfg-input.is-error { border-color: var(--c-red); }
.verifica-stato { display: flex; flex-wrap: wrap; gap: 4px 12px; }
.verifica-stato a { color: var(--c-red); text-decoration: none; }
.verifica-stato a:hover { text-decoration: underline; }
.verifica-ok { color: var(--c-success); }
.verifica-dns { flex-basis: 100%; }
.verifica-attesa { color: var(--c-warning); }

/* Pagina per pagina */
.seo-filtri { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; padding: 4px 0 12px; }
.seo-filtri small { color: var(--c-text-faint); margin-left: 4px; font-weight: 600; }
.seo-cerca { flex: 1 1 180px; max-width: 260px; }
.seo-vuoto { margin: 6px 0 12px; font-size: 13px; color: var(--c-text-mute); }
.seo-pagine { list-style: none; margin: 0 0 8px; padding: 0; border-top: 1px solid var(--c-line-soft); transition: opacity .15s; }
.seo-pagine.is-carica { opacity: .5; }
.seo-pagine > li { border-bottom: 1px solid var(--c-line-soft); }
.seo-pagina-testa { width: 100%; display: flex; align-items: center; gap: 12px; padding: 11px 4px; background: none; border: 0; cursor: pointer; text-align: left; font: inherit; color: inherit; }
.seo-pagina-testa:hover { background: var(--c-bg); }
.seo-pagina-testa:focus-visible { outline: 2px solid var(--c-red); outline-offset: -2px; border-radius: 6px; }
.seo-pallino { flex: 0 0 10px; width: 10px; height: 10px; border-radius: 50%; background: var(--c-success); }
.is-attenzione .seo-pallino { background: var(--c-warning); }
.is-errore .seo-pallino { background: var(--c-red); }
.is-escluso .seo-pallino { background: var(--c-text-faint); }
.seo-pagina-nome { flex: 1 1 auto; min-width: 0; display: flex; flex-direction: column; }
.seo-pagina-nome b { font-size: 13.5px; font-weight: 600; color: var(--c-text); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.seo-pagina-nome small { font-size: 12px; color: var(--c-text-faint); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.seo-pagina-problemi { display: flex; flex-wrap: wrap; justify-content: flex-end; gap: 4px; flex: 0 1 auto; }
.seo-chip { font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 999px; background: var(--c-bg); color: var(--c-text-mute); white-space: nowrap; }
.is-attenzione .seo-chip { background: var(--c-warning-soft); color: var(--c-warning); }
.is-errore .seo-chip { background: var(--c-red-soft); color: var(--c-red-dark); }
.seo-chip.is-mod { background: var(--c-navy); color: #fff; }
.seo-chev { flex: 0 0 16px; width: 16px; height: 16px; color: var(--c-text-faint); transition: transform .2s; }
.is-aperta .seo-chev { transform: rotate(180deg); }
.seo-pagina-corpo { padding: 4px 4px 18px 26px; display: grid; gap: 12px; }
.seo-campo { display: grid; gap: 6px; max-width: 600px; }
.seo-campo .cfg-input, .seo-campo .cfg-textarea { max-width: none; }
/* Le descrizioni sono testo da leggere, non codice: il carattere del testo (la shell dà alle textarea quello mono) */
.seo-campo .cfg-textarea textarea, .seo-testo textarea { font-family: var(--c-sans); font-size: 13.5px; line-height: 1.5; }
.seo-indirizzo .prefix { font-family: var(--c-mono); font-size: 12.5px; }
.seo-indirizzo input { font-family: var(--c-mono); font-size: 12.5px; }
.seo-campo-testa { display: flex; justify-content: space-between; font-size: 12.5px; font-weight: 600; color: var(--c-text); }
.seo-campo-testa span:last-child { font-weight: 500; font-variant-numeric: tabular-nums; color: var(--c-text-faint); }
.seo-campo-testa span.is-ok { color: var(--c-success); }
.seo-campo-testa span.is-att { color: var(--c-warning); }
.seo-pagina-piede { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 10px; align-items: center; max-width: 600px; font-size: 13px; }
.seo-pagine-nav { display: flex; align-items: center; justify-content: center; gap: 12px; font-size: 12.5px; color: var(--c-text-mute); padding: 4px 0; }

.seo-numeri { font-size: 13px; color: var(--c-text-mute); }
.seo-numeri b { font-size: 16px; color: var(--c-text); margin-right: 2px; }
.seo-numeri.is-att b { color: var(--c-warning); }

@media (max-width: 900px) {
  .seo-elenco { grid-template-columns: 1fr; }
}
@media (max-width: 640px) {
  .seo-griglia.due, .seo-scelte { grid-template-columns: 1fr; }
  .seo-griglia.indirizzo { grid-template-columns: 90px minmax(0, 1fr); }
  .seo-giorno { grid-template-columns: 80px 1fr 12px 1fr; }
  .seo-pagina-problemi { display: none; }
}
</style>
