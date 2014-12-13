$('span.rate span.star').click(
    function () {
    $(this).siblings().removeClass('rate-select-on');
    $(this).prevAll().andSelf().addClass('rate-select-on');

    rate = $(this).attr('rate');
    $('input[name=rate]').val(rate);
}
);

$('a#add-review-btn').click(
    function () {
    target_url = $(this).attr('review-url');

    bid = $('input[name=bid]').val();
    rate = $('input[name=rate]').val();
    content = $('textarea[name=content]').val();

    data = {};
    data.rate = rate;
    data.bid = bid;
    data.content = content;

    $.post(
        target_url,
        JSON.stringify(data),
        function (json) {
            if (json.result) {
                // 评论成功, 把新评论添加到评论列表开头
                load_review_list(review_url);
                now = $('span.review_count:first').text();
                $('span.review_count').text(parseInt(now) + 1);
                $('button.mfp-close').click();
            }
            alert(json.msg);
        }
    );
}
);

$(
    function () {
    if (review_count > 0) {
        load_review_list(review_url);
    }
}
);

function load_review_list (url) {
    $('section#review-list').load(
        url,
        function () {
            $('div.pagination-container nav ul li a').click(
                function () {
                t_url = $(this).attr('url_target');
                load_review_list($(this).attr('url_target'));
            }
            );
        }
    );
}
