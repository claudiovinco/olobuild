#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
fill-po-calendar.py
Riempie i file .po di olo-calendar con traduzioni in 11 lingue.
Source language: it_IT (italiano).
"""
import os
import re
import sys

LANG_DIR = r"D:\TECNICA\olo-calendar\languages"

# Lingue target (suffisso file = chiave dict)
LANGS = [
    "cs_CZ", "de_DE", "en_US", "es_ES", "fr_FR",
    "hu_HU", "ja", "nl_NL", "pl_PL", "pt_BR", "ru_RU",
]

# Dizionario traduzioni: msgid IT -> { lang_code: traduzione }
TRANSLATIONS = {
    "Evento non trovato.": {
        "cs_CZ": "Událost nenalezena.",
        "de_DE": "Veranstaltung nicht gefunden.",
        "en_US": "Event not found.",
        "es_ES": "Evento no encontrado.",
        "fr_FR": "Événement introuvable.",
        "hu_HU": "Az esemény nem található.",
        "ja": "イベントが見つかりません。",
        "nl_NL": "Evenement niet gevonden.",
        "pl_PL": "Nie znaleziono wydarzenia.",
        "pt_BR": "Evento não encontrado.",
        "ru_RU": "Событие не найдено.",
    },
    "Il titolo e obbligatorio.": {
        "cs_CZ": "Název je povinný.",
        "de_DE": "Der Titel ist erforderlich.",
        "en_US": "The title is required.",
        "es_ES": "El título es obligatorio.",
        "fr_FR": "Le titre est obligatoire.",
        "hu_HU": "A cím megadása kötelező.",
        "ja": "タイトルは必須です。",
        "nl_NL": "De titel is verplicht.",
        "pl_PL": "Tytuł jest wymagany.",
        "pt_BR": "O título é obrigatório.",
        "ru_RU": "Заголовок обязателен.",
    },
    "Evento eliminato.": {
        "cs_CZ": "Událost smazána.",
        "de_DE": "Veranstaltung gelöscht.",
        "en_US": "Event deleted.",
        "es_ES": "Evento eliminado.",
        "fr_FR": "Événement supprimé.",
        "hu_HU": "Esemény törölve.",
        "ja": "イベントを削除しました。",
        "nl_NL": "Evenement verwijderd.",
        "pl_PL": "Wydarzenie usunięte.",
        "pt_BR": "Evento excluído.",
        "ru_RU": "Событие удалено.",
    },
    "Data inizio range (ISO 8601)": {
        "cs_CZ": "Počáteční datum rozsahu (ISO 8601)",
        "de_DE": "Startdatum des Bereichs (ISO 8601)",
        "en_US": "Range start date (ISO 8601)",
        "es_ES": "Fecha de inicio del rango (ISO 8601)",
        "fr_FR": "Date de début de la plage (ISO 8601)",
        "hu_HU": "Tartomány kezdő dátuma (ISO 8601)",
        "ja": "範囲の開始日 (ISO 8601)",
        "nl_NL": "Begindatum bereik (ISO 8601)",
        "pl_PL": "Data początkowa zakresu (ISO 8601)",
        "pt_BR": "Data de início do intervalo (ISO 8601)",
        "ru_RU": "Начальная дата диапазона (ISO 8601)",
    },
    "Data fine range (ISO 8601)": {
        "cs_CZ": "Koncové datum rozsahu (ISO 8601)",
        "de_DE": "Enddatum des Bereichs (ISO 8601)",
        "en_US": "Range end date (ISO 8601)",
        "es_ES": "Fecha de fin del rango (ISO 8601)",
        "fr_FR": "Date de fin de la plage (ISO 8601)",
        "hu_HU": "Tartomány záró dátuma (ISO 8601)",
        "ja": "範囲の終了日 (ISO 8601)",
        "nl_NL": "Einddatum bereik (ISO 8601)",
        "pl_PL": "Data końcowa zakresu (ISO 8601)",
        "pt_BR": "Data de fim do intervalo (ISO 8601)",
        "ru_RU": "Конечная дата диапазона (ISO 8601)",
    },
    "Slug categorie separati da virgola": {
        "cs_CZ": "Slugy kategorií oddělené čárkou",
        "de_DE": "Kategorie-Slugs, durch Komma getrennt",
        "en_US": "Category slugs separated by commas",
        "es_ES": "Slugs de categoría separados por comas",
        "fr_FR": "Slugs de catégories séparés par des virgules",
        "hu_HU": "Kategória-slug-ok vesszővel elválasztva",
        "ja": "カンマ区切りのカテゴリースラッグ",
        "nl_NL": "Categorie-slugs gescheiden door komma's",
        "pl_PL": "Slugi kategorii oddzielone przecinkami",
        "pt_BR": "Slugs de categoria separados por vírgula",
        "ru_RU": "Слаги категорий, разделённые запятыми",
    },
    "Ricerca nel titolo o luogo": {
        "cs_CZ": "Vyhledávání v názvu nebo místě",
        "de_DE": "Suche in Titel oder Ort",
        "en_US": "Search in title or location",
        "es_ES": "Buscar en título o ubicación",
        "fr_FR": "Recherche dans le titre ou le lieu",
        "hu_HU": "Keresés címben vagy helyszínen",
        "ja": "タイトルまたは場所で検索",
        "nl_NL": "Zoeken in titel of locatie",
        "pl_PL": "Wyszukiwanie w tytule lub miejscu",
        "pt_BR": "Buscar no título ou local",
        "ru_RU": "Поиск по названию или месту",
    },
    "Aggiungi evento": {
        "cs_CZ": "Přidat událost",
        "de_DE": "Veranstaltung hinzufügen",
        "en_US": "Add Event",
        "es_ES": "Añadir evento",
        "fr_FR": "Ajouter un événement",
        "hu_HU": "Esemény hozzáadása",
        "ja": "イベントを追加",
        "nl_NL": "Evenement toevoegen",
        "pl_PL": "Dodaj wydarzenie",
        "pt_BR": "Adicionar evento",
        "ru_RU": "Добавить событие",
    },
    "Aggiungi nuovo evento": {
        "cs_CZ": "Přidat novou událost",
        "de_DE": "Neue Veranstaltung hinzufügen",
        "en_US": "Add New Event",
        "es_ES": "Añadir nuevo evento",
        "fr_FR": "Ajouter un nouvel événement",
        "hu_HU": "Új esemény hozzáadása",
        "ja": "新規イベントを追加",
        "nl_NL": "Nieuw evenement toevoegen",
        "pl_PL": "Dodaj nowe wydarzenie",
        "pt_BR": "Adicionar novo evento",
        "ru_RU": "Добавить новое событие",
    },
    "Modifica evento": {
        "cs_CZ": "Upravit událost",
        "de_DE": "Veranstaltung bearbeiten",
        "en_US": "Edit Event",
        "es_ES": "Editar evento",
        "fr_FR": "Modifier l'événement",
        "hu_HU": "Esemény szerkesztése",
        "ja": "イベントを編集",
        "nl_NL": "Evenement bewerken",
        "pl_PL": "Edytuj wydarzenie",
        "pt_BR": "Editar evento",
        "ru_RU": "Редактировать событие",
    },
    "Nuovo evento": {
        "cs_CZ": "Nová událost",
        "de_DE": "Neue Veranstaltung",
        "en_US": "New Event",
        "es_ES": "Nuevo evento",
        "fr_FR": "Nouvel événement",
        "hu_HU": "Új esemény",
        "ja": "新規イベント",
        "nl_NL": "Nieuw evenement",
        "pl_PL": "Nowe wydarzenie",
        "pt_BR": "Novo evento",
        "ru_RU": "Новое событие",
    },
    "Visualizza evento": {
        "cs_CZ": "Zobrazit událost",
        "de_DE": "Veranstaltung anzeigen",
        "en_US": "View Event",
        "es_ES": "Ver evento",
        "fr_FR": "Voir l'événement",
        "hu_HU": "Esemény megtekintése",
        "ja": "イベントを表示",
        "nl_NL": "Evenement bekijken",
        "pl_PL": "Zobacz wydarzenie",
        "pt_BR": "Ver evento",
        "ru_RU": "Просмотреть событие",
    },
    "Cerca eventi": {
        "cs_CZ": "Hledat události",
        "de_DE": "Veranstaltungen suchen",
        "en_US": "Search Events",
        "es_ES": "Buscar eventos",
        "fr_FR": "Rechercher des événements",
        "hu_HU": "Események keresése",
        "ja": "イベントを検索",
        "nl_NL": "Evenementen zoeken",
        "pl_PL": "Szukaj wydarzeń",
        "pt_BR": "Buscar eventos",
        "ru_RU": "Искать события",
    },
    "Nessun evento trovato": {
        "cs_CZ": "Žádné události nenalezeny",
        "de_DE": "Keine Veranstaltungen gefunden",
        "en_US": "No events found",
        "es_ES": "No se han encontrado eventos",
        "fr_FR": "Aucun événement trouvé",
        "hu_HU": "Nem található esemény",
        "ja": "イベントが見つかりません",
        "nl_NL": "Geen evenementen gevonden",
        "pl_PL": "Nie znaleziono wydarzeń",
        "pt_BR": "Nenhum evento encontrado",
        "ru_RU": "События не найдены",
    },
    "Nessun evento nel cestino": {
        "cs_CZ": "Žádné události v koši",
        "de_DE": "Keine Veranstaltungen im Papierkorb",
        "en_US": "No events in Trash",
        "es_ES": "No hay eventos en la papelera",
        "fr_FR": "Aucun événement dans la corbeille",
        "hu_HU": "Nincs esemény a lomtárban",
        "ja": "ゴミ箱にイベントはありません",
        "nl_NL": "Geen evenementen in de prullenbak",
        "pl_PL": "Brak wydarzeń w koszu",
        "pt_BR": "Nenhum evento na lixeira",
        "ru_RU": "Нет событий в корзине",
    },
    "Tutti gli eventi": {
        "cs_CZ": "Všechny události",
        "de_DE": "Alle Veranstaltungen",
        "en_US": "All Events",
        "es_ES": "Todos los eventos",
        "fr_FR": "Tous les événements",
        "hu_HU": "Összes esemény",
        "ja": "すべてのイベント",
        "nl_NL": "Alle evenementen",
        "pl_PL": "Wszystkie wydarzenia",
        "pt_BR": "Todos os eventos",
        "ru_RU": "Все события",
    },
    "Olo Calendar": {
        "cs_CZ": "Olo Calendar",
        "de_DE": "Olo Calendar",
        "en_US": "Olo Calendar",
        "es_ES": "Olo Calendar",
        "fr_FR": "Olo Calendar",
        "hu_HU": "Olo Calendar",
        "ja": "Olo Calendar",
        "nl_NL": "Olo Calendar",
        "pl_PL": "Olo Calendar",
        "pt_BR": "Olo Calendar",
        "ru_RU": "Olo Calendar",
    },
    "Cerca categorie": {
        "cs_CZ": "Hledat kategorie",
        "de_DE": "Kategorien suchen",
        "en_US": "Search Categories",
        "es_ES": "Buscar categorías",
        "fr_FR": "Rechercher des catégories",
        "hu_HU": "Kategóriák keresése",
        "ja": "カテゴリーを検索",
        "nl_NL": "Categorieën zoeken",
        "pl_PL": "Szukaj kategorii",
        "pt_BR": "Buscar categorias",
        "ru_RU": "Искать категории",
    },
    "Tutte le categorie": {
        "cs_CZ": "Všechny kategorie",
        "de_DE": "Alle Kategorien",
        "en_US": "All Categories",
        "es_ES": "Todas las categorías",
        "fr_FR": "Toutes les catégories",
        "hu_HU": "Összes kategória",
        "ja": "すべてのカテゴリー",
        "nl_NL": "Alle categorieën",
        "pl_PL": "Wszystkie kategorie",
        "pt_BR": "Todas as categorias",
        "ru_RU": "Все категории",
    },
    "Modifica categoria": {
        "cs_CZ": "Upravit kategorii",
        "de_DE": "Kategorie bearbeiten",
        "en_US": "Edit Category",
        "es_ES": "Editar categoría",
        "fr_FR": "Modifier la catégorie",
        "hu_HU": "Kategória szerkesztése",
        "ja": "カテゴリーを編集",
        "nl_NL": "Categorie bewerken",
        "pl_PL": "Edytuj kategorię",
        "pt_BR": "Editar categoria",
        "ru_RU": "Редактировать категорию",
    },
    "Aggiungi categoria": {
        "cs_CZ": "Přidat kategorii",
        "de_DE": "Kategorie hinzufügen",
        "en_US": "Add Category",
        "es_ES": "Añadir categoría",
        "fr_FR": "Ajouter une catégorie",
        "hu_HU": "Kategória hozzáadása",
        "ja": "カテゴリーを追加",
        "nl_NL": "Categorie toevoegen",
        "pl_PL": "Dodaj kategorię",
        "pt_BR": "Adicionar categoria",
        "ru_RU": "Добавить категорию",
    },
    "Nome nuova categoria": {
        "cs_CZ": "Název nové kategorie",
        "de_DE": "Name der neuen Kategorie",
        "en_US": "New category name",
        "es_ES": "Nombre de la nueva categoría",
        "fr_FR": "Nom de la nouvelle catégorie",
        "hu_HU": "Új kategória neve",
        "ja": "新規カテゴリー名",
        "nl_NL": "Naam nieuwe categorie",
        "pl_PL": "Nazwa nowej kategorii",
        "pt_BR": "Nome da nova categoria",
        "ru_RU": "Название новой категории",
    },
    "Categorie": {
        "cs_CZ": "Kategorie",
        "de_DE": "Kategorien",
        "en_US": "Categories",
        "es_ES": "Categorías",
        "fr_FR": "Catégories",
        "hu_HU": "Kategóriák",
        "ja": "カテゴリー",
        "nl_NL": "Categorieën",
        "pl_PL": "Kategorie",
        "pt_BR": "Categorias",
        "ru_RU": "Категории",
    },
    "Data evento": {
        "cs_CZ": "Datum události",
        "de_DE": "Datum der Veranstaltung",
        "en_US": "Event date",
        "es_ES": "Fecha del evento",
        "fr_FR": "Date de l'événement",
        "hu_HU": "Esemény dátuma",
        "ja": "イベント日",
        "nl_NL": "Datum evenement",
        "pl_PL": "Data wydarzenia",
        "pt_BR": "Data do evento",
        "ru_RU": "Дата события",
    },
    "Orario": {
        "cs_CZ": "Čas",
        "de_DE": "Uhrzeit",
        "en_US": "Time",
        "es_ES": "Horario",
        "fr_FR": "Horaire",
        "hu_HU": "Időpont",
        "ja": "時刻",
        "nl_NL": "Tijd",
        "pl_PL": "Godzina",
        "pt_BR": "Horário",
        "ru_RU": "Время",
    },
    "Luogo": {
        "cs_CZ": "Místo",
        "de_DE": "Ort",
        "en_US": "Location",
        "es_ES": "Ubicación",
        "fr_FR": "Lieu",
        "hu_HU": "Helyszín",
        "ja": "場所",
        "nl_NL": "Locatie",
        "pl_PL": "Miejsce",
        "pt_BR": "Local",
        "ru_RU": "Место",
    },
    "Tutto il giorno": {
        "cs_CZ": "Celý den",
        "de_DE": "Ganztägig",
        "en_US": "All day",
        "es_ES": "Todo el día",
        "fr_FR": "Toute la journée",
        "hu_HU": "Egész nap",
        "ja": "終日",
        "nl_NL": "Hele dag",
        "pl_PL": "Cały dzień",
        "pt_BR": "Dia inteiro",
        "ru_RU": "Весь день",
    },
    "Colore": {
        "cs_CZ": "Barva",
        "de_DE": "Farbe",
        "en_US": "Color",
        "es_ES": "Color",
        "fr_FR": "Couleur",
        "hu_HU": "Szín",
        "ja": "色",
        "nl_NL": "Kleur",
        "pl_PL": "Kolor",
        "pt_BR": "Cor",
        "ru_RU": "Цвет",
    },
    "Colore per gli eventi di questa categoria nel calendario.": {
        "cs_CZ": "Barva pro události této kategorie v kalendáři.",
        "de_DE": "Farbe für Veranstaltungen dieser Kategorie im Kalender.",
        "en_US": "Color for events of this category in the calendar.",
        "es_ES": "Color para los eventos de esta categoría en el calendario.",
        "fr_FR": "Couleur des événements de cette catégorie dans le calendrier.",
        "hu_HU": "A kategória eseményeinek színe a naptárban.",
        "ja": "カレンダー上でこのカテゴリーのイベントに使用する色。",
        "nl_NL": "Kleur voor evenementen van deze categorie in de kalender.",
        "pl_PL": "Kolor wydarzeń tej kategorii w kalendarzu.",
        "pt_BR": "Cor para os eventos desta categoria no calendário.",
        "ru_RU": "Цвет для событий этой категории в календаре.",
    },
    "Eventi": {
        "cs_CZ": "Události",
        "de_DE": "Veranstaltungen",
        "en_US": "Events",
        "es_ES": "Eventos",
        "fr_FR": "Événements",
        "hu_HU": "Események",
        "ja": "イベント",
        "nl_NL": "Evenementen",
        "pl_PL": "Wydarzenia",
        "pt_BR": "Eventos",
        "ru_RU": "События",
    },
    "Evento": {
        "cs_CZ": "Událost",
        "de_DE": "Veranstaltung",
        "en_US": "Event",
        "es_ES": "Evento",
        "fr_FR": "Événement",
        "hu_HU": "Esemény",
        "ja": "イベント",
        "nl_NL": "Evenement",
        "pl_PL": "Wydarzenie",
        "pt_BR": "Evento",
        "ru_RU": "Событие",
    },
    "Categorie Evento": {
        "cs_CZ": "Kategorie událostí",
        "de_DE": "Veranstaltungskategorien",
        "en_US": "Event Categories",
        "es_ES": "Categorías de eventos",
        "fr_FR": "Catégories d'événements",
        "hu_HU": "Esemény kategóriák",
        "ja": "イベントカテゴリー",
        "nl_NL": "Evenementcategorieën",
        "pl_PL": "Kategorie wydarzeń",
        "pt_BR": "Categorias de eventos",
        "ru_RU": "Категории событий",
    },
    "Categoria Evento": {
        "cs_CZ": "Kategorie události",
        "de_DE": "Veranstaltungskategorie",
        "en_US": "Event Category",
        "es_ES": "Categoría de evento",
        "fr_FR": "Catégorie d'événement",
        "hu_HU": "Esemény kategória",
        "ja": "イベントカテゴリー",
        "nl_NL": "Evenementcategorie",
        "pl_PL": "Kategoria wydarzenia",
        "pt_BR": "Categoria de evento",
        "ru_RU": "Категория события",
    },
    "Calendario eventi interattivo con filtri e modale dettagli.": {
        "cs_CZ": "Interaktivní kalendář událostí s filtry a modálním oknem s detaily.",
        "de_DE": "Interaktiver Veranstaltungskalender mit Filtern und Detail-Modal.",
        "en_US": "Interactive event calendar with filters and details modal.",
        "es_ES": "Calendario de eventos interactivo con filtros y modal de detalles.",
        "fr_FR": "Calendrier d'événements interactif avec filtres et modale de détails.",
        "hu_HU": "Interaktív eseménynaptár szűrőkkel és részletek modállal.",
        "ja": "フィルターと詳細モーダルを備えたインタラクティブなイベントカレンダー。",
        "nl_NL": "Interactieve evenementenkalender met filters en detailmodaal.",
        "pl_PL": "Interaktywny kalendarz wydarzeń z filtrami i oknem ze szczegółami.",
        "pt_BR": "Calendário de eventos interativo com filtros e modal de detalhes.",
        "ru_RU": "Интерактивный календарь событий с фильтрами и модальным окном деталей.",
    },
    "calendario": {
        "cs_CZ": "kalendář",
        "de_DE": "kalender",
        "en_US": "calendar",
        "es_ES": "calendario",
        "fr_FR": "calendrier",
        "hu_HU": "naptár",
        "ja": "カレンダー",
        "nl_NL": "kalender",
        "pl_PL": "kalendarz",
        "pt_BR": "calendário",
        "ru_RU": "календарь",
    },
    "eventi": {
        "cs_CZ": "události",
        "de_DE": "veranstaltungen",
        "en_US": "events",
        "es_ES": "eventos",
        "fr_FR": "événements",
        "hu_HU": "események",
        "ja": "イベント",
        "nl_NL": "evenementen",
        "pl_PL": "wydarzenia",
        "pt_BR": "eventos",
        "ru_RU": "события",
    },
    "calendar": {
        "cs_CZ": "calendar",
        "de_DE": "calendar",
        "en_US": "calendar",
        "es_ES": "calendar",
        "fr_FR": "calendar",
        "hu_HU": "calendar",
        "ja": "calendar",
        "nl_NL": "calendar",
        "pl_PL": "calendar",
        "pt_BR": "calendar",
        "ru_RU": "calendar",
    },
    "events": {
        "cs_CZ": "events",
        "de_DE": "events",
        "en_US": "events",
        "es_ES": "events",
        "fr_FR": "events",
        "hu_HU": "events",
        "ja": "events",
        "nl_NL": "events",
        "pl_PL": "events",
        "pt_BR": "events",
        "ru_RU": "events",
    },
    "Editor Evento": {
        "cs_CZ": "Editor událostí",
        "de_DE": "Veranstaltungseditor",
        "en_US": "Event Editor",
        "es_ES": "Editor de eventos",
        "fr_FR": "Éditeur d'événement",
        "hu_HU": "Esemény szerkesztő",
        "ja": "イベントエディター",
        "nl_NL": "Evenementeditor",
        "pl_PL": "Edytor wydarzeń",
        "pt_BR": "Editor de evento",
        "ru_RU": "Редактор события",
    },
    "Aggiorna evento": {
        "cs_CZ": "Aktualizovat událost",
        "de_DE": "Veranstaltung aktualisieren",
        "en_US": "Update event",
        "es_ES": "Actualizar evento",
        "fr_FR": "Mettre à jour l'événement",
        "hu_HU": "Esemény frissítése",
        "ja": "イベントを更新",
        "nl_NL": "Evenement bijwerken",
        "pl_PL": "Aktualizuj wydarzenie",
        "pt_BR": "Atualizar evento",
        "ru_RU": "Обновить событие",
    },
    "Pubblica evento": {
        "cs_CZ": "Publikovat událost",
        "de_DE": "Veranstaltung veröffentlichen",
        "en_US": "Publish event",
        "es_ES": "Publicar evento",
        "fr_FR": "Publier l'événement",
        "hu_HU": "Esemény közzététele",
        "ja": "イベントを公開",
        "nl_NL": "Evenement publiceren",
        "pl_PL": "Opublikuj wydarzenie",
        "pt_BR": "Publicar evento",
        "ru_RU": "Опубликовать событие",
    },
    "Torna alla dashboard": {
        "cs_CZ": "Zpět na dashboard",
        "de_DE": "Zurück zum Dashboard",
        "en_US": "Back to dashboard",
        "es_ES": "Volver al panel",
        "fr_FR": "Retour au tableau de bord",
        "hu_HU": "Vissza a vezérlőpultra",
        "ja": "ダッシュボードに戻る",
        "nl_NL": "Terug naar dashboard",
        "pl_PL": "Wróć do panelu",
        "pt_BR": "Voltar ao painel",
        "ru_RU": "Назад к панели",
    },
    "Anteprima": {
        "cs_CZ": "Náhled",
        "de_DE": "Vorschau",
        "en_US": "Preview",
        "es_ES": "Vista previa",
        "fr_FR": "Aperçu",
        "hu_HU": "Előnézet",
        "ja": "プレビュー",
        "nl_NL": "Voorbeeld",
        "pl_PL": "Podgląd",
        "pt_BR": "Pré-visualização",
        "ru_RU": "Предпросмотр",
    },
    "Titolo evento": {
        "cs_CZ": "Název události",
        "de_DE": "Titel der Veranstaltung",
        "en_US": "Event title",
        "es_ES": "Título del evento",
        "fr_FR": "Titre de l'événement",
        "hu_HU": "Esemény címe",
        "ja": "イベントタイトル",
        "nl_NL": "Titel evenement",
        "pl_PL": "Tytuł wydarzenia",
        "pt_BR": "Título do evento",
        "ru_RU": "Название события",
    },
    "Descrizione": {
        "cs_CZ": "Popis",
        "de_DE": "Beschreibung",
        "en_US": "Description",
        "es_ES": "Descripción",
        "fr_FR": "Description",
        "hu_HU": "Leírás",
        "ja": "説明",
        "nl_NL": "Beschrijving",
        "pl_PL": "Opis",
        "pt_BR": "Descrição",
        "ru_RU": "Описание",
    },
    "Descrizione completa dell'evento...": {
        "cs_CZ": "Úplný popis události…",
        "de_DE": "Vollständige Beschreibung der Veranstaltung…",
        "en_US": "Full event description…",
        "es_ES": "Descripción completa del evento…",
        "fr_FR": "Description complète de l'événement…",
        "hu_HU": "Az esemény teljes leírása…",
        "ja": "イベントの詳細説明…",
        "nl_NL": "Volledige beschrijving van het evenement…",
        "pl_PL": "Pełny opis wydarzenia…",
        "pt_BR": "Descrição completa do evento…",
        "ru_RU": "Полное описание события…",
    },
    "Descrizione breve": {
        "cs_CZ": "Krátký popis",
        "de_DE": "Kurzbeschreibung",
        "en_US": "Short description",
        "es_ES": "Descripción breve",
        "fr_FR": "Description courte",
        "hu_HU": "Rövid leírás",
        "ja": "概要",
        "nl_NL": "Korte beschrijving",
        "pl_PL": "Krótki opis",
        "pt_BR": "Descrição breve",
        "ru_RU": "Краткое описание",
    },
    "Breve descrizione visibile nella modale del calendario": {
        "cs_CZ": "Krátký popis viditelný v modálním okně kalendáře",
        "de_DE": "Kurzbeschreibung, sichtbar im Kalender-Modal",
        "en_US": "Short description visible in the calendar modal",
        "es_ES": "Descripción breve visible en el modal del calendario",
        "fr_FR": "Description courte visible dans la modale du calendrier",
        "hu_HU": "Rövid leírás, mely a naptár modálban jelenik meg",
        "ja": "カレンダーのモーダルに表示される概要",
        "nl_NL": "Korte beschrijving, zichtbaar in het kalender-modaal",
        "pl_PL": "Krótki opis widoczny w oknie modalnym kalendarza",
        "pt_BR": "Descrição breve visível no modal do calendário",
        "ru_RU": "Краткое описание, отображаемое в модальном окне календаря",
    },
    "Data e ora": {
        "cs_CZ": "Datum a čas",
        "de_DE": "Datum und Uhrzeit",
        "en_US": "Date and time",
        "es_ES": "Fecha y hora",
        "fr_FR": "Date et heure",
        "hu_HU": "Dátum és idő",
        "ja": "日時",
        "nl_NL": "Datum en tijd",
        "pl_PL": "Data i godzina",
        "pt_BR": "Data e hora",
        "ru_RU": "Дата и время",
    },
    "Inizio *": {
        "cs_CZ": "Začátek *",
        "de_DE": "Beginn *",
        "en_US": "Start *",
        "es_ES": "Inicio *",
        "fr_FR": "Début *",
        "hu_HU": "Kezdés *",
        "ja": "開始 *",
        "nl_NL": "Begin *",
        "pl_PL": "Początek *",
        "pt_BR": "Início *",
        "ru_RU": "Начало *",
    },
    "Ora inizio": {
        "cs_CZ": "Čas zahájení",
        "de_DE": "Startzeit",
        "en_US": "Start time",
        "es_ES": "Hora de inicio",
        "fr_FR": "Heure de début",
        "hu_HU": "Kezdés időpontja",
        "ja": "開始時刻",
        "nl_NL": "Begintijd",
        "pl_PL": "Godzina rozpoczęcia",
        "pt_BR": "Hora de início",
        "ru_RU": "Время начала",
    },
    "Fine": {
        "cs_CZ": "Konec",
        "de_DE": "Ende",
        "en_US": "End",
        "es_ES": "Fin",
        "fr_FR": "Fin",
        "hu_HU": "Vége",
        "ja": "終了",
        "nl_NL": "Einde",
        "pl_PL": "Koniec",
        "pt_BR": "Fim",
        "ru_RU": "Конец",
    },
    "Ora fine": {
        "cs_CZ": "Čas ukončení",
        "de_DE": "Endzeit",
        "en_US": "End time",
        "es_ES": "Hora de fin",
        "fr_FR": "Heure de fin",
        "hu_HU": "Befejezés időpontja",
        "ja": "終了時刻",
        "nl_NL": "Eindtijd",
        "pl_PL": "Godzina zakończenia",
        "pt_BR": "Hora de fim",
        "ru_RU": "Время окончания",
    },
    "es. Centro Congressi, Milano": {
        "cs_CZ": "např. Kongresové centrum, Praha",
        "de_DE": "z. B. Kongresszentrum, Berlin",
        "en_US": "e.g. Convention Center, Milan",
        "es_ES": "p. ej. Palacio de Congresos, Madrid",
        "fr_FR": "ex. Palais des Congrès, Paris",
        "hu_HU": "pl. Kongresszusi Központ, Budapest",
        "ja": "例: コンベンションセンター、東京",
        "nl_NL": "bijv. Congrescentrum, Amsterdam",
        "pl_PL": "np. Centrum Kongresowe, Warszawa",
        "pt_BR": "ex. Centro de Convenções, São Paulo",
        "ru_RU": "например, Конгресс-центр, Москва",
    },
    "Link Google Maps": {
        "cs_CZ": "Odkaz Google Maps",
        "de_DE": "Google-Maps-Link",
        "en_US": "Google Maps link",
        "es_ES": "Enlace de Google Maps",
        "fr_FR": "Lien Google Maps",
        "hu_HU": "Google Maps hivatkozás",
        "ja": "Google マップのリンク",
        "nl_NL": "Google Maps-link",
        "pl_PL": "Link Google Maps",
        "pt_BR": "Link do Google Maps",
        "ru_RU": "Ссылка Google Maps",
    },
    "Immagine in evidenza": {
        "cs_CZ": "Náhledový obrázek",
        "de_DE": "Beitragsbild",
        "en_US": "Featured image",
        "es_ES": "Imagen destacada",
        "fr_FR": "Image mise en avant",
        "hu_HU": "Kiemelt kép",
        "ja": "アイキャッチ画像",
        "nl_NL": "Uitgelichte afbeelding",
        "pl_PL": "Obrazek wyróżniający",
        "pt_BR": "Imagem destacada",
        "ru_RU": "Изображение записи",
    },
    "Rimuovi immagine": {
        "cs_CZ": "Odebrat obrázek",
        "de_DE": "Bild entfernen",
        "en_US": "Remove image",
        "es_ES": "Eliminar imagen",
        "fr_FR": "Supprimer l'image",
        "hu_HU": "Kép eltávolítása",
        "ja": "画像を削除",
        "nl_NL": "Afbeelding verwijderen",
        "pl_PL": "Usuń obraz",
        "pt_BR": "Remover imagem",
        "ru_RU": "Удалить изображение",
    },
    "Carica immagine": {
        "cs_CZ": "Nahrát obrázek",
        "de_DE": "Bild hochladen",
        "en_US": "Upload image",
        "es_ES": "Subir imagen",
        "fr_FR": "Téléverser une image",
        "hu_HU": "Kép feltöltése",
        "ja": "画像をアップロード",
        "nl_NL": "Afbeelding uploaden",
        "pl_PL": "Prześlij obraz",
        "pt_BR": "Enviar imagem",
        "ru_RU": "Загрузить изображение",
    },
    "Categoria": {
        "cs_CZ": "Kategorie",
        "de_DE": "Kategorie",
        "en_US": "Category",
        "es_ES": "Categoría",
        "fr_FR": "Catégorie",
        "hu_HU": "Kategória",
        "ja": "カテゴリー",
        "nl_NL": "Categorie",
        "pl_PL": "Kategoria",
        "pt_BR": "Categoria",
        "ru_RU": "Категория",
    },
    "Nessuna categoria": {
        "cs_CZ": "Žádná kategorie",
        "de_DE": "Keine Kategorie",
        "en_US": "No category",
        "es_ES": "Sin categoría",
        "fr_FR": "Aucune catégorie",
        "hu_HU": "Nincs kategória",
        "ja": "カテゴリーなし",
        "nl_NL": "Geen categorie",
        "pl_PL": "Brak kategorii",
        "pt_BR": "Sem categoria",
        "ru_RU": "Без категории",
    },
    "Colore evento": {
        "cs_CZ": "Barva události",
        "de_DE": "Veranstaltungsfarbe",
        "en_US": "Event color",
        "es_ES": "Color del evento",
        "fr_FR": "Couleur de l'événement",
        "hu_HU": "Esemény színe",
        "ja": "イベントの色",
        "nl_NL": "Kleur evenement",
        "pl_PL": "Kolor wydarzenia",
        "pt_BR": "Cor do evento",
        "ru_RU": "Цвет события",
    },
    "Usa colore categoria": {
        "cs_CZ": "Použít barvu kategorie",
        "de_DE": "Kategoriefarbe verwenden",
        "en_US": "Use category color",
        "es_ES": "Usar color de la categoría",
        "fr_FR": "Utiliser la couleur de la catégorie",
        "hu_HU": "Kategória színének használata",
        "ja": "カテゴリーの色を使用",
        "nl_NL": "Categoriekleur gebruiken",
        "pl_PL": "Użyj koloru kategorii",
        "pt_BR": "Usar cor da categoria",
        "ru_RU": "Использовать цвет категории",
    },
    "Sovrascrive il colore della categoria": {
        "cs_CZ": "Přepíše barvu kategorie",
        "de_DE": "Überschreibt die Farbe der Kategorie",
        "en_US": "Overrides the category color",
        "es_ES": "Sobrescribe el color de la categoría",
        "fr_FR": "Remplace la couleur de la catégorie",
        "hu_HU": "Felülírja a kategória színét",
        "ja": "カテゴリーの色を上書きします",
        "nl_NL": "Overschrijft de categoriekleur",
        "pl_PL": "Nadpisuje kolor kategorii",
        "pt_BR": "Substitui a cor da categoria",
        "ru_RU": "Переопределяет цвет категории",
    },
    "Link esterno": {
        "cs_CZ": "Externí odkaz",
        "de_DE": "Externer Link",
        "en_US": "External link",
        "es_ES": "Enlace externo",
        "fr_FR": "Lien externe",
        "hu_HU": "Külső hivatkozás",
        "ja": "外部リンク",
        "nl_NL": "Externe link",
        "pl_PL": "Link zewnętrzny",
        "pt_BR": "Link externo",
        "ru_RU": "Внешняя ссылка",
    },
    "Link opzionale visibile nella modale": {
        "cs_CZ": "Volitelný odkaz viditelný v modálním okně",
        "de_DE": "Optionaler Link, sichtbar im Modal",
        "en_US": "Optional link visible in the modal",
        "es_ES": "Enlace opcional visible en el modal",
        "fr_FR": "Lien optionnel visible dans la modale",
        "hu_HU": "Választható hivatkozás, mely a modálban jelenik meg",
        "ja": "モーダルに表示される任意のリンク",
        "nl_NL": "Optionele link zichtbaar in het modaal",
        "pl_PL": "Opcjonalny link widoczny w oknie modalnym",
        "pt_BR": "Link opcional visível no modal",
        "ru_RU": "Необязательная ссылка, отображаемая в модальном окне",
    },
    "Elimina evento": {
        "cs_CZ": "Smazat událost",
        "de_DE": "Veranstaltung löschen",
        "en_US": "Delete event",
        "es_ES": "Eliminar evento",
        "fr_FR": "Supprimer l'événement",
        "hu_HU": "Esemény törlése",
        "ja": "イベントを削除",
        "nl_NL": "Evenement verwijderen",
        "pl_PL": "Usuń wydarzenie",
        "pt_BR": "Excluir evento",
        "ru_RU": "Удалить событие",
    },
    "Eliminare questo evento?": {
        "cs_CZ": "Smazat tuto událost?",
        "de_DE": "Diese Veranstaltung löschen?",
        "en_US": "Delete this event?",
        "es_ES": "¿Eliminar este evento?",
        "fr_FR": "Supprimer cet événement ?",
        "hu_HU": "Törli ezt az eseményt?",
        "ja": "このイベントを削除しますか?",
        "nl_NL": "Dit evenement verwijderen?",
        "pl_PL": "Usunąć to wydarzenie?",
        "pt_BR": "Excluir este evento?",
        "ru_RU": "Удалить это событие?",
    },
    "Questa azione non puo essere annullata.": {
        "cs_CZ": "Tuto akci nelze vrátit zpět.",
        "de_DE": "Diese Aktion kann nicht rückgängig gemacht werden.",
        "en_US": "This action cannot be undone.",
        "es_ES": "Esta acción no se puede deshacer.",
        "fr_FR": "Cette action est irréversible.",
        "hu_HU": "Ez a művelet nem vonható vissza.",
        "ja": "この操作は取り消せません。",
        "nl_NL": "Deze actie kan niet ongedaan worden gemaakt.",
        "pl_PL": "Tej akcji nie można cofnąć.",
        "pt_BR": "Esta ação não pode ser desfeita.",
        "ru_RU": "Это действие нельзя отменить.",
    },
    "Annulla": {
        "cs_CZ": "Zrušit",
        "de_DE": "Abbrechen",
        "en_US": "Cancel",
        "es_ES": "Cancelar",
        "fr_FR": "Annuler",
        "hu_HU": "Mégse",
        "ja": "キャンセル",
        "nl_NL": "Annuleren",
        "pl_PL": "Anuluj",
        "pt_BR": "Cancelar",
        "ru_RU": "Отмена",
    },
    "Elimina": {
        "cs_CZ": "Smazat",
        "de_DE": "Löschen",
        "en_US": "Delete",
        "es_ES": "Eliminar",
        "fr_FR": "Supprimer",
        "hu_HU": "Törlés",
        "ja": "削除",
        "nl_NL": "Verwijderen",
        "pl_PL": "Usuń",
        "pt_BR": "Excluir",
        "ru_RU": "Удалить",
    },
    "Dettagli Evento": {
        "cs_CZ": "Detaily události",
        "de_DE": "Veranstaltungsdetails",
        "en_US": "Event Details",
        "es_ES": "Detalles del evento",
        "fr_FR": "Détails de l'événement",
        "hu_HU": "Esemény részletei",
        "ja": "イベントの詳細",
        "nl_NL": "Details evenement",
        "pl_PL": "Szczegóły wydarzenia",
        "pt_BR": "Detalhes do evento",
        "ru_RU": "Детали события",
    },
    "Data inizio": {
        "cs_CZ": "Datum zahájení",
        "de_DE": "Startdatum",
        "en_US": "Start date",
        "es_ES": "Fecha de inicio",
        "fr_FR": "Date de début",
        "hu_HU": "Kezdő dátum",
        "ja": "開始日",
        "nl_NL": "Begindatum",
        "pl_PL": "Data rozpoczęcia",
        "pt_BR": "Data de início",
        "ru_RU": "Дата начала",
    },
    "Data fine": {
        "cs_CZ": "Datum ukončení",
        "de_DE": "Enddatum",
        "en_US": "End date",
        "es_ES": "Fecha de fin",
        "fr_FR": "Date de fin",
        "hu_HU": "Befejező dátum",
        "ja": "終了日",
        "nl_NL": "Einddatum",
        "pl_PL": "Data zakończenia",
        "pt_BR": "Data de fim",
        "ru_RU": "Дата окончания",
    },
    "es. Centro Congressi, Roma": {
        "cs_CZ": "např. Kongresové centrum, Praha",
        "de_DE": "z. B. Kongresszentrum, München",
        "en_US": "e.g. Convention Center, Rome",
        "es_ES": "p. ej. Palacio de Congresos, Barcelona",
        "fr_FR": "ex. Palais des Congrès, Lyon",
        "hu_HU": "pl. Kongresszusi Központ, Debrecen",
        "ja": "例: コンベンションセンター、大阪",
        "nl_NL": "bijv. Congrescentrum, Rotterdam",
        "pl_PL": "np. Centrum Kongresowe, Kraków",
        "pt_BR": "ex. Centro de Convenções, Rio de Janeiro",
        "ru_RU": "например, Конгресс-центр, Санкт-Петербург",
    },
    "URL mappa": {
        "cs_CZ": "URL mapy",
        "de_DE": "Karten-URL",
        "en_US": "Map URL",
        "es_ES": "URL del mapa",
        "fr_FR": "URL de la carte",
        "hu_HU": "Térkép URL",
        "ja": "マップの URL",
        "nl_NL": "Kaart-URL",
        "pl_PL": "URL mapy",
        "pt_BR": "URL do mapa",
        "ru_RU": "URL карты",
    },
    "URL esterno": {
        "cs_CZ": "Externí URL",
        "de_DE": "Externe URL",
        "en_US": "External URL",
        "es_ES": "URL externa",
        "fr_FR": "URL externe",
        "hu_HU": "Külső URL",
        "ja": "外部 URL",
        "nl_NL": "Externe URL",
        "pl_PL": "Zewnętrzny URL",
        "pt_BR": "URL externa",
        "ru_RU": "Внешний URL",
    },
    "Link esterno (opzionale)": {
        "cs_CZ": "Externí odkaz (volitelné)",
        "de_DE": "Externer Link (optional)",
        "en_US": "External link (optional)",
        "es_ES": "Enlace externo (opcional)",
        "fr_FR": "Lien externe (optionnel)",
        "hu_HU": "Külső hivatkozás (opcionális)",
        "ja": "外部リンク (任意)",
        "nl_NL": "Externe link (optioneel)",
        "pl_PL": "Link zewnętrzny (opcjonalny)",
        "pt_BR": "Link externo (opcional)",
        "ru_RU": "Внешняя ссылка (необязательно)",
    },
    "Tutti": {
        "cs_CZ": "Vše",
        "de_DE": "Alle",
        "en_US": "All",
        "es_ES": "Todos",
        "fr_FR": "Tous",
        "hu_HU": "Mind",
        "ja": "すべて",
        "nl_NL": "Alle",
        "pl_PL": "Wszystkie",
        "pt_BR": "Todos",
        "ru_RU": "Все",
    },
    "Dettagli evento": {
        "cs_CZ": "Detaily události",
        "de_DE": "Veranstaltungsdetails",
        "en_US": "Event details",
        "es_ES": "Detalles del evento",
        "fr_FR": "Détails de l'événement",
        "hu_HU": "Esemény részletei",
        "ja": "イベントの詳細",
        "nl_NL": "Details evenement",
        "pl_PL": "Szczegóły wydarzenia",
        "pt_BR": "Detalhes do evento",
        "ru_RU": "Детали события",
    },
    "Chiudi": {
        "cs_CZ": "Zavřít",
        "de_DE": "Schließen",
        "en_US": "Close",
        "es_ES": "Cerrar",
        "fr_FR": "Fermer",
        "hu_HU": "Bezárás",
        "ja": "閉じる",
        "nl_NL": "Sluiten",
        "pl_PL": "Zamknij",
        "pt_BR": "Fechar",
        "ru_RU": "Закрыть",
    },
    "Aggiungi al calendario": {
        "cs_CZ": "Přidat do kalendáře",
        "de_DE": "Zum Kalender hinzufügen",
        "en_US": "Add to calendar",
        "es_ES": "Añadir al calendario",
        "fr_FR": "Ajouter au calendrier",
        "hu_HU": "Hozzáadás a naptárhoz",
        "ja": "カレンダーに追加",
        "nl_NL": "Toevoegen aan kalender",
        "pl_PL": "Dodaj do kalendarza",
        "pt_BR": "Adicionar ao calendário",
        "ru_RU": "Добавить в календарь",
    },
    "iCal": {
        "cs_CZ": "iCal",
        "de_DE": "iCal",
        "en_US": "iCal",
        "es_ES": "iCal",
        "fr_FR": "iCal",
        "hu_HU": "iCal",
        "ja": "iCal",
        "nl_NL": "iCal",
        "pl_PL": "iCal",
        "pt_BR": "iCal",
        "ru_RU": "iCal",
    },
    "Dashboard Calendario": {
        "cs_CZ": "Dashboard kalendáře",
        "de_DE": "Kalender-Dashboard",
        "en_US": "Calendar Dashboard",
        "es_ES": "Panel del calendario",
        "fr_FR": "Tableau de bord du calendrier",
        "hu_HU": "Naptár vezérlőpult",
        "ja": "カレンダーダッシュボード",
        "nl_NL": "Kalenderdashboard",
        "pl_PL": "Panel kalendarza",
        "pt_BR": "Painel do calendário",
        "ru_RU": "Панель календаря",
    },
    "Dashboard": {
        "cs_CZ": "Dashboard",
        "de_DE": "Dashboard",
        "en_US": "Dashboard",
        "es_ES": "Panel",
        "fr_FR": "Tableau de bord",
        "hu_HU": "Vezérlőpult",
        "ja": "ダッシュボード",
        "nl_NL": "Dashboard",
        "pl_PL": "Panel",
        "pt_BR": "Painel",
        "ru_RU": "Панель",
    },
    "Titolo *": {
        "cs_CZ": "Název *",
        "de_DE": "Titel *",
        "en_US": "Title *",
        "es_ES": "Título *",
        "fr_FR": "Titre *",
        "hu_HU": "Cím *",
        "ja": "タイトル *",
        "nl_NL": "Titel *",
        "pl_PL": "Tytuł *",
        "pt_BR": "Título *",
        "ru_RU": "Заголовок *",
    },
    "Nome evento": {
        "cs_CZ": "Název události",
        "de_DE": "Name der Veranstaltung",
        "en_US": "Event name",
        "es_ES": "Nombre del evento",
        "fr_FR": "Nom de l'événement",
        "hu_HU": "Esemény neve",
        "ja": "イベント名",
        "nl_NL": "Naam evenement",
        "pl_PL": "Nazwa wydarzenia",
        "pt_BR": "Nome do evento",
        "ru_RU": "Название события",
    },
    "Data inizio *": {
        "cs_CZ": "Datum zahájení *",
        "de_DE": "Startdatum *",
        "en_US": "Start date *",
        "es_ES": "Fecha de inicio *",
        "fr_FR": "Date de début *",
        "hu_HU": "Kezdő dátum *",
        "ja": "開始日 *",
        "nl_NL": "Begindatum *",
        "pl_PL": "Data rozpoczęcia *",
        "pt_BR": "Data de início *",
        "ru_RU": "Дата начала *",
    },
    "Nessuna": {
        "cs_CZ": "Žádná",
        "de_DE": "Keine",
        "en_US": "None",
        "es_ES": "Ninguna",
        "fr_FR": "Aucune",
        "hu_HU": "Nincs",
        "ja": "なし",
        "nl_NL": "Geen",
        "pl_PL": "Brak",
        "pt_BR": "Nenhuma",
        "ru_RU": "Нет",
    },
    "Link opzionale": {
        "cs_CZ": "Volitelný odkaz",
        "de_DE": "Optionaler Link",
        "en_US": "Optional link",
        "es_ES": "Enlace opcional",
        "fr_FR": "Lien optionnel",
        "hu_HU": "Választható hivatkozás",
        "ja": "任意のリンク",
        "nl_NL": "Optionele link",
        "pl_PL": "Opcjonalny link",
        "pt_BR": "Link opcional",
        "ru_RU": "Необязательная ссылка",
    },
    "Auto": {
        "cs_CZ": "Auto",
        "de_DE": "Auto",
        "en_US": "Auto",
        "es_ES": "Auto",
        "fr_FR": "Auto",
        "hu_HU": "Automatikus",
        "ja": "自動",
        "nl_NL": "Auto",
        "pl_PL": "Auto",
        "pt_BR": "Automático",
        "ru_RU": "Авто",
    },
    "Descrizione visibile nella modale evento": {
        "cs_CZ": "Popis viditelný v modálním okně události",
        "de_DE": "Beschreibung, sichtbar im Veranstaltungs-Modal",
        "en_US": "Description visible in the event modal",
        "es_ES": "Descripción visible en el modal del evento",
        "fr_FR": "Description visible dans la modale de l'événement",
        "hu_HU": "Az esemény modálban megjelenő leírás",
        "ja": "イベントモーダルに表示される説明",
        "nl_NL": "Beschrijving zichtbaar in het evenement-modaal",
        "pl_PL": "Opis widoczny w oknie modalnym wydarzenia",
        "pt_BR": "Descrição visível no modal do evento",
        "ru_RU": "Описание, отображаемое в модальном окне события",
    },
    "Salva evento": {
        "cs_CZ": "Uložit událost",
        "de_DE": "Veranstaltung speichern",
        "en_US": "Save event",
        "es_ES": "Guardar evento",
        "fr_FR": "Enregistrer l'événement",
        "hu_HU": "Esemény mentése",
        "ja": "イベントを保存",
        "nl_NL": "Evenement opslaan",
        "pl_PL": "Zapisz wydarzenie",
        "pt_BR": "Salvar evento",
        "ru_RU": "Сохранить событие",
    },
}


def escape_po_string(s: str) -> str:
    """Escape per inserire stringa dentro msgstr ".." di un file .po."""
    # Backslash prima di tutto, poi virgolette
    s = s.replace("\\", "\\\\")
    s = s.replace('"', '\\"')
    s = s.replace("\n", "\\n")
    s = s.replace("\t", "\\t")
    return s


def unescape_po_string(s: str) -> str:
    """Inverso di escape_po_string."""
    # Ordine importante: prima \\n e \\t poi \\" infine \\\\
    s = s.replace('\\n', '\n')
    s = s.replace('\\t', '\t')
    s = s.replace('\\"', '"')
    s = s.replace('\\\\', '\\')
    return s


def parse_po_entries(lines):
    """
    Parsing semplificato: estrae (msgid_text, msgid_lineno, msgstr_lineno, msgstr_end_lineno).
    Supporta continuation multi-line (msgid "" seguito da più "..." righe).
    Ritorna lista di dict.
    """
    entries = []
    i = 0
    n = len(lines)
    while i < n:
        line = lines[i].rstrip("\n").rstrip("\r")
        if line.startswith('msgid '):
            # Parse msgid (può essere multi-line)
            msgid_start = i
            # Prima riga: msgid "text"
            m = re.match(r'^msgid\s+"(.*)"\s*$', line)
            if not m:
                i += 1
                continue
            msgid_parts = [m.group(1)]
            j = i + 1
            # Continua finché trovo righe "..."
            while j < n:
                ln = lines[j].rstrip("\n").rstrip("\r")
                m2 = re.match(r'^"(.*)"\s*$', ln)
                if m2:
                    msgid_parts.append(m2.group(1))
                    j += 1
                else:
                    break
            msgid_raw = "".join(msgid_parts)
            msgid_text = unescape_po_string(msgid_raw)

            # Ora deve seguire msgstr "..."
            if j >= n:
                break
            ln = lines[j].rstrip("\n").rstrip("\r")
            m3 = re.match(r'^msgstr\s+"(.*)"\s*$', ln)
            if not m3:
                # Non valido, skip
                i = j
                continue
            msgstr_start = j
            msgstr_parts = [m3.group(1)]
            k = j + 1
            while k < n:
                ln = lines[k].rstrip("\n").rstrip("\r")
                m4 = re.match(r'^"(.*)"\s*$', ln)
                if m4:
                    msgstr_parts.append(m4.group(1))
                    k += 1
                else:
                    break
            msgstr_raw = "".join(msgstr_parts)
            msgstr_text = unescape_po_string(msgstr_raw)

            entries.append({
                "msgid_text": msgid_text,
                "msgid_start": msgid_start,
                "msgid_end": j - 1,  # ultima riga del msgid (msgid o continuation)
                "msgstr_start": msgstr_start,
                "msgstr_end": k - 1,
                "msgstr_text": msgstr_text,
            })
            i = k
        else:
            i += 1
    return entries


def render_msgstr(text: str) -> list:
    """
    Rende una stringa come blocco di righe per il .po.
    Se contiene \n nel testo, usa il pattern multi-line gettext con msgstr "" iniziale.
    Altrimenti msgstr "text" su una riga.
    """
    if "\n" in text:
        # Multi-line: ogni newline diventa "\n" alla fine di una riga
        lines_out = ['msgstr ""\n']
        parts = text.split("\n")
        # Conserva i \n: tutti tranne l'ultimo hanno trailing \n
        for idx, p in enumerate(parts):
            if idx < len(parts) - 1:
                # ha trailing newline
                escaped = escape_po_string(p) + "\\n"
            else:
                # ultima parte: nessun trailing newline (a meno che il testo originale finisca con \n)
                escaped = escape_po_string(p)
            lines_out.append(f'"{escaped}"\n')
        # Se la stringa finisce con newline, l'ultima parte è "" e abbiamo aggiunto una riga vuota
        # con solo le virgolette: va bene
        return lines_out
    else:
        escaped = escape_po_string(text)
        return [f'msgstr "{escaped}"\n']


def fill_po_file(po_path: str, lang_code: str):
    """Riempie i msgstr vuoti di po_path con le traduzioni per lang_code."""
    with open(po_path, "r", encoding="utf-8") as f:
        lines = f.readlines()

    entries = parse_po_entries(lines)
    if not entries:
        print(f"  WARN: nessuna entry trovata in {po_path}")
        return 0

    # Costruiamo il nuovo file
    # Strategia: ricostruiamo riga per riga, sostituendo le righe del msgstr
    # quando il msgid corrisponde a uno tradotto e il msgstr corrente è vuoto.
    new_lines = []
    cursor = 0
    n = len(lines)
    # Map: msgstr_start -> (msgstr_end, replacement_lines or None)
    replacements = {}
    translated_count = 0
    for ent in entries:
        msgid = ent["msgid_text"]
        if msgid == "":
            # Header entry, skip (la trattiamo separatamente con header update)
            continue
        if msgid in TRANSLATIONS and lang_code in TRANSLATIONS[msgid]:
            translation = TRANSLATIONS[msgid][lang_code]
            # Solo se il msgstr corrente è vuoto (per non sovrascrivere traduzioni già fatte)
            if ent["msgstr_text"] == "":
                rep_lines = render_msgstr(translation)
                replacements[ent["msgstr_start"]] = (ent["msgstr_end"], rep_lines)
                translated_count += 1

    # Riscrittura
    i = 0
    while i < n:
        if i in replacements:
            end_idx, rep_lines = replacements[i]
            new_lines.extend(rep_lines)
            i = end_idx + 1
        else:
            new_lines.append(lines[i])
            i += 1

    # Aggiorna header: Last-Translator e PO-Revision-Date
    final_lines = []
    for line in new_lines:
        if line.startswith('"Last-Translator:'):
            final_lines.append('"Last-Translator: Claude (AI translation)\\n"\n')
        elif line.startswith('"PO-Revision-Date:'):
            final_lines.append('"PO-Revision-Date: 2026-05-21 12:00+0000\\n"\n')
        else:
            final_lines.append(line)

    with open(po_path, "w", encoding="utf-8", newline="\n") as f:
        f.writelines(final_lines)

    return translated_count


def main():
    if not os.path.isdir(LANG_DIR):
        print(f"ERROR: directory non trovata: {LANG_DIR}")
        sys.exit(1)

    total = 0
    for lang in LANGS:
        po_path = os.path.join(LANG_DIR, f"olo-calendar-{lang}.po")
        if not os.path.isfile(po_path):
            print(f"  SKIP: {po_path} non esiste")
            continue
        count = fill_po_file(po_path, lang)
        total += count
        print(f"  {lang}: {count} stringhe tradotte")
    print(f"\nTotale: {total} traduzioni inserite.")


if __name__ == "__main__":
    main()
