'use strict';

$('.house').on('click', function(e) {
  var $house = $(e.target);
  while (! $house.hasClass('house')) {
    $house = $house.parent();
  }
  var name = $house.attr('id');
  $house.find('header').css({
    'writing-mode': 'rl',
  });
  $house.find('header h2').css({
    margin: '10px'
  });
  $house.find('.sigil img').css({
    width: '200px',
    height: '200px'
  });
  $house.find('.description').css({
    display: 'block'
  });
  $house.find('.button').css({
    display: 'block'
  });
  $('.house:not(#' + name + ') header').css({
    'writing-mode': 'tb-rl'
  });
  $('.house:not(#' + name + ') header h2').css({
    margin: '1px'
  });
  $('.house:not(#' + name + ') .sigil img').css({
    width: '48px',
    height: '48px'
  });
  $('.house:not(#' + name + ') .description').css({
    display: 'none'
  });
  $('.house:not(#' + name + ') .button').css({
    display: 'none'
  });
  $('.house:not(#' + name + ')').velocity({
    'flex-basis': '5%'
  });
  $house.velocity({
    'flex-basis': '85%',
  });
});
