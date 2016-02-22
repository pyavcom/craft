/**
* Copyright (c) <2012>, <Rugento.ru>
* ЭТА ПРОГРАММА ПРЕДОСТАВЛЕНА ВЛАДЕЛЬЦАМИ АВТОРСКИХ ПРАВ И/ИЛИ ДРУГИМИ
* СТОРОНАМИ "КАК ОНА ЕСТЬ" БЕЗ КАКОГО-ЛИБО ВИДА ГАРАНТИЙ, ВЫРАЖЕННЫХ ЯВНО
* ИЛИ ПОДРАЗУМЕВАЕМЫХ, ВКЛЮЧАЯ, НО НЕ ОГРАНИЧИВАЯСЬ ИМИ, ПОДРАЗУМЕВАЕМЫЕ
* ГАРАНТИИ КОММЕРЧЕСКОЙ ЦЕННОСТИ И ПРИГОДНОСТИ ДЛЯ КОНКРЕТНОЙ ЦЕЛИ. НИ В
* КОЕМ СЛУЧАЕ, ЕСЛИ НЕ ТРЕБУЕТСЯ СООТВЕТСТВУЮЩИМ ЗАКОНОМ, ИЛИ НЕ УСТАНОВЛЕНО
* В УСТНОЙ ФОРМЕ, НИ ОДИН ВЛАДЕЛЕЦ АВТОРСКИХ ПРАВ И НИ ОДНО  ДРУГОЕ ЛИЦО,
* КОТОРОЕ МОЖЕТ ИЗМЕНЯТЬ И/ИЛИ ПОВТОРНО РАСПРОСТРАНЯТЬ ПРОГРАММУ, КАК БЫЛО
* СКАЗАНО ВЫШЕ, НЕ НЕСЁТ ОТВЕТСТВЕННОСТИ, ВКЛЮЧАЯ ЛЮБЫЕ ОБЩИЕ, СЛУЧАЙНЫЕ,
* СПЕЦИАЛЬНЫЕ ИЛИ ПОСЛЕДОВАВШИЕ УБЫТКИ, ВСЛЕДСТВИЕ ИСПОЛЬЗОВАНИЯ ИЛИ
* НЕВОЗМОЖНОСТИ ИСПОЛЬЗОВАНИЯ ПРОГРАММЫ (ВКЛЮЧАЯ, НО НЕ ОГРАНИЧИВАЯСЬ
* ПОТЕРЕЙ ДАННЫХ, ИЛИ ДАННЫМИ, СТАВШИМИ НЕПРАВИЛЬНЫМИ, ИЛИ ПОТЕРЯМИ
* ПРИНЕСЕННЫМИ ИЗ-ЗА ВАС ИЛИ ТРЕТЬИХ ЛИЦ, ИЛИ ОТКАЗОМ ПРОГРАММЫ РАБОТАТЬ
* СОВМЕСТНО С ДРУГИМИ ПРОГРАММАМИ), ДАЖЕ ЕСЛИ ТАКОЙ ВЛАДЕЛЕЦ ИЛИ ДРУГОЕ
* ЛИЦО БЫЛИ ИЗВЕЩЕНЫ О ВОЗМОЖНОСТИ ТАКИХ УБЫТКОВ.
*/

var timerId;
var j_ready_safe_load_filter = true;
var changePrice = false;

jQuery(document).ready(function(){

    if(j_ready_safe_load_filter)
    {
        jQuery("div.advanced-navigation span.filter-hidden-value").bind("click", function() {
            jQuery("#"+jQuery(this).attr('id')+"-value").toggleClass("af-arrow-ud");
            jQuery("#"+jQuery(this).attr('id')+"-value ol").slideToggle('fast');
        });

        jQuery("div.advanced-navigation span.filter-hidden-value-arrow").bind("click", function() {
            jQuery(this).parent().parent().toggleClass("af-arrow-ud");
            jQuery(this).parent().parent().find('.a-fsliderclass').slideToggle('fast', function(){
                var olidchange = jQuery(this).parent().parent()/*.find('ol')*/.attr('id');
                    if(jQuery('#show-count-'+ olidchange).length)
                    {
                        if(jQuery('#show-count-'+ olidchange + '-value').is(":visible"))
                        {
                            jQuery('#show-count-'+ olidchange).html(layhide);
                        }
                        else {
                            jQuery('#show-count-'+ olidchange).html(layshow);
                        }
                    }
            });
        });

        jQuery("div.advanced-navigation span.show-count-filter").bind("click", function() {
            var filtervisible = this;
            jQuery("#"+jQuery(this).attr('id')+"-value").slideToggle("fast", function() {
                jQuery("#"+jQuery(filtervisible).attr('id')+"-value").parent().parent().parent().toggleClass("af-arrow-ud");
                if(jQuery(this).is(":visible"))
                    {
                        jQuery(filtervisible).html(layhide);
                    }
                    else {
                        jQuery(filtervisible).html(layshow);
                    }
            });
        });

        jQuery("div.advanced-navigation .nav-checkbox").bind("change", function(event){
            var pos = jQuery(this).position();
            getFilterData(pos.top, pos.left);
        });

        jQuery("#nav-link").bind("mouseover", function(event){
            clearTimeout(timerId);
        });

        jQuery("#nav-link").bind("mouseout", function(event){
            hideNavLink();
        });

        jQuery("#af-showinstock").bind("click", function(event) {
            jQuery("#instock-input").attr('checked', 'checked');
            var pos = jQuery(this).position();
            getFilterData(pos.top, pos.left);
            jQuery('#af-showinstock').addClass('af-stock-enabled');
            jQuery('#af-showallstock').removeClass('af-stock-enabled');
        });

        jQuery("#af-showallstock").bind("click", function(event) {
            jQuery("#instock-input").removeAttr('checked');
            var pos = jQuery(this).position();
            getFilterData(pos.top, pos.left);
            jQuery('#af-showinstock').removeClass('af-stock-enabled');
            jQuery('#af-showallstock').addClass('af-stock-enabled');
        });

        readyStock ();
        jQuery("span.filter-note").tooltip({ position: "top left"});

        j_ready_safe_load = false;
    }
});

function readyStock ()
{
    if(jQuery('#instock-input').is(":checked"))
        {
            jQuery('#af-showinstock').addClass('af-stock-enabled');
        } else {
            jQuery('#af-showallstock').addClass('af-stock-enabled');
        }
}

function setChangePrice()
{
    changePrice = true;
}

function getFilterData(x,y)
{
    //остановка предыдущего таймера
    clearTimeout(timerId);

    //выключаем все, чтобы не ползали))
    jQuery("div.advanced-navigation .nav-slider").slider( "option", "disabled", true );
    jQuery("div.advanced-navigation .input-text").attr("disabled", "disabled");
    jQuery("div.advanced-navigation .nav-checkbox").attr("disabled", "disabled");

    var filterData = new Object(); //объект с параметрами запроса

    if(changePrice)
        {
            filterData.price = jQuery("#nav-slider-price-from").val() + "," + jQuery("#nav-slider-price-to").val();
        } else {
            var _currentPriceFromUrl = decodeURI(parseGetParams().price);
            if(_currentPriceFromUrl != 'undefined')
                {
                    filterData.price = _currentPriceFromUrl;
                }
        }

    //получаем чекбоксы
    jQuery("div.advanced-navigation ol.attribute-filter-values").each(function(){

        var f_id = jQuery(this).attr("id");
        var f_values = '';

        jQuery("#" + f_id + " .nav-checkbox:checked").each(function(){
            f_values = f_values + jQuery(this).val() + ',';
        });

        if(f_values.length)
        {
            filterData[f_id] = f_values;
        }
        });

    //получаем decimail
    jQuery("div.advanced-navigation ol.decimal-filter-values").each(function(){

        var f_id = jQuery(this).attr("id");
        var f_values = '';

        if(jQuery("#" + f_id + " input.nav-decimal-from").val().length || jQuery("#" + f_id + " input.nav-decimal-to").val().length)
        {
            f_values = jQuery("#" + f_id + " input.nav-decimal-from").val() + ',' + jQuery("#" + f_id + " input.nav-decimal-to").val();
            filterData[f_id] = f_values;
        }

        });
    //получаем сток
    if(jQuery("#instock-input").is(":checked"))
    {
        filterData.instock = 1;
    }

    filterData.cat = jQuery('#currentCategoryId').val();

    //если поиск пишем q
    if(decodeURI(parseGetParams().q) != 'undefined')
        {
            filterData.q = decodeURI(parseGetParams().q);
        }

    jQuery.getJSON(
                    layerurl,
                    filterData,
                    function(json) {
                        //разбираем ответ
                        parseJsonData(json);
                    }
            )
            .complete(function() {

                if(reload == '1')
                {
                    window.location.href = jQuery(".nav-bottom-link").attr('href');
                    return ;
                }

                jQuery("div.advanced-navigation .nav-slider").slider( "option", "disabled", false );
                jQuery("div.advanced-navigation .input-text").removeAttr("disabled");
                jQuery("div.advanced-navigation .nav-checkbox").removeAttr("disabled");
                showNavLink(x,y);
                hideNavLink();
                });
};

//парсим ответ сервера
function parseJsonData(json)
{
    jQuery("div.advanced-navigation .attribute-filter-values label").removeClass("filter-disable"); //удаляем не акт. классы
    for(var p in json) {

        //размер коллекции
        if(p == 'collection_size' || p == 'browser_url')
        {
            if(p == 'collection_size')
            {
                jQuery("#nav-link-count").text(json[p]);
                jQuery('#af-plural').text(GetNoun(json[p], afone, aftwo, affri));
            } else {
                jQuery("div.advanced-navigation .nav-a-link").attr("href", json[p]);
            }
        } else {
            var val_enabled = json[p];
            for(var i=0; i<val_enabled.length; i++) {
                //ставим стиль не активность
                jQuery("label[for='" +p + '-' + val_enabled[i] +"']").addClass("filter-disable");
                }
        }
    }
}

function showNavLink(x,y)
{
    jQuery("#nav-link").css({"top":x, "left":-xleft}).show();
}
function hideNavLink()
{
    var idTimer = setTimeout(function(){
        jQuery("#nav-link").hide();
        clearTimeout(idTimer);
        jQuery("#nav-link-count").text('');
//		jQuery("#nav-a-link").attr('href', '#');

        if(reload == '2')
        {
            window.location.href = jQuery(".nav-bottom-link").attr('href');
            return ;
        }

        }, timeoutlink);
    timerId = idTimer;
}

/****************Plural******************************/
var GetNoun = function(number, one, two, five) {
    number = Math.abs(number);
    number %= 100;
    if (number >= 5 && number <= 20) {
        return five;
    }
    number %= 10;
    if (number == 1) {
        return one;
    }
    if (number >= 2 && number <= 4) {
        return two;
    }
    return five;
}
/****************Parse Url**************************************/
function parseGetParams() {
       var $_GET = {};
       var __GET = window.location.search.substring(1).split("&");
       for(var i=0; i<__GET.length; i++) {
          var getVar = __GET[i].split("=");
          $_GET[getVar[0]] = typeof(getVar[1])=="undefined" ? "" : getVar[1];
       }
       return $_GET;
    }