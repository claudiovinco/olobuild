/**
 * Olo Calendar — Admin Event Meta Box
 */
(function () {
    'use strict';

    var allDayCheckbox = document.getElementById('olo_all_day');
    if (!allDayCheckbox) return;

    var timeFields = document.querySelectorAll('.olo-em-time');

    function toggleTimeFields() {
        var disabled = allDayCheckbox.checked;
        timeFields.forEach(function (el) {
            el.style.opacity = disabled ? '0.3' : '1';
            el.style.pointerEvents = disabled ? 'none' : '';
        });
    }

    allDayCheckbox.addEventListener('change', toggleTimeFields);

    // Auto-fill end date when start date changes
    var startDate = document.getElementById('olo_start_date');
    var endDate = document.getElementById('olo_end_date');
    if (startDate && endDate) {
        startDate.addEventListener('change', function () {
            if (!endDate.value || endDate.value < startDate.value) {
                endDate.value = startDate.value;
            }
        });
    }
})();
