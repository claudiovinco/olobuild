# -*- coding: utf-8 -*-
"""
Fill olo-vtour .po translation files.

Reads each .po template in D:/TECNICA/olo-vtour/languages/, looks up
each msgid in the `translations` dict below, and writes the matching
msgstr inline. Also updates the header (Last-Translator, PO-Revision-Date).

Run:
    python D:/TECNICA/olobuild/scripts/fill-po-vtour.py
"""

import os
import re
import sys

LANG_DIR = r"D:\TECNICA\olo-vtour\languages"

LANGS = [
    "cs_CZ", "de_DE", "en_US", "es_ES", "fr_FR",
    "hu_HU", "ja", "nl_NL", "pl_PL", "pt_BR", "ru_RU",
]

# All 56 strings to translate. Keys are the original Italian msgids.
# Each entry is a dict mapping lang_code -> translated string.
# Brand/product names kept untranslated: OLOtour, Olo Vtour, Olobuild,
# Polyhaven, Google Street View, HDRI, Pannellum, Photo Sphere Viewer,
# WordPress.
translations = {
    "Poly Haven API error": {
        "cs_CZ": "Chyba Poly Haven API",
        "de_DE": "Poly-Haven-API-Fehler",
        "en_US": "Poly Haven API error",
        "es_ES": "Error de la API de Poly Haven",
        "fr_FR": "Erreur de l'API Poly Haven",
        "hu_HU": "Poly Haven API hiba",
        "ja": "Poly Haven API エラー",
        "nl_NL": "Poly Haven API-fout",
        "pl_PL": "Błąd API Poly Haven",
        "pt_BR": "Erro da API do Poly Haven",
        "ru_RU": "Ошибка API Poly Haven",
    },
    "Risposta non valida": {
        "cs_CZ": "Neplatná odpověď",
        "de_DE": "Ungültige Antwort",
        "en_US": "Invalid response",
        "es_ES": "Respuesta no válida",
        "fr_FR": "Réponse non valide",
        "hu_HU": "Érvénytelen válasz",
        "ja": "無効な応答",
        "nl_NL": "Ongeldig antwoord",
        "pl_PL": "Nieprawidłowa odpowiedź",
        "pt_BR": "Resposta inválida",
        "ru_RU": "Недопустимый ответ",
    },
    "Slug panorama mancante": {
        "cs_CZ": "Chybí slug panoramatu",
        "de_DE": "Panorama-Slug fehlt",
        "en_US": "Missing panorama slug",
        "es_ES": "Falta el slug del panorama",
        "fr_FR": "Slug du panorama manquant",
        "hu_HU": "Hiányzó panoráma slug",
        "ja": "パノラマのスラッグがありません",
        "nl_NL": "Panorama-slug ontbreekt",
        "pl_PL": "Brak slugu panoramy",
        "pt_BR": "Slug do panorama ausente",
        "ru_RU": "Отсутствует slug панорамы",
    },
    "Errore recupero file": {
        "cs_CZ": "Chyba při načítání souboru",
        "de_DE": "Fehler beim Abrufen der Datei",
        "en_US": "Error fetching file",
        "es_ES": "Error al recuperar el archivo",
        "fr_FR": "Erreur de récupération du fichier",
        "hu_HU": "Hiba a fájl lekérésekor",
        "ja": "ファイル取得エラー",
        "nl_NL": "Fout bij ophalen van bestand",
        "pl_PL": "Błąd pobierania pliku",
        "pt_BR": "Erro ao recuperar o arquivo",
        "ru_RU": "Ошибка получения файла",
    },
    "Risposta file non valida": {
        "cs_CZ": "Neplatná odpověď souboru",
        "de_DE": "Ungültige Dateiantwort",
        "en_US": "Invalid file response",
        "es_ES": "Respuesta de archivo no válida",
        "fr_FR": "Réponse de fichier non valide",
        "hu_HU": "Érvénytelen fájlválasz",
        "ja": "無効なファイル応答",
        "nl_NL": "Ongeldig bestandsantwoord",
        "pl_PL": "Nieprawidłowa odpowiedź pliku",
        "pt_BR": "Resposta de arquivo inválida",
        "ru_RU": "Недопустимый ответ файла",
    },
    "Nessun file trovato per %s": {
        "cs_CZ": "Nebyl nalezen žádný soubor pro %s",
        "de_DE": "Keine Datei für %s gefunden",
        "en_US": "No file found for %s",
        "es_ES": "No se encontró ningún archivo para %s",
        "fr_FR": "Aucun fichier trouvé pour %s",
        "hu_HU": "Nem található fájl ehhez: %s",
        "ja": "%s のファイルが見つかりません",
        "nl_NL": "Geen bestand gevonden voor %s",
        "pl_PL": "Nie znaleziono pliku dla %s",
        "pt_BR": "Nenhum arquivo encontrado para %s",
        "ru_RU": "Файл для %s не найден",
    },
    "HDRI by %s — Poly Haven (CC0)": {
        "cs_CZ": "HDRI od %s — Poly Haven (CC0)",
        "de_DE": "HDRI von %s — Poly Haven (CC0)",
        "en_US": "HDRI by %s — Poly Haven (CC0)",
        "es_ES": "HDRI de %s — Poly Haven (CC0)",
        "fr_FR": "HDRI par %s — Poly Haven (CC0)",
        "hu_HU": "HDRI: %s — Poly Haven (CC0)",
        "ja": "HDRI by %s — Poly Haven (CC0)",
        "nl_NL": "HDRI door %s — Poly Haven (CC0)",
        "pl_PL": "HDRI autorstwa %s — Poly Haven (CC0)",
        "pt_BR": "HDRI por %s — Poly Haven (CC0)",
        "ru_RU": "HDRI от %s — Poly Haven (CC0)",
    },
    "Poly Haven (CC0)": {
        "cs_CZ": "Poly Haven (CC0)",
        "de_DE": "Poly Haven (CC0)",
        "en_US": "Poly Haven (CC0)",
        "es_ES": "Poly Haven (CC0)",
        "fr_FR": "Poly Haven (CC0)",
        "hu_HU": "Poly Haven (CC0)",
        "ja": "Poly Haven (CC0)",
        "nl_NL": "Poly Haven (CC0)",
        "pl_PL": "Poly Haven (CC0)",
        "pt_BR": "Poly Haven (CC0)",
        "ru_RU": "Poly Haven (CC0)",
    },
    "Tour non trovato": {
        "cs_CZ": "Prohlídka nenalezena",
        "de_DE": "Tour nicht gefunden",
        "en_US": "Tour not found",
        "es_ES": "Tour no encontrado",
        "fr_FR": "Visite non trouvée",
        "hu_HU": "A túra nem található",
        "ja": "ツアーが見つかりません",
        "nl_NL": "Tour niet gevonden",
        "pl_PL": "Nie znaleziono wycieczki",
        "pt_BR": "Tour não encontrado",
        "ru_RU": "Тур не найден",
    },
    "Impossibile creare il tour": {
        "cs_CZ": "Prohlídku nelze vytvořit",
        "de_DE": "Tour kann nicht erstellt werden",
        "en_US": "Unable to create the tour",
        "es_ES": "No se puede crear el tour",
        "fr_FR": "Impossible de créer la visite",
        "hu_HU": "A túra nem hozható létre",
        "ja": "ツアーを作成できません",
        "nl_NL": "Kan de tour niet maken",
        "pl_PL": "Nie można utworzyć wycieczki",
        "pt_BR": "Não foi possível criar o tour",
        "ru_RU": "Не удалось создать тур",
    },
    "Non hai i permessi per modificare questo tour": {
        "cs_CZ": "Nemáte oprávnění k úpravě této prohlídky",
        "de_DE": "Sie haben keine Berechtigung, diese Tour zu bearbeiten",
        "en_US": "You don't have permission to edit this tour",
        "es_ES": "No tienes permisos para editar este tour",
        "fr_FR": "Vous n'avez pas la permission de modifier cette visite",
        "hu_HU": "Nincs jogosultsága a túra szerkesztéséhez",
        "ja": "このツアーを編集する権限がありません",
        "nl_NL": "Je hebt geen toestemming om deze tour te bewerken",
        "pl_PL": "Nie masz uprawnień do edycji tej wycieczki",
        "pt_BR": "Você não tem permissão para editar este tour",
        "ru_RU": "У вас нет прав на редактирование этого тура",
    },
    "Non hai i permessi per eliminare questo tour": {
        "cs_CZ": "Nemáte oprávnění ke smazání této prohlídky",
        "de_DE": "Sie haben keine Berechtigung, diese Tour zu löschen",
        "en_US": "You don't have permission to delete this tour",
        "es_ES": "No tienes permisos para eliminar este tour",
        "fr_FR": "Vous n'avez pas la permission de supprimer cette visite",
        "hu_HU": "Nincs jogosultsága a túra törléséhez",
        "ja": "このツアーを削除する権限がありません",
        "nl_NL": "Je hebt geen toestemming om deze tour te verwijderen",
        "pl_PL": "Nie masz uprawnień do usunięcia tej wycieczki",
        "pt_BR": "Você não tem permissão para excluir este tour",
        "ru_RU": "У вас нет прав на удаление этого тура",
    },
    "Impossibile duplicare il tour": {
        "cs_CZ": "Prohlídku nelze duplikovat",
        "de_DE": "Tour kann nicht dupliziert werden",
        "en_US": "Unable to duplicate the tour",
        "es_ES": "No se puede duplicar el tour",
        "fr_FR": "Impossible de dupliquer la visite",
        "hu_HU": "A túra nem duplikálható",
        "ja": "ツアーを複製できません",
        "nl_NL": "Kan de tour niet dupliceren",
        "pl_PL": "Nie można zduplikować wycieczki",
        "pt_BR": "Não foi possível duplicar o tour",
        "ru_RU": "Не удалось дублировать тур",
    },
    "Revisione non trovata": {
        "cs_CZ": "Revize nenalezena",
        "de_DE": "Revision nicht gefunden",
        "en_US": "Revision not found",
        "es_ES": "Revisión no encontrada",
        "fr_FR": "Révision non trouvée",
        "hu_HU": "A változat nem található",
        "ja": "リビジョンが見つかりません",
        "nl_NL": "Revisie niet gevonden",
        "pl_PL": "Nie znaleziono wersji",
        "pt_BR": "Revisão não encontrada",
        "ru_RU": "Версия не найдена",
    },
    "Dati import non validi": {
        "cs_CZ": "Neplatná data importu",
        "de_DE": "Ungültige Importdaten",
        "en_US": "Invalid import data",
        "es_ES": "Datos de importación no válidos",
        "fr_FR": "Données d'importation non valides",
        "hu_HU": "Érvénytelen importálási adatok",
        "ja": "無効なインポートデータ",
        "nl_NL": "Ongeldige importgegevens",
        "pl_PL": "Nieprawidłowe dane importu",
        "pt_BR": "Dados de importação inválidos",
        "ru_RU": "Недопустимые данные импорта",
    },
    "%s (importato)": {
        "cs_CZ": "%s (importováno)",
        "de_DE": "%s (importiert)",
        "en_US": "%s (imported)",
        "es_ES": "%s (importado)",
        "fr_FR": "%s (importé)",
        "hu_HU": "%s (importálva)",
        "ja": "%s(インポート済み)",
        "nl_NL": "%s (geïmporteerd)",
        "pl_PL": "%s (zaimportowano)",
        "pt_BR": "%s (importado)",
        "ru_RU": "%s (импортировано)",
    },
    "Troppi tentativi. Riprova tra un minuto.": {
        "cs_CZ": "Příliš mnoho pokusů. Zkuste to znovu za minutu.",
        "de_DE": "Zu viele Versuche. Bitte versuchen Sie es in einer Minute erneut.",
        "en_US": "Too many attempts. Try again in a minute.",
        "es_ES": "Demasiados intentos. Inténtalo de nuevo en un minuto.",
        "fr_FR": "Trop de tentatives. Réessayez dans une minute.",
        "hu_HU": "Túl sok próbálkozás. Próbálja újra egy perc múlva.",
        "ja": "試行回数が多すぎます。1分後に再試行してください。",
        "nl_NL": "Te veel pogingen. Probeer het over een minuut opnieuw.",
        "pl_PL": "Zbyt wiele prób. Spróbuj ponownie za minutę.",
        "pt_BR": "Muitas tentativas. Tente novamente em um minuto.",
        "ru_RU": "Слишком много попыток. Повторите через минуту.",
    },
    "Password non corretta": {
        "cs_CZ": "Nesprávné heslo",
        "de_DE": "Falsches Passwort",
        "en_US": "Incorrect password",
        "es_ES": "Contraseña incorrecta",
        "fr_FR": "Mot de passe incorrect",
        "hu_HU": "Helytelen jelszó",
        "ja": "パスワードが正しくありません",
        "nl_NL": "Onjuist wachtwoord",
        "pl_PL": "Nieprawidłowe hasło",
        "pt_BR": "Senha incorreta",
        "ru_RU": "Неверный пароль",
    },
    "vtour_id richiesto": {
        "cs_CZ": "vtour_id je vyžadováno",
        "de_DE": "vtour_id erforderlich",
        "en_US": "vtour_id required",
        "es_ES": "vtour_id requerido",
        "fr_FR": "vtour_id requis",
        "hu_HU": "vtour_id szükséges",
        "ja": "vtour_id が必要です",
        "nl_NL": "vtour_id vereist",
        "pl_PL": "vtour_id wymagane",
        "pt_BR": "vtour_id obrigatório",
        "ru_RU": "Требуется vtour_id",
    },
    "OLOtour": {
        "cs_CZ": "OLOtour",
        "de_DE": "OLOtour",
        "en_US": "OLOtour",
        "es_ES": "OLOtour",
        "fr_FR": "OLOtour",
        "hu_HU": "OLOtour",
        "ja": "OLOtour",
        "nl_NL": "OLOtour",
        "pl_PL": "OLOtour",
        "pt_BR": "OLOtour",
        "ru_RU": "OLOtour",
    },
    "Nessun tile disponibile per questa immagine": {
        "cs_CZ": "Pro tento obrázek nejsou k dispozici žádné dlaždice",
        "de_DE": "Keine Kacheln für dieses Bild verfügbar",
        "en_US": "No tiles available for this image",
        "es_ES": "No hay teselas disponibles para esta imagen",
        "fr_FR": "Aucune tuile disponible pour cette image",
        "hu_HU": "Nincsenek elérhető csempék ehhez a képhez",
        "ja": "この画像には利用可能なタイルがありません",
        "nl_NL": "Geen tegels beschikbaar voor deze afbeelding",
        "pl_PL": "Brak dostępnych kafelków dla tego obrazu",
        "pt_BR": "Nenhum tile disponível para esta imagem",
        "ru_RU": "Нет доступных тайлов для этого изображения",
    },
    "Nuovo Tour": {
        "cs_CZ": "Nová prohlídka",
        "de_DE": "Neue Tour",
        "en_US": "New Tour",
        "es_ES": "Nuevo tour",
        "fr_FR": "Nouvelle visite",
        "hu_HU": "Új túra",
        "ja": "新しいツアー",
        "nl_NL": "Nieuwe tour",
        "pl_PL": "Nowa wycieczka",
        "pt_BR": "Novo tour",
        "ru_RU": "Новый тур",
    },
    "%s (copia)": {
        "cs_CZ": "%s (kopie)",
        "de_DE": "%s (Kopie)",
        "en_US": "%s (copy)",
        "es_ES": "%s (copia)",
        "fr_FR": "%s (copie)",
        "hu_HU": "%s (másolat)",
        "ja": "%s(コピー)",
        "nl_NL": "%s (kopie)",
        "pl_PL": "%s (kopia)",
        "pt_BR": "%s (cópia)",
        "ru_RU": "%s (копия)",
    },
    "Virtual Tour 360°": {
        "cs_CZ": "Virtuální prohlídka 360°",
        "de_DE": "360°-Virtual-Tour",
        "en_US": "360° Virtual Tour",
        "es_ES": "Tour virtual 360°",
        "fr_FR": "Visite virtuelle 360°",
        "hu_HU": "360°-os virtuális túra",
        "ja": "360°バーチャルツアー",
        "nl_NL": "360° virtuele tour",
        "pl_PL": "Wirtualna wycieczka 360°",
        "pt_BR": "Tour Virtual 360°",
        "ru_RU": "Виртуальный тур 360°",
    },
    "Seleziona un tour virtuale dall'inspector": {
        "cs_CZ": "Vyberte virtuální prohlídku v inspektoru",
        "de_DE": "Wählen Sie eine virtuelle Tour im Inspector aus",
        "en_US": "Select a virtual tour from the inspector",
        "es_ES": "Selecciona un tour virtual desde el inspector",
        "fr_FR": "Sélectionnez une visite virtuelle dans l'inspecteur",
        "hu_HU": "Válasszon virtuális túrát az inspectorból",
        "ja": "インスペクターから仮想ツアーを選択してください",
        "nl_NL": "Selecteer een virtuele tour in de inspector",
        "pl_PL": "Wybierz wirtualną wycieczkę z inspektora",
        "pt_BR": "Selecione um tour virtual no inspector",
        "ru_RU": "Выберите виртуальный тур в инспекторе",
    },
    "%s (bozza)": {
        "cs_CZ": "%s (koncept)",
        "de_DE": "%s (Entwurf)",
        "en_US": "%s (draft)",
        "es_ES": "%s (borrador)",
        "fr_FR": "%s (brouillon)",
        "hu_HU": "%s (piszkozat)",
        "ja": "%s(下書き)",
        "nl_NL": "%s (concept)",
        "pl_PL": "%s (szkic)",
        "pt_BR": "%s (rascunho)",
        "ru_RU": "%s (черновик)",
    },
    "🌐 Virtual Tour 360°": {
        "cs_CZ": "🌐 Virtuální prohlídka 360°",
        "de_DE": "🌐 360°-Virtual-Tour",
        "en_US": "🌐 360° Virtual Tour",
        "es_ES": "🌐 Tour virtual 360°",
        "fr_FR": "🌐 Visite virtuelle 360°",
        "hu_HU": "🌐 360°-os virtuális túra",
        "ja": "🌐 360°バーチャルツアー",
        "nl_NL": "🌐 360° virtuele tour",
        "pl_PL": "🌐 Wirtualna wycieczka 360°",
        "pt_BR": "🌐 Tour Virtual 360°",
        "ru_RU": "🌐 Виртуальный тур 360°",
    },
    "Tour": {
        "cs_CZ": "Prohlídka",
        "de_DE": "Tour",
        "en_US": "Tour",
        "es_ES": "Tour",
        "fr_FR": "Visite",
        "hu_HU": "Túra",
        "ja": "ツアー",
        "nl_NL": "Tour",
        "pl_PL": "Wycieczka",
        "pt_BR": "Tour",
        "ru_RU": "Тур",
    },
    "Seleziona tour": {
        "cs_CZ": "Vybrat prohlídku",
        "de_DE": "Tour auswählen",
        "en_US": "Select tour",
        "es_ES": "Seleccionar tour",
        "fr_FR": "Sélectionner une visite",
        "hu_HU": "Túra kiválasztása",
        "ja": "ツアーを選択",
        "nl_NL": "Tour selecteren",
        "pl_PL": "Wybierz wycieczkę",
        "pt_BR": "Selecionar tour",
        "ru_RU": "Выбрать тур",
    },
    "Altezza viewer": {
        "cs_CZ": "Výška prohlížeče",
        "de_DE": "Viewer-Höhe",
        "en_US": "Viewer height",
        "es_ES": "Altura del visor",
        "fr_FR": "Hauteur de la visionneuse",
        "hu_HU": "Néző magassága",
        "ja": "ビューアーの高さ",
        "nl_NL": "Viewer-hoogte",
        "pl_PL": "Wysokość przeglądarki",
        "pt_BR": "Altura do visualizador",
        "ru_RU": "Высота просмотрщика",
    },
    "Override": {
        "cs_CZ": "Přepsat",
        "de_DE": "Überschreiben",
        "en_US": "Override",
        "es_ES": "Sobrescribir",
        "fr_FR": "Remplacer",
        "hu_HU": "Felülírás",
        "ja": "オーバーライド",
        "nl_NL": "Overschrijven",
        "pl_PL": "Zastąp",
        "pt_BR": "Sobrescrever",
        "ru_RU": "Переопределить",
    },
    "Autorotazione": {
        "cs_CZ": "Automatické otáčení",
        "de_DE": "Automatische Drehung",
        "en_US": "Auto-rotation",
        "es_ES": "Rotación automática",
        "fr_FR": "Rotation automatique",
        "hu_HU": "Automatikus forgatás",
        "ja": "自動回転",
        "nl_NL": "Automatische rotatie",
        "pl_PL": "Automatyczne obracanie",
        "pt_BR": "Rotação automática",
        "ru_RU": "Автоповорот",
    },
    "Default del tour": {
        "cs_CZ": "Výchozí pro prohlídku",
        "de_DE": "Standard der Tour",
        "en_US": "Tour default",
        "es_ES": "Predeterminado del tour",
        "fr_FR": "Valeur par défaut de la visite",
        "hu_HU": "Túra alapértelmezett",
        "ja": "ツアーのデフォルト",
        "nl_NL": "Standaard van de tour",
        "pl_PL": "Domyślne wycieczki",
        "pt_BR": "Padrão do tour",
        "ru_RU": "По умолчанию для тура",
    },
    "Attiva": {
        "cs_CZ": "Aktivovat",
        "de_DE": "Aktivieren",
        "en_US": "Enable",
        "es_ES": "Activar",
        "fr_FR": "Activer",
        "hu_HU": "Bekapcsolás",
        "ja": "有効化",
        "nl_NL": "Inschakelen",
        "pl_PL": "Włącz",
        "pt_BR": "Ativar",
        "ru_RU": "Включить",
    },
    "Disattiva": {
        "cs_CZ": "Deaktivovat",
        "de_DE": "Deaktivieren",
        "en_US": "Disable",
        "es_ES": "Desactivar",
        "fr_FR": "Désactiver",
        "hu_HU": "Kikapcsolás",
        "ja": "無効化",
        "nl_NL": "Uitschakelen",
        "pl_PL": "Wyłącz",
        "pt_BR": "Desativar",
        "ru_RU": "Отключить",
    },
    "Scena iniziale (ID)": {
        "cs_CZ": "Počáteční scéna (ID)",
        "de_DE": "Anfangsszene (ID)",
        "en_US": "Initial scene (ID)",
        "es_ES": "Escena inicial (ID)",
        "fr_FR": "Scène initiale (ID)",
        "hu_HU": "Kezdő jelenet (ID)",
        "ja": "初期シーン(ID)",
        "nl_NL": "Beginscène (ID)",
        "pl_PL": "Scena początkowa (ID)",
        "pt_BR": "Cena inicial (ID)",
        "ru_RU": "Начальная сцена (ID)",
    },
    "Stile": {
        "cs_CZ": "Styl",
        "de_DE": "Stil",
        "en_US": "Style",
        "es_ES": "Estilo",
        "fr_FR": "Style",
        "hu_HU": "Stílus",
        "ja": "スタイル",
        "nl_NL": "Stijl",
        "pl_PL": "Styl",
        "pt_BR": "Estilo",
        "ru_RU": "Стиль",
    },
    "Bordo arrotondato": {
        "cs_CZ": "Zaoblený okraj",
        "de_DE": "Abgerundeter Rand",
        "en_US": "Rounded border",
        "es_ES": "Borde redondeado",
        "fr_FR": "Bordure arrondie",
        "hu_HU": "Lekerekített szegély",
        "ja": "角丸ボーダー",
        "nl_NL": "Afgeronde rand",
        "pl_PL": "Zaokrąglona ramka",
        "pt_BR": "Borda arredondada",
        "ru_RU": "Скруглённая граница",
    },
    "Tour non valido": {
        "cs_CZ": "Neplatná prohlídka",
        "de_DE": "Ungültige Tour",
        "en_US": "Invalid tour",
        "es_ES": "Tour no válido",
        "fr_FR": "Visite non valide",
        "hu_HU": "Érvénytelen túra",
        "ja": "無効なツアー",
        "nl_NL": "Ongeldige tour",
        "pl_PL": "Nieprawidłowa wycieczka",
        "pt_BR": "Tour inválido",
        "ru_RU": "Недопустимый тур",
    },
    "Errore": {
        "cs_CZ": "Chyba",
        "de_DE": "Fehler",
        "en_US": "Error",
        "es_ES": "Error",
        "fr_FR": "Erreur",
        "hu_HU": "Hiba",
        "ja": "エラー",
        "nl_NL": "Fout",
        "pl_PL": "Błąd",
        "pt_BR": "Erro",
        "ru_RU": "Ошибка",
    },
    "Tour non trovato o non pubblicato": {
        "cs_CZ": "Prohlídka nenalezena nebo nepublikována",
        "de_DE": "Tour nicht gefunden oder nicht veröffentlicht",
        "en_US": "Tour not found or not published",
        "es_ES": "Tour no encontrado o no publicado",
        "fr_FR": "Visite non trouvée ou non publiée",
        "hu_HU": "A túra nem található vagy nincs közzétéve",
        "ja": "ツアーが見つからないか、公開されていません",
        "nl_NL": "Tour niet gevonden of niet gepubliceerd",
        "pl_PL": "Nie znaleziono wycieczki lub nie została opublikowana",
        "pt_BR": "Tour não encontrado ou não publicado",
        "ru_RU": "Тур не найден или не опубликован",
    },
    "Inserisci un URL, coordinate o pano_id": {
        "cs_CZ": "Zadejte URL, souřadnice nebo pano_id",
        "de_DE": "Geben Sie eine URL, Koordinaten oder pano_id ein",
        "en_US": "Enter a URL, coordinates or pano_id",
        "es_ES": "Introduce una URL, coordenadas o pano_id",
        "fr_FR": "Saisissez une URL, des coordonnées ou un pano_id",
        "hu_HU": "Adjon meg URL-t, koordinátákat vagy pano_id-t",
        "ja": "URL、座標、または pano_id を入力してください",
        "nl_NL": "Voer een URL, coördinaten of pano_id in",
        "pl_PL": "Wprowadź URL, współrzędne lub pano_id",
        "pt_BR": "Insira uma URL, coordenadas ou pano_id",
        "ru_RU": "Введите URL, координаты или pano_id",
    },
    "Impossibile determinare il panorama": {
        "cs_CZ": "Panorama nelze určit",
        "de_DE": "Panorama kann nicht ermittelt werden",
        "en_US": "Unable to determine the panorama",
        "es_ES": "No se puede determinar el panorama",
        "fr_FR": "Impossible de déterminer le panorama",
        "hu_HU": "A panoráma nem határozható meg",
        "ja": "パノラマを特定できません",
        "nl_NL": "Kan het panorama niet bepalen",
        "pl_PL": "Nie można określić panoramy",
        "pt_BR": "Não foi possível determinar o panorama",
        "ru_RU": "Не удалось определить панораму",
    },
    "Panorama non trovato per pano_id: %1$s — %2$s": {
        "cs_CZ": "Panorama nenalezeno pro pano_id: %1$s — %2$s",
        "de_DE": "Panorama für pano_id nicht gefunden: %1$s — %2$s",
        "en_US": "Panorama not found for pano_id: %1$s — %2$s",
        "es_ES": "Panorama no encontrado para pano_id: %1$s — %2$s",
        "fr_FR": "Panorama non trouvé pour pano_id : %1$s — %2$s",
        "hu_HU": "Nem található panoráma a következő pano_id-hez: %1$s — %2$s",
        "ja": "pano_id のパノラマが見つかりません: %1$s — %2$s",
        "nl_NL": "Panorama niet gevonden voor pano_id: %1$s — %2$s",
        "pl_PL": "Nie znaleziono panoramy dla pano_id: %1$s — %2$s",
        "pt_BR": "Panorama não encontrado para pano_id: %1$s — %2$s",
        "ru_RU": "Панорама не найдена для pano_id: %1$s — %2$s",
    },
    "Impossibile risolvere il link: %s": {
        "cs_CZ": "Nelze rozpoznat odkaz: %s",
        "de_DE": "Link kann nicht aufgelöst werden: %s",
        "en_US": "Unable to resolve link: %s",
        "es_ES": "No se puede resolver el enlace: %s",
        "fr_FR": "Impossible de résoudre le lien : %s",
        "hu_HU": "A hivatkozás nem oldható fel: %s",
        "ja": "リンクを解決できません: %s",
        "nl_NL": "Kan de link niet oplossen: %s",
        "pl_PL": "Nie można rozpoznać linku: %s",
        "pt_BR": "Não foi possível resolver o link: %s",
        "ru_RU": "Не удалось разрешить ссылку: %s",
    },
    "Nessun panorama Street View trovato a queste coordinate": {
        "cs_CZ": "Pro tyto souřadnice nebylo nalezeno žádné panorama Street View",
        "de_DE": "Kein Street-View-Panorama an diesen Koordinaten gefunden",
        "en_US": "No Street View panorama found at these coordinates",
        "es_ES": "No se encontró ningún panorama de Street View en estas coordenadas",
        "fr_FR": "Aucun panorama Street View trouvé à ces coordonnées",
        "hu_HU": "Nem található Street View panoráma ezeken a koordinátákon",
        "ja": "これらの座標で Street View パノラマが見つかりません",
        "nl_NL": "Geen Street View-panorama gevonden op deze coördinaten",
        "pl_PL": "Nie znaleziono panoramy Street View w tych współrzędnych",
        "pt_BR": "Nenhum panorama do Street View encontrado nestas coordenadas",
        "ru_RU": "Панорама Street View не найдена по этим координатам",
    },
    "Panorama non trovato (HTTP %d)": {
        "cs_CZ": "Panorama nenalezeno (HTTP %d)",
        "de_DE": "Panorama nicht gefunden (HTTP %d)",
        "en_US": "Panorama not found (HTTP %d)",
        "es_ES": "Panorama no encontrado (HTTP %d)",
        "fr_FR": "Panorama non trouvé (HTTP %d)",
        "hu_HU": "Nem található panoráma (HTTP %d)",
        "ja": "パノラマが見つかりません(HTTP %d)",
        "nl_NL": "Panorama niet gevonden (HTTP %d)",
        "pl_PL": "Nie znaleziono panoramy (HTTP %d)",
        "pt_BR": "Panorama não encontrado (HTTP %d)",
        "ru_RU": "Панорама не найдена (HTTP %d)",
    },
    "Risposta photometa non valida": {
        "cs_CZ": "Neplatná odpověď photometa",
        "de_DE": "Ungültige photometa-Antwort",
        "en_US": "Invalid photometa response",
        "es_ES": "Respuesta de photometa no válida",
        "fr_FR": "Réponse photometa non valide",
        "hu_HU": "Érvénytelen photometa válasz",
        "ja": "無効な photometa 応答",
        "nl_NL": "Ongeldig photometa-antwoord",
        "pl_PL": "Nieprawidłowa odpowiedź photometa",
        "pt_BR": "Resposta photometa inválida",
        "ru_RU": "Недопустимый ответ photometa",
    },
    "Nessun dato panorama": {
        "cs_CZ": "Žádná data panoramatu",
        "de_DE": "Keine Panorama-Daten",
        "en_US": "No panorama data",
        "es_ES": "Sin datos de panorama",
        "fr_FR": "Aucune donnée de panorama",
        "hu_HU": "Nincs panoráma adat",
        "ja": "パノラマデータがありません",
        "nl_NL": "Geen panoramagegevens",
        "pl_PL": "Brak danych panoramy",
        "pt_BR": "Sem dados de panorama",
        "ru_RU": "Нет данных панорамы",
    },
    "pano_id mancante": {
        "cs_CZ": "Chybí pano_id",
        "de_DE": "pano_id fehlt",
        "en_US": "pano_id missing",
        "es_ES": "Falta pano_id",
        "fr_FR": "pano_id manquant",
        "hu_HU": "Hiányzó pano_id",
        "ja": "pano_id がありません",
        "nl_NL": "pano_id ontbreekt",
        "pl_PL": "Brak pano_id",
        "pt_BR": "pano_id ausente",
        "ru_RU": "Отсутствует pano_id",
    },
    "Estensione GD non disponibile sul server": {
        "cs_CZ": "Rozšíření GD není na serveru k dispozici",
        "de_DE": "GD-Erweiterung auf dem Server nicht verfügbar",
        "en_US": "GD extension not available on the server",
        "es_ES": "La extensión GD no está disponible en el servidor",
        "fr_FR": "Extension GD non disponible sur le serveur",
        "hu_HU": "A GD bővítmény nem érhető el a kiszolgálón",
        "ja": "サーバーで GD 拡張機能を利用できません",
        "nl_NL": "GD-extensie niet beschikbaar op de server",
        "pl_PL": "Rozszerzenie GD nie jest dostępne na serwerze",
        "pt_BR": "Extensão GD não disponível no servidor",
        "ru_RU": "Расширение GD недоступно на сервере",
    },
    "Zoom 5 richiede ~%1$dMB RAM. Limite attuale: %2$dMB. Usa zoom 4 o inferiore.": {
        "cs_CZ": "Zoom 5 vyžaduje ~%1$dMB RAM. Aktuální limit: %2$dMB. Použijte zoom 4 nebo nižší.",
        "de_DE": "Zoom 5 benötigt ~%1$dMB RAM. Aktuelles Limit: %2$dMB. Verwenden Sie Zoom 4 oder weniger.",
        "en_US": "Zoom 5 requires ~%1$dMB RAM. Current limit: %2$dMB. Use zoom 4 or lower.",
        "es_ES": "El zoom 5 requiere ~%1$dMB de RAM. Límite actual: %2$dMB. Usa zoom 4 o inferior.",
        "fr_FR": "Le zoom 5 nécessite ~%1$dMo de RAM. Limite actuelle : %2$dMo. Utilisez le zoom 4 ou inférieur.",
        "hu_HU": "A Zoom 5 ~%1$dMB RAM-ot igényel. Jelenlegi korlát: %2$dMB. Használjon Zoom 4 vagy alacsonyabb értéket.",
        "ja": "ズーム 5 には ~%1$dMB の RAM が必要です。現在の制限: %2$dMB。ズーム 4 以下を使用してください。",
        "nl_NL": "Zoom 5 vereist ~%1$dMB RAM. Huidige limiet: %2$dMB. Gebruik zoom 4 of lager.",
        "pl_PL": "Zoom 5 wymaga ~%1$dMB RAM. Aktualny limit: %2$dMB. Użyj zoomu 4 lub niższego.",
        "pt_BR": "O zoom 5 requer ~%1$dMB de RAM. Limite atual: %2$dMB. Use zoom 4 ou inferior.",
        "ru_RU": "Зум 5 требует ~%1$dМБ ОЗУ. Текущий лимит: %2$dМБ. Используйте зум 4 или ниже.",
    },
    "Impossibile creare immagine canvas": {
        "cs_CZ": "Nelze vytvořit obrázek plátna",
        "de_DE": "Canvas-Bild kann nicht erstellt werden",
        "en_US": "Unable to create canvas image",
        "es_ES": "No se puede crear la imagen del canvas",
        "fr_FR": "Impossible de créer l'image du canevas",
        "hu_HU": "Nem hozható létre canvas kép",
        "ja": "キャンバス画像を作成できません",
        "nl_NL": "Kan canvas-afbeelding niet maken",
        "pl_PL": "Nie można utworzyć obrazu kanwy",
        "pt_BR": "Não foi possível criar a imagem do canvas",
        "ru_RU": "Не удалось создать изображение canvas",
    },
    "Nessun tile scaricato con successo": {
        "cs_CZ": "Žádná dlaždice nebyla úspěšně stažena",
        "de_DE": "Keine Kachel erfolgreich heruntergeladen",
        "en_US": "No tile downloaded successfully",
        "es_ES": "No se descargó ninguna tesela con éxito",
        "fr_FR": "Aucune tuile téléchargée avec succès",
        "hu_HU": "Egy csempe sem töltődött le sikeresen",
        "ja": "タイルのダウンロードに成功しませんでした",
        "nl_NL": "Geen tegel succesvol gedownload",
        "pl_PL": "Nie pobrano pomyślnie żadnego kafelka",
        "pt_BR": "Nenhum tile baixado com sucesso",
        "ru_RU": "Ни один тайл не загружен успешно",
    },
    "Google Street View": {
        "cs_CZ": "Google Street View",
        "de_DE": "Google Street View",
        "en_US": "Google Street View",
        "es_ES": "Google Street View",
        "fr_FR": "Google Street View",
        "hu_HU": "Google Street View",
        "ja": "Google Street View",
        "nl_NL": "Google Street View",
        "pl_PL": "Google Street View",
        "pt_BR": "Google Street View",
        "ru_RU": "Google Street View",
    },
    "Street View %s": {
        "cs_CZ": "Street View %s",
        "de_DE": "Street View %s",
        "en_US": "Street View %s",
        "es_ES": "Street View %s",
        "fr_FR": "Street View %s",
        "hu_HU": "Street View %s",
        "ja": "Street View %s",
        "nl_NL": "Street View %s",
        "pl_PL": "Street View %s",
        "pt_BR": "Street View %s",
        "ru_RU": "Street View %s",
    },
}


def po_escape(s: str) -> str:
    """Escape a python str for use in a .po msgstr literal."""
    # Order matters: backslash first
    s = s.replace("\\", "\\\\")
    s = s.replace('"', '\\"')
    s = s.replace("\n", "\\n")
    s = s.replace("\t", "\\t")
    return s


def po_unescape(s: str) -> str:
    """Inverse of po_escape (for matching msgids from the file against
    keys in the translations dict)."""
    out = []
    i = 0
    while i < len(s):
        c = s[i]
        if c == "\\" and i + 1 < len(s):
            nxt = s[i + 1]
            if nxt == "n":
                out.append("\n")
            elif nxt == "t":
                out.append("\t")
            elif nxt == "\\":
                out.append("\\")
            elif nxt == '"':
                out.append('"')
            else:
                out.append(nxt)
            i += 2
        else:
            out.append(c)
            i += 1
    return "".join(out)


# Regex: capture a `msgid "..."` followed by `msgstr ""` (no continuation lines).
# We require msgstr to be empty (to avoid overwriting any work already done)
# AND we require msgid to NOT be empty (so we skip the .po header).
# The trailing newline after `msgstr ""` is optional (last entry may have no \n).
PAIR_RE = re.compile(
    r'(?P<msgid_line>msgid "(?P<msgid_value>(?:[^"\\]|\\.)*)"\n)'
    r'msgstr ""(?P<tail>\n|$)',
)


def process_file(path: str, lang: str) -> int:
    with open(path, "r", encoding="utf-8") as f:
        content = f.read()

    missing = []
    replaced = [0]

    def repl(m: re.Match) -> str:
        raw = m.group("msgid_value")
        # Skip the header: msgid "" → leave untouched (regex requires
        # msgstr "" which the header has, so we must explicitly skip).
        if raw == "":
            return m.group(0)

        msgid_decoded = po_unescape(raw)
        if msgid_decoded not in translations:
            missing.append(msgid_decoded)
            return m.group(0)

        lang_map = translations[msgid_decoded]
        if lang not in lang_map:
            missing.append(f"{msgid_decoded} [missing {lang}]")
            return m.group(0)

        translated = lang_map[lang]
        escaped = po_escape(translated)
        replaced[0] += 1
        tail = m.group("tail") or "\n"
        return f'{m.group("msgid_line")}msgstr "{escaped}"{tail}'

    new_content = PAIR_RE.sub(repl, content)

    # Update header: Last-Translator and PO-Revision-Date
    new_content = re.sub(
        r'"Last-Translator: [^"]*\\n"',
        r'"Last-Translator: Claude (AI translation)\\n"',
        new_content,
    )
    new_content = re.sub(
        r'"PO-Revision-Date: [^"]*\\n"',
        r'"PO-Revision-Date: 2026-05-21 12:00+0000\\n"',
        new_content,
    )

    with open(path, "w", encoding="utf-8", newline="\n") as f:
        f.write(new_content)

    if missing:
        sys.stderr.write(f"[{lang}] missing translations:\n")
        for m in missing:
            sys.stderr.write(f"  - {m!r}\n")

    return replaced[0]


def main() -> int:
    if not os.path.isdir(LANG_DIR):
        sys.stderr.write(f"Directory not found: {LANG_DIR}\n")
        return 1

    total = 0
    for lang in LANGS:
        path = os.path.join(LANG_DIR, f"olo-vtour-{lang}.po")
        if not os.path.isfile(path):
            sys.stderr.write(f"Skipping (file not found): {path}\n")
            continue
        count = process_file(path, lang)
        print(f"  {lang}: {count} msgstr filled")
        total += count

    print(f"\nDone. Total msgstr filled: {total} across {len(LANGS)} languages.")
    print(f"Expected: {len(translations) * len(LANGS)} (56 strings * 11 languages = 616)")
    return 0


if __name__ == "__main__":
    sys.exit(main())
