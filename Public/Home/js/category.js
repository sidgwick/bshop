// Categories

function addLevelClass($parent, level) {
    $parent.addClass('parent-'+level);
    var $children = $parent.children('li');
    $children.addClass('child-'+level).data('level',level);
    $children.each(function() {
        var $sublist = $(this).children('ul');
        if ($sublist.length > 0) {
            $(this).addClass('has-sublist');
            addLevelClass($sublist, level+1);
        }
    });
}

addLevelClass($('#categories'), 1);

//----------------------------------------//
$('#categories > li a').click(function(e){
    e.preventDefault();

    if ($(this).attr('class') != 'active'){
        $(this).parent().siblings().find('ul').slideUp();
        $(this).next().slideToggle();
        $(this).parent().siblings().find('a').removeClass('active');
        $(this).addClass('active');

        update_display_list($(this).attr('href'));
    } else {
        $(this).next().slideToggle();
        $(this).parent().find('ul').slideUp();
        var curlvl = $(this).parent().data('level');
        if(curlvl){
            $('#categories li.child-'+curlvl+' a').removeClass('active');
        }
    }
});



// Filter by Price
//----------------------------------------//

$("#slider-range").slider({
    range: true,
    min: 0,
    max: 200,
    values: [ 0, 200 ],
    slide: function(event, ui) {
        event = event;
        $("#amount").val("$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ]);
    }
});
$("#amount").val("$" + $("#slider-range").slider("values", 0) + " - $" + $("#slider-range").slider("values", 1));

// 价格筛选按钮
$('a#price_filter').click(
    function () {
    update_display_list();
}
);

// 排序切换
$('select#orderby').change(
    function () {
    update_display_list();
}
);

$().ready(
    function () {
    // 页面加载完毕, 执行依次AJAX, 拉取数据
    update_display_list();
}
);

function get_parameter(s, name) {
    pattern = "\\/" + name + "\\/\\d*\\/?" 
    if (tmp = s.match(pattern)) {
        tmp = tmp[0].substr(2 + name.length);
        if (tmp.indexOf('/') != -1) {
            tmp = tmp.substr(0, tmp.length - 1);
        }
    }
    
    if (!tmp) {
        tmp = 0;
    }

    return tmp;
}

function update_display_list(url) {
    if (url) {
        // 目标分类ID
        cid = get_parameter(url, 'id');
        page = get_parameter(url, 'p');

    } else {
        page = 0;
    }
    

    // 目标价格区间
    price_low = $("#slider-range").slider("values", 0);
    price_high = $("#slider-range").slider("values", 1);
    // 目标排序方式
    order = $('select#orderby').val();

    url = init_url;
    url = url + "/id/" + cid + "/pl/" + price_low + "/ph/" + price_high + "/order/" + order + "/p/" + page;

    $('div#products_list').load(
        url,
        function () {
            // 挂钩事件处理
            // 加入购物车事件
            $('div.category-list-items').find('a.addCart').click(
                function () {
                addCart(this);

            }
            );

            // 翻页事件
            $('div.pagination-container nav ul li a').click(
                function () {
                t_url = $(this).attr('url_target');
                update_display_list($(this).attr('url_target'));
                scroll_to = $('div#products_list').offset().top;
                $('html, body').animate({scrollTop:scroll_to}, 500);
            }
            );
        }
    );
}

