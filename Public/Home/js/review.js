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
            alert(json.result);
        }
    );
}
);
