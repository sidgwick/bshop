$('a.to-next-step').click(
    function () {
    step = $(this).attr('this-step');
    next = String(parseInt(step) + 1);

    $('#active-step-' + step).slideUp();
    $('#result-step-' + step).slideDown();
    $('#active-step-' + next).slideDown();

    update_display_data(step);
}
);

$('strong.edit-btn').click(
    function () {
    edit_step = $(this).parent().find('span').text();

    for (i = 1; i <= 3; i++) {
        if (i < parseInt(edit_step)) {
            $('#active-step-' + i).slideUp();
            $('#result-step-' + i).slideDown();
        } else if (i == parseInt(edit_step)) {
            $('#result-step-' + i).slideUp();
            $('#active-step-' + i).slideDown();
        } else {
            $('#active-step-' + i).slideUp();
            $('#result-step-' + i).slideUp();
        }
    }
}
);

$('input.address-selector').click(
    function () {
    // 选择已保存的地址, 自动填充表单
    // 省
    province_list = $('select#province > option');
    province = $(this).parent().find('label.address-selector-label > span.province').text().trim();
    province_list.each(
        function () {
        if ($(this).text().trim() == province) {
            region = {};
            region.id = "province";
            region.value = $(this).val();
            $(this).attr('selected', true);
            get_subregion(region);
        }
    }
    );

    // 市
    city_list = $('select#city > option');
    city = $(this).parent().find('label.address-selector-label > span.city').text().trim();
    city_list.each(
        function () {
        if ($(this).text().trim() == city) {
            region = {};
            region.id = "city";
            region.value = $(this).val();
            $(this).attr('selected', true);
            get_subregion(region);
            return false;
        }
    }
    );
    
    // 区/县
    country = $(this).parent().find('label.address-selector-label > span.country').text().trim();
    if (country) {
        country_list = $('select#country > option');
        country_list.each(
            function () {
            if ($(this).text().trim() == country) {
                $(this).attr('selected', true);
                return false;
            }
        }
        );
    }

    // 街道地址
    street = $(this).parent().find('label.address-selector-label > span.street').text().trim();
    $('input[name=street]#street').val(street);
    
    // 邮编
    zipcode = $(this).parent().find('label.address-selector-label > span.zipcode').text().trim();
    $('input[name=zipcode]#zipcode').val(zipcode);
    
    // 收件人
    receiver = $(this).parent().find('label.address-selector-label > span.receiver').text().trim();
    $('input[name=receiver]#receiver').val(receiver);
    
    // 手机号码
    mobile = $(this).parent().find('label.address-selector-label > span.mobile').text().trim();
    $('input[name=mobile]#mobile').val(mobile);
}
);

function update_display_data(step) {
    if (step == 1) {
        // 收件人
        receiver = $('input[name=receiver]#receiver').val().trim();
        $('dl.delivery-address > dd.receiver').text(receiver);
        
        // 电子邮件
        // email = $('input[name=email]#email').val().trim();
        // $('dl.delivery-address > dd.email').text(email);

        // 联系电话
        mobile = $('input[name=mobile]#mobile').val().trim();
        $('dl.delivery-address > dd.mobile').text(mobile);

        // 地址信息
        province = $('select#province > option:selected').text().trim();
        city = $('select#city > option:selected').text().trim();
        country_val = $('select#country').val();
        if (country_val == -1) {
            country = "";
        } else {
            country = $('select#country > option:selected').text().trim();
        }
        street = $('input[name=street]#street').val().trim();
        address = province + " " + city + " " + country + " " + street;
        $('dl.delivery-address > dd.address').text(address);

        // 邮政编码
        zipcode = $('input[name=zipcode]#zipcode').val().trim();
        $('dl.delivery-address > dd.zipcode').text(zipcode);

    }

    if (step == 2) {
        // 第二步是选择邮递方式
        delevery = $('input[type=radio][name=shipping-address]:checked');
        $("p#delevery-method").html(delevery.parent().find('label').html());
    }
}
