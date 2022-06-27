/*!
 * Start Bootstrap - SB Admin v6.0.0 (https://startbootstrap.com/templates/sb-admin)
 * Copyright 2013-2020 Start Bootstrap
 * Licensed under MIT (https://github.com/BlackrockDigital/startbootstrap-sb-admin/blob/master/LICENSE)
 */
(function($) {
    "use strict";

    var dt = [];
    dt['ru'] = {
        "emptyTable": "Данные не обнаружены в таблице",
        "info": "Показаны элементы от _START_ до _END_ из _TOTAL_",
        "infoEmpty": "Показано 0 элементов",
        "infoFiltered": "(выбрано из _MAX_ всего)",
        "infoPostFix": "",
        "thousands": " ",
        "lengthMenu": "Показать _MENU_ записей",
        "loadingRecords": "Загрузка...",
        "processing": "Обработка...",
        "search": "Поиск:",
        "zeroRecords": "Совпадения не найдены",
        "paginate": {
            "first": "Начало",
            "last": "Конец",
            "next": "Следующая",
            "previous": "Предыдущая"
        },
        "aria": {
            "sortAscending": ": нажмите для сортировки по возрастанию",
            "sortDescending": ": нажмите для сортировки по убыванию"
        }
    }

    // Add active state to sidbar nav links
    var path = window.location.href; // because the 'href' property of the DOM element is the absolute path
    $("#layoutSidenav_nav .sb-sidenav a.nav-link").each(function() {
        if (this.href === path) {
            $(this).addClass("active");
        }
    });

    // Toggle the side navigation
    $("#sidebarToggle").on("click", function(e) {
        e.preventDefault();
        $("body").toggleClass("sb-sidenav-toggled");
    });

    $('.datatable').DataTable({
        language: dt[script_lang]
    });
})(jQuery);

$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-bs-toggle="tooltip"]').tooltip();
});