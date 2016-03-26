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

$('.button').on('click', function(event) {
  var target = $(event.target).attr('data-target');
  $.get('/api/idea/' + target)
    .done(function(res) {
      alert('Request received!');
      $('.house header').css({ 'writing-mode': 'rl' });
      $('.house header h2').css({ margin: '10px' });
      $('.house .sigil img').css({
        width: '200px',
        height: '200px'
      });
      $('.house .button').css({ display: 'none' });
      $('.house .description').css({ display: 'block' });
      $('.house').velocity({
        'flex-basis': '25%'
      });
      
    });
});
