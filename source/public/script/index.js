$('.house').on('click', function(e) {
  var $house = $(e.target),
      name = $house.attr('id');
  $house.find('header').css({
    'writing-mode': 'rl',
  });
  $house.find('header h2').css({
    margin: '10px'
  });
  $house.find('.content').css({
    display: 'block'
  });
  $house.velocity({
    'flex-basis': '85%',
  });
  $('.house:not(#' + name + ') header').css({
    'writing-mode': 'tb-rl'
  });
  $('.house:not(#' + name + ') header h2').css({
    margin: '1px'
  });
  $('.house:not(#' + name + ') .content').css({
    display: 'none'
  });
  $('.house:not(#' + name + ')').velocity({
    'flex-basis': '5%'
  });
});