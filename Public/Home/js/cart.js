// 更新购物车按钮
$('a#update-cart-btn').click(
    function () {
    target = $(this).attr('update-url');
    items = $('td.quantity-id-area');

    data = [];
    items.each(
        function () {
        quantity = $(this).find('input[name=quantity]').val();
        id = $(this).find('input[name=id]').val();
        item = {};
        item.id = id;
        item.quantity = quantity;
        data.push(item);
    }
    );
    $.post(
        target,
        JSON.stringify(data),
        function (json) {
            update_cart(json);
            update_cart_main(json);
        }
    );
}
);

// Product Quantity
var thisrowfield;
$('.qtyplus').click(function(e){
    e.preventDefault();
    thisrowfield = $(this).parent().find('.qty');

    var currentVal = parseInt(thisrowfield.val());
    if (!isNaN(currentVal)) {
        thisrowfield.val(currentVal + 1);
        update_display_price(thisrowfield);
    } else {
        thisrowfield.val(0);
    }
});

$(".qtyminus").click(function(e) {
    e.preventDefault();
    thisrowfield = $(this).parent().find('.qty');
    
    var currentVal = parseInt(thisrowfield.val());
    if (!isNaN(currentVal) && currentVal > 0) {
        thisrowfield.val(currentVal - 1);
        update_display_price(thisrowfield);
    } else {
        thisrowfield.val(0);
    }
});

// 加入购物车按钮
$('a.addCart').click(
    function () {
        target = $(this).attr('addCartUrl');
        if (!(quantity = $(this).attr('quantity'))) {
            quantity = $('input[type="text"][name="quantity"]').val();
        }
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

$('td a.cart-remove').click(
    function () {
    quantity = $(this).parent().parent().find('td.quantity-id-area > input[name=quantity]');
    quantity.val(0);
    update_display_price(quantity);
    $(this).parent().parent().remove();
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

function update_cart_main(json) {
    ht = $('tr.signle-book:first').clone(true);

    // 删除老的数据
    $('tr.signle-book').remove();

    // 补上新的数据
    $(json.items).each(
        function () {
        html = ht.clone(true);

        // 图片
        img = html.find('td.item-img > img');
        src = img.attr('purl') + this.id + "/main.jpg";
        img.attr('src', src);

        // 标题链接
        a = html.find('td.cart-title > a');
        href = a.attr('purl') + "/bid/" + this.id + ".html";
        a.attr('href', href);
        a.text(this.title);

        // 单价
        html.find('td.signle-price').text('¥' + this.price);

        // 数量和隐藏的ID
        html.find('td.quantity-id-area input[name=quantity]').val(this.quantity);
        html.find('td.quantity-id-area input[name=id]').val(this.id);

        // 小件合计
        item_total = parseFloat(this.price) * parseFloat(this.quantity);
        html.find('td.cart-total').text("¥" + parseFloat(item_total).toFixed(2));


        $('table#cart-display-table').append(html);
    }
    );

    // 订单总计
    $('strong#amount').text('¥' + parseFloat(json.total).toFixed(2));
    $('td#cal-ship-fee').text('¥0.00');
    $('strong#order-total').text('¥' + parseFloat(json.total).toFixed(2));
}

/*
 * obj 是Jquery包装好的(数量)对象
 */
function update_display_price(obj) {
    var quantity = parseInt(obj.val());
    var item_price_obj = obj.parent().parent().find('td.signle-price');
    var item_total_obj = obj.parent().parent().find('td.cart-total');
    var item_price = parseFloat(item_price_obj.text().substr(1));
    var old_item_total = parseFloat(item_total_obj.text().substr(1));
    var total_obj = $('strong#amount');
    var order_total_obj = $('strong#order-total');
    var total = parseFloat(total_obj.text().substr(1));
    var order_total = parseFloat(order_total_obj.text().substr(1));
    
    // item_total = parseFloat(item_total) - parseFloat(item_price);
    // 现在计算单件小结
    item_total = item_price * quantity;
    item_total_obj.text('¥' + item_total.toFixed(2));

    // 订单总计
    // 首先算出来单件"差价", 之后再计算总价
    diff = item_total - old_item_total;
    total = total + diff;
    total_obj.text('¥' + total.toFixed(2));
    order_total = order_total + diff;
    order_total_obj.text('¥' + order_total.toFixed(2));
}
