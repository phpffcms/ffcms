function translateText(lang_source, lang_target, source_text, api_key, blockname, ckeditor_instance, selectize_instance) {
    var yandex_languages = {
        'ua' : 'uk'
    };

    var lang_yandex_source = (typeof yandex_languages[lang_source] !== 'undefined') ? yandex_languages[lang_source] : lang_source;
    var lang_yandex_target = (typeof yandex_languages[lang_target] !== 'undefined') ? yandex_languages[lang_target] : lang_target;

    //$.getJSON('https://translate.yandex.net/api/v1.5/tr.json/translate?key='+api_key+'&text='+source_text+'&lang='+lang_yandex_source+'-'+lang_yandex_target+'&format=html&callback=?', function(result) {
    $.post('https://translate.yandex.net/api/v1.5/tr.json/translate', { key: api_key, text : source_text, lang : lang_yandex_source+'-'+lang_yandex_target, format : 'html'}, function(result) {
        if(selectize_instance) {
            var tag_array = result.text[0].split(',');
            for(var i = 0;i < tag_array.length;i++) {
                blockname.addOption({
                    text: tag_array[i],
                    value: tag_array[i]
                });
                blockname.addItem(tag_array[i]);
            }
        } else if(ckeditor_instance) {
            CKEDITOR.instances[blockname+lang_target].setData(result.text[0].replace('/'+lang_source+'/', '/' + lang_target + '/'));
        } else
            $('#'+blockname+lang_target).val(result.text[0]);
    });
}

function translateNews(lang_source, lang_target, api_key) {
    var title_source = $('#news_title_'+lang_source).val();
    var text_source = $('#textobject'+lang_source).val();
    if(text_source.length < 1)
        text_source = CKEDITOR.instances['textobject'+lang_source].getData();
    var desc_source = $('#news_desc_'+lang_source).val();
    var keywords_source = $('#keywords_'+lang_source).val();

    if(title_source.length > 0)
        translateText(lang_source, lang_target, title_source, api_key, 'news_title_', false);
    if(text_source.length > 0)
        translateText(lang_source, lang_target, text_source, api_key, 'textobject', true);
    if(desc_source.length > 0)
        translateText(lang_source, lang_target, desc_source, api_key, 'news_desc_', false);
    if(keywords_source.length > 0) {
        var c_selector = null;
        for(var s=0;s<Jobject.length;s++) {
            if(Jobject[s]['id'] == 'keywords_'+lang_target) {
                c_selector = Jobject[s].selectize;
                break;
            }
        }
        if(c_selector != null)
            translateText(lang_source, lang_target, keywords_source, api_key, c_selector, false, true);
    }
}

function translateStatic(lang_source, lang_target, api_key) {
    var title_source = $('#static_title_'+lang_source).val();
    var text_source = $('#textobject'+lang_source).val();
    if(text_source.length < 1)
        text_source = CKEDITOR.instances['textobject'+lang_source].getData();
    var desc_source = $('#static_desc_'+lang_source).val();
    var keywords_source = $('#keywords_'+lang_source).val();

    if(title_source.length > 0)
        translateText(lang_source, lang_target, title_source, api_key, 'static_title_', false);
    if(text_source.length > 0)
        translateText(lang_source, lang_target, text_source, api_key, 'textobject', true);
    if(desc_source.length > 0)
        translateText(lang_source, lang_target, desc_source, api_key, 'static_desc_', false);
    if(keywords_source.length > 0) {
        var c_selector = null;
        for(var s=0;s<Jobject.length;s++) {
            if(Jobject[s]['id'] == 'keywords_'+lang_target) {
                c_selector = Jobject[s].selectize;
                break;
            }
        }
        if(c_selector != null)
            translateText(lang_source, lang_target, keywords_source, api_key, c_selector, false, true);
    }
}