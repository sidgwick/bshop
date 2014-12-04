$('a.addCart').click(
    function () {
        target = $(this).attr('addCartUrl');
        quantity = $('input[type="text"][name="quantity"]').val();
        target = target + '/quantity/' + quantity;
        $.getJSON(
            target,
            function (json){
                if (json.result == true) {
                    update_cart(json.cart);
                    //alert('添加成功');
                } else {
                    alert('添加失败: ' + json.msg);
                }
            }
        );
    }
);

function update_cart(json) {
    // 修改购物车按钮
    $('div#cart > div.cart-btn > a').text(json.total);
    // 修改购物车总数量
    $('div#cart > div.cart-list > div.cart-amount > span').text('购物车里有 ' + $(json.items).size() +' 件商品');
    // 购物车显示两个条目, 现在显示的不到两条,就增加显示.刚好有两条,
    // 就显示"移步购物车查看更多"
    lists = $('div#cart > div.cart-list > ul');
    tmp_li = $('<li><a class="to_detail"><img class="cover_img" /></a><a class="to_detail book_title"></a><span class="quantity_price"></span><div class="clearfix"></div></li>');
    // li = lists.find('li').slice(0, 1);
    lists.find('li').remove();
    for (i = 0; i < 2; i++) {
        if (item = json.items[i]) {
            li = tmp_li.clone();
            // 商品链接
            href = product_detail_url + '/' + item.id + '.html';
            li.find('a.to_detail').attr('href', href);
            // 商品名称
            li.find('a.book_title').text(item.title);
            // 图片地址
            src = product_image_url + '/' + item.id + '/main.jpg';
            li.find('img.cover_img').attr('src', src);
            // 价格, 数量
            li.find('span.quantity_price').text(item.quantity + " x ¥" + item.price);
            // 显示出来
            lists.append(li);
        }
    }
    if ($(json.items).size() > 2) {
        lists.append('<li>目前仅显示2件, 更多请到购物车查看</li>');
    }
}
