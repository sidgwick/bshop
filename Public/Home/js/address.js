$('#address select').change(
  function (){
    // 得到上级地址
    value = this.value;
    id = this.id;
    if (id == "province") {
      target = $('#city');
      greet = "请选择市";
    } else if (id == "city") {
      greet = "请选择县/区";
      target = $('#country');
    } else {
      target = false;
    };
    // 接下来AJAX请求获得后面的下级地址
    $.getJSON(
      subregion + "&id=" + value,
      function (sr) {
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
    );
  }
);

// 刷新后, 不能干把把的等着用户在此点击省份, 这里住动情求一次
$(
  function (){
    // 得到上级地址
    province = $('#province');
    value = province.val();
    if (value == "") {
        return false;
    }
    id = province.attr('id');
    if (id == "province") {
      target = $('#city');
      greet = "请选择市";
    } else if (id == "city") {
      greet = "请选择县/区";
      target = $('#country');
    } else {
      target = false;
    };
    // 接下来AJAX请求获得后面的下级地址
    $.getJSON(
      subregion + "&id=" + value,
      function (sr) {
        if (target) {
          // 清空老地址
          target.html('<option value="">' + greet + "</option>");
          $(sr).each(
            function () {
                target.append("<option value='" + this.id + "'>" + this.name + "</option>");
            }
          );
        }
      }
    );
  }
);

