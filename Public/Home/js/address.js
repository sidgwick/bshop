$('select.address-select-input').change(
    function () {
    region = {};
    region.id = $(this).attr('id');
    region.value = $(this).val();
    get_subregion(region);
}
);

// 刷新后, 不能干把把的等着用户在此点击省份, 这里住动情求一次
$().ready(
    function (){
    // 得到上级地址
    province = $('#province');

    if (province.val() == "") {
        return false;
    } else {
        region = {};
        region.id = "province";
        region.value = province.val();
        get_subregion(region);
    }
}
);

function get_subregion(region){
    // 得到上级地址
    value = region.value;
    id = region.id;
    if (id == "province") {
        target = $('#city');
        // 省份都重新选择了, 县区要清零
        $('#country').html('<option value="">请选择县/区</option>');
        greet = "请选择市/区";
    } else if (id == "city") {
        greet = "请选择县/区";
        target = $('#country');
    } else {
        target = false;
    };
    // 接下来AJAX请求获得后面的下级地址
    $.ajax({
        type: "get",
        async: false,
        url: subregion + "/id/" + value,
        contentType: "application/x-www-form-urlencoded; charset=utf-8",
        dataType: "json",
        cache: false,
        success: function (sr) {
            //返回的数据用data.d获取内容
            if (target) {
                if (!sr) {
                    target.html('<option value="-1">无子级地址</option>');
                } else {
                    // 清空老地址
                    target.html('<option value="">' + greet + "</option>");
                    $(sr).each(
                        function () {
                        target.append("<option value='" + this.id + "'>" + this.name + "</option>");
                    }
                    );
                }
            }
        }
    });
}
