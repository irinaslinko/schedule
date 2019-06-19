function tableSearch(phrase, table, select = false) {
    if(!select) {
        var regPhrase = new RegExp(phrase.value, 'i');
    }else {
        var regPhrase = new RegExp(phrase, 'i');
    }
    var flag = false;
    for (var i = 1; i < table.rows.length; i++) {
        flag = false;
        for (var j = table.rows[i].cells.length - 1; j >= 0; j--) {
            flag = regPhrase.test(table.rows[i].cells[j].innerHTML);
            if (flag) break;
        }
        if (flag) {
            table.rows[i].style.display = "";
        } else {
            table.rows[i].style.display = "none";
        }
    }
}

function tableDoctorSearch() {
    var phrase = document.getElementById('search-list');
    var table = document.getElementById('doctor-list');
    tableSearch(phrase,table);
}

$(document).ready(function() {
    $('#city-search-list').on('keyup', function(){
        $('#lpu-city-select').siblings().find('span.filter-option').text('Поиск по медицинским учреждениям');
        $('#lpu-city-select').prop('selectedIndex', 0);
        $('#lpu-city-select').siblings().find('li.selected').removeClass();
        $('#lpu-city-select').siblings().find('[data-original-index="0"]').attr('class', 'selected');

        var phrase = document.getElementById('city-search-list');
        var table = document.getElementById('city-lpu');
        tableSearch(phrase, table);
    });

    $('#area-search-list').on('keyup', function(){
        $('#lpu-area-select').siblings().find('span.filter-option').text('Поиск по медицинским учреждениям');
        $('#lpu-area-select').prop('selectedIndex', 0);
        $('#lpu-area-select').siblings().find('li.selected').removeClass();
        $('#lpu-area-select').siblings().find('[data-original-index="0"]').attr('class', 'selected');

        var phrase = document.getElementById('area-search-list');
        var table = document.getElementById('area-lpu');
        tableSearch(phrase, table);
    });

    $('.doctor-select').change(function(){
        document.location.href = $(this).val();
    });

    $('#lpu-city-select').change(function(){
    document.getElementById('city-search-list').value = '';
    var table = document.getElementById('city-lpu');
    if($(this).val() == 0){
        var phrase = '';
    }else {
        var phrase = $(this).find('option:selected').text();
    }
    tableSearch(phrase, table, true);
    });

    $('#lpu-area-select').change(function(){
        document.getElementById('area-search-list').value = '';
        var table = document.getElementById('area-lpu');
        if($(this).val() == 0){
            var phrase = '';
        }else {
            var phrase = $(this).find('option:selected').text();
        }
        tableSearch(phrase, table, true);
    });

    var input_class = 'clearable',
    input_class_x = input_class + '__x',
    input_class_x_over = input_class + '__x_over',
    input_selector = '.' + input_class,
    input_selector_x = '.' + input_class_x,
    input_selector_x_over = '.' + input_class_x_over,
    event_main = input_class + '-init',
    event_names = [event_main, 'focus drop paste keydown keypress input change'].join(' '),
    btn_width = 24,
    btn_height = 24,
    btn_margin = 13;

    function tog(v) {
        return v ? 'addClass' : 'removeClass';
    }
	
    $(document).on(event_names, input_selector, function () {
        $(this)[tog(this.value)](input_class_x);
    });
	
    $(document).on('mousemove', input_selector_x, function (e) {
        var input = $(this),
        input_width = this.offsetWidth,
        input_height = this.offsetHeight,
        input_border_bottom = parseFloat(input.css('borderBottomWidth')),
        input_border_right = parseFloat(input.css('borderRightWidth')),
        input_border_left = parseFloat(input.css('borderLeftWidth')),
        input_border_top = parseFloat(input.css('borderTopWidth')),
        input_border_hr = input_border_left + input_border_right,
        input_border_vr = input_border_top + input_border_bottom,
        client_rect = this.getBoundingClientRect(),
        input_cursor_pos_x = e.clientX - client_rect.left,
        input_cursor_pos_y = e.clientY - client_rect.top,
        is_over_cross = true;

        is_over_cross = is_over_cross && (input_cursor_pos_x >= input_width - input_border_hr - btn_margin - btn_width);
        is_over_cross = is_over_cross && (input_cursor_pos_x <= input_width - input_border_hr - btn_margin);
        is_over_cross = is_over_cross && (input_cursor_pos_y >= (input_height - input_border_vr - btn_height) / 2);
        is_over_cross = is_over_cross && (input_cursor_pos_y <= (input_height - input_border_vr - btn_height) / 2 + btn_height);

        $(this)[tog(is_over_cross)](input_class_x_over);
    });
	
    $(document).on('click', input_selector_x_over, function () {
        $(this).removeClass([input_class_x, input_class_x_over].join(' ')).val('').trigger('input');

        if($(this).attr("id") == "city-search-list") {
            var table_id = 'city-lpu';
        }else if($(this).attr("id") == "area-search-list") {
            var table_id = 'area-lpu';
        }else if($(this).attr("id") == "search-list") {
            var table_id = 'doctor-list';
        }

        var table = document.getElementById(table_id);
        tableSearch("", table);
    });

    $(function () {
        $(input_selector).trigger(event_main);
    });
});