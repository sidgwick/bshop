$('.more-input').click(
  function (){
      newNode = $(this).parents('.more').clone();
      newNode.find('.more-area').remove();
      $(this).parents('.more').after(newNode);
  }
);
