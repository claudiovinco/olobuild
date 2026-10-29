/**
 * Campi dei tipi di contenuto nella scheda di modifica (Olobuild_Tipi_Contenuto): foto, galleria, allegati scelti dalla
 * libreria media, e le foto per i social inquadrate col ritaglio di WordPress, una proporzione per formato
 * (ajax crop-image: il ritaglio diventa una foto nuova della libreria, l'originale resta intatto).
 */
(function ($) {
    'use strict';
    var T = (window.oloTipiContenuto || {}).testi || {};

    function anteprima(att) {
        var s = att.sizes || {};
        var u = (s.thumbnail || s.medium || s.full || {}).url || att.icon || '';
        return u ? $('<img alt="">').attr('src', u) : $('<span>');
    }

    function voce(att, tipo) {
        var li = $('<li>').attr('data-id', att.id);
        if (tipo === 'allegati') {
            var est = String(att.filename || '').split('.').pop().toUpperCase();
            li.append($('<span class="olo-tc-file">').text(att.title || att.filename).append(' ', $('<em>').text(est + (att.filesizeHumanReadable ? ' · ' + att.filesizeHumanReadable : ''))));
        } else {
            li.append(anteprima(att));
        }
        li.append($('<button type="button" class="olo-tc-su">').attr('aria-label', T.su).text('↑'));
        li.append($('<button type="button" class="olo-tc-togli">').attr('aria-label', T.togli).text('×'));
        return li;
    }

    function scrivi(box) {
        var ids = box.find('.olo-tc-voci > li').map(function () { return $(this).attr('data-id'); }).get();
        box.find('input[type=hidden]').first().val(ids.join(',')).trigger('change');
    }

    // Foto, galleria, allegati
    $(document).on('click', '.olo-tc-media .olo-tc-scegli', function () {
        var box = $(this).closest('.olo-tc-media');
        var tipo = box.attr('data-tipo');
        var multi = tipo !== 'immagine';
        var frame = wp.media({
            title: tipo === 'allegati' ? T.allegati : (multi ? T.fotoMolte : T.foto),
            button: { text: T.usa },
            multiple: multi ? 'add' : false,
            library: tipo === 'allegati' ? {} : { type: 'image' }
        });
        frame.on('select', function () {
            var lista = box.find('.olo-tc-voci');
            if (!multi) {
                lista.empty();
            }
            frame.state().get('selection').each(function (m) {
                var att = m.toJSON();
                if (!lista.find('li[data-id="' + att.id + '"]').length) {
                    lista.append(voce(att, tipo));
                }
            });
            scrivi(box);
        });
        frame.open();
    });
    $(document).on('click', '.olo-tc-media .olo-tc-togli', function () {
        var box = $(this).closest('.olo-tc-media');
        $(this).closest('li').remove();
        scrivi(box);
    });
    $(document).on('click', '.olo-tc-media .olo-tc-su', function () {
        var li = $(this).closest('li');
        var box = li.closest('.olo-tc-media');
        if (li.prev().length) {
            li.insertBefore(li.prev());
            scrivi(box);
        }
    });

    // Video: un file della libreria (il suo ID nel campo nascosto) o un link scritto a mano
    function video(box, valore, nomeFile) {
        box.find('input[type=hidden]').first().val(valore).trigger('change');
        box.find('.olo-tc-video-file').text(nomeFile || '').prop('hidden', !nomeFile);
        box.find('.olo-tc-video-link').prop('hidden', !!nomeFile);
        box.find('.olo-tc-video-togli').prop('hidden', !valore);
    }
    $(document).on('input', '.olo-tc-video .olo-tc-video-link', function () {
        video($(this).closest('.olo-tc-video'), $.trim($(this).val()), '');
    });
    $(document).on('click', '.olo-tc-video .olo-tc-video-scegli', function () {
        var box = $(this).closest('.olo-tc-video');
        var frame = wp.media({ title: T.video, button: { text: T.usa }, multiple: false, library: { type: 'video' } });
        frame.on('select', function () {
            var att = frame.state().get('selection').first().toJSON();
            box.find('.olo-tc-video-link').val('');
            video(box, String(att.id), att.title || att.filename);
        });
        frame.open();
    });
    $(document).on('click', '.olo-tc-video .olo-tc-video-togli', function () {
        var box = $(this).closest('.olo-tc-video');
        box.find('.olo-tc-video-link').val('');
        video(box, '', '');
    });

    // Foto per i social
    function leggi(box) {
        try {
            return JSON.parse(box.find('input[type=hidden]').first().val() || '{}') || {};
        } catch (e) {
            return {};
        }
    }
    function salva(box, dati) {
        box.find('input[type=hidden]').first().val(JSON.stringify(dati)).trigger('change');
    }

    $(document).on('click', '.olo-tc-social .olo-tc-originale', function () {
        var box = $(this).closest('.olo-tc-social');
        var frame = wp.media({ title: T.foto, button: { text: T.usa }, multiple: false, library: { type: 'image' } });
        frame.on('select', function () {
            var att = frame.state().get('selection').first().toJSON();
            // Una foto nuova: i ritagli della vecchia non valgono più.
            salva(box, { sorgente: att.id });
            box.find('.olo-tc-sorgente').children('img,.olo-tc-vuoto').replaceWith(anteprima(att));
            var media = (att.sizes && (att.sizes.medium || att.sizes.full)) || {};
            box.find('.olo-tc-formati > li').each(function () {
                $(this).find('.olo-tc-anteprima').empty().append($('<img alt="">').attr('src', media.url || ''));
                $(this).find('.olo-tc-stato').text(T.centro);
                $(this).find('.olo-tc-inquadra').prop('disabled', false);
            });
        });
        frame.open();
    });

    // Il ritaglio: lo stato «cropper» di WordPress con la proporzione fissa del formato, il centro della foto come
    // proposta e la misura del formato come uscita (mai più grande della parte scelta).
    var Ritaglio = wp.media.controller.Cropper.extend({
        doCrop: function (attachment) {
            var d = _.extend({}, attachment.get('cropDetails'));
            var f = this.get('formato');
            var scala = Math.min(1, f.w / Math.max(1, d.width));
            d.dst_width = Math.round(d.width * scala);
            d.dst_height = Math.round(d.height * scala);
            return wp.ajax.post('crop-image', {
                nonce: attachment.get('nonces').edit,
                id: attachment.get('id'),
                context: 'olo-foto-social',
                cropDetails: d
            });
        }
    });

    $(document).on('click', '.olo-tc-social .olo-tc-inquadra', function () {
        var li = $(this).closest('li');
        var box = li.closest('.olo-tc-social');
        var dati = leggi(box);
        if (!dati.sorgente) {
            return;
        }
        var f = { w: parseInt(li.attr('data-w'), 10), h: parseInt(li.attr('data-h'), 10) };
        var frame = wp.media({
            button: { text: T.inquadra, close: false },
            states: [
                new wp.media.controller.Library({
                    title: li.find('.olo-tc-formato').text(),
                    library: wp.media.query({ type: 'image' }),
                    multiple: false,
                    date: false,
                    priority: 20,
                    suggestedWidth: f.w,
                    suggestedHeight: f.h
                }),
                new Ritaglio({
                    formato: f,
                    canSkipCrop: false,
                    imgSelectOptions: function (att) {
                        var w = att.get('width');
                        var h = att.get('height');
                        var sw = w;
                        var sh = Math.round(w * f.h / f.w);
                        if (sh > h) {
                            sh = h;
                            sw = Math.round(h * f.w / f.h);
                        }
                        var x1 = Math.round((w - sw) / 2);
                        var y1 = Math.round((h - sh) / 2);
                        return {
                            handles: true, keys: true, instance: true, persistent: true,
                            imageWidth: w, imageHeight: h, aspectRatio: f.w + ':' + f.h,
                            x1: x1, y1: y1, x2: x1 + sw, y2: y1 + sh
                        };
                    }
                })
            ]
        });
        frame.on('open', function () {
            var sel = frame.state('library').get('selection');
            var att = wp.media.attachment(dati.sorgente);
            att.fetch();
            sel.reset([att]);
        });
        frame.on('select', function () {
            frame.setState('cropper');
        });
        frame.on('cropped', function (img) {
            var d = leggi(box);
            d[li.attr('data-formato')] = img.id;
            salva(box, d);
            li.find('.olo-tc-anteprima').empty().append($('<img alt="">').attr('src', img.url));
            li.find('.olo-tc-stato').text(T.inquadrata);
        });
        frame.on('content:error:crop', function () {
            window.alert(T.errore);
        });
        frame.open();
    });
})(jQuery);
