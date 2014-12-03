$('.more-input').click(
  function (){
      newNode = $(this).parents('.more').clone(true);
      newNode.find('input').val('');
      $(this).parents('.more').after(newNode);
      $(this).parents('.more').find('.more-area').remove();
  }
);

$('a.delete_img').hover(
  function (){
    $(this).prev().addClass('cover-img-delete');
    $(this).prev().removeClass('cover');
  },
  function (){
    $(this).prev().removeClass('cover-img-delete');
    $(this).prev().addClass('cover');
  }
);

$('a.delete_img').click(
  function (){
    target = $(this).prev().attr('src');
    del_node = $(this).parent();
    
    $.post(
        delete_image_url,
        "target=" + target,
        function (json){
          if (json.result == true) {
            del_node.remove();
          } else {
            alert(json.msg);
          }
        }
    );
  }
);
