var translitMap = [];
translitMap["Ё"] = "YO";
translitMap["Й"] = "I";
translitMap["Ц"] = "TS";
translitMap["У"] = "U";
translitMap["К"] = "K";
translitMap["Е"] = "E";
translitMap["Н"] = "N";
translitMap["Г"] = "G";
translitMap["Ш"] = "SH";
translitMap["Щ"] = "SCH";
translitMap["З"] = "Z";
translitMap["Х"] = "H";
translitMap["Ъ"] = "";
translitMap["ё"] = "yo";
translitMap["й"] = "i";
translitMap["ц"] = "ts";
translitMap["у"] = "u";
translitMap["к"] = "k";
translitMap["е"] = "e";
translitMap["н"] = "n";
translitMap["г"] = "g";
translitMap["ш"] = "sh";
translitMap["щ"] = "sch";
translitMap["з"] = "z";
translitMap["х"] = "h";
translitMap["ъ"] = "";
translitMap["Ф"] = "F";
translitMap["Ы"] = "I";
translitMap["В"] = "V";
translitMap["А"] = "A";
translitMap["П"] = "P";
translitMap["Р"] = "R";
translitMap["О"] = "O";
translitMap["Л"] = "L";
translitMap["Д"] = "D";
translitMap["Ж"] = "ZH";
translitMap["Э"] = "E";
translitMap["ф"] = "f";
translitMap["ы"] = "i";
translitMap["в"] = "v";
translitMap["а"] = "a";
translitMap["п"] = "p";
translitMap["р"] = "r";
translitMap["о"] = "o";
translitMap["л"] = "l";
translitMap["д"] = "d";
translitMap["ж"] = "zh";
translitMap["э"] = "e";
translitMap["Я"] = "YA";
translitMap["Ч"] = "CH";
translitMap["С"] = "S";
translitMap["М"] = "M";
translitMap["И"] = "I";
translitMap["Т"] = "T";
translitMap["Ь"] = "";
translitMap["Б"] = "B";
translitMap["Ю"] = "YU";
translitMap["я"] = "ya";
translitMap["ч"] = "ch";
translitMap["с"] = "s";
translitMap["м"] = "m";
translitMap["и"] = "i";
translitMap["т"] = "t";
translitMap["ь"] = "";
translitMap["б"] = "b";
translitMap["ю"] = "yu";
translitMap[" "] = "-";
/**
 * Translit russian
 * @param data
 * @returns {*}
 */
function translit(data)
{
    return data.replace(/[^A-Za-z0-9\u0410-\u0451_ ]/g, '').replace(/([\u0410-\u0451 ])/g,
        function (str, p1, offset, s) {
            if (translitMap[str] != 'undefined') {
                return translitMap[str].toLowerCase();
            }
        }
    ).replace(/[A-Z]/g,
        function (resp) {
            return resp.toLowerCase();
        }
    );
}

// multi-level dropdown
$(function(){
    $(".dropdown-menu > li > a.trigger").on("click",function(e){
        var current=$(this).next();
        var grandparent=$(this).parent().parent();
        if($(this).hasClass('left-caret')||$(this).hasClass('right-caret'))
            $(this).toggleClass('right-caret left-caret');
        grandparent.find('.left-caret').not(this).toggleClass('right-caret left-caret');
        grandparent.find(".sub-menu:visible").not(current).hide();
        current.toggle();
        e.stopPropagation();
    });
    $(".dropdown-menu > li > a:not(.trigger)").on("click",function(){
        var root=$(this).closest('.dropdown');
        root.find('.left-caret').toggleClass('right-caret left-caret');
        root.find('.sub-menu:visible').hide();
    });
});

// checkbox change on table row click
$(function () {
    $('tr.checkbox-row').click(function(event) {
        if (event.target.type !== 'checkbox') {
            $(':checkbox', this).trigger('click');
        }
    });
});