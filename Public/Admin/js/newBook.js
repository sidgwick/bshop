$('.more-input').click(
  function (){
      newNode = $(this).parents('.more').clone(true);
      newNode.find('input').val('');
      $(this).parents('.more').after(newNode);
      $(this).parents('.more').find('.more-area').remove();
  }
);
