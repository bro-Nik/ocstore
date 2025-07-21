$(document).ready(function() {
  // Reorderable drag-and-drop lists
  $('tbody.sorting .input-group-btn').prepend('<span data-toggle="tooltip" title="" class="btn btn-success btn-sm handle"><i class="fa fa-hand-grab-o"></i></span>');
  console.log($('tbody.sorting .input-group-btn')); // Проверка

  $('table tbody').sortable({
    handle: '.handle',
    chosenClass: 'handle-active',
    onEnd: function (evt) {
      var orderIndex = 1;
      $($(evt.item).parent().find('input[name*="sort_order"]')).each(function() {
        $(this).val(orderIndex);
        orderIndex++;
      });
    }
  });
});

