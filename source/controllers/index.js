'use strict';

var express = require('express'),
    ideas = require('../models/idea'),
    hatComm = require('../helpers/hatComm'),
    router = express.Router();

router.get('/idea/web', function(req, res) {
  var idea = ideas.getIdea('web');
  console.log(idea);
  hatComm.sendMessage( 'Gryffindor ' + idea.join(', ') );
  res.send('ok');
});

router.get('/idea/desktop', function(req, res) {
  var idea = ideas.getIdea('desktop');
  console.log(idea);
  hatComm.sendMessage( 'Hufflepuff ' + idea.join(', ') );
  res.send('ok');
});

router.get('/idea/mobile', function(req, res) {
  var idea = ideas.getIdea('mobile');
  console.log(idea);
  hatComm.sendMessage( 'Slytherin ' + idea.join(', ') );
  res.send('ok');
});

router.get('/idea/data_science', function(req, res) {
  var idea = ideas.getIdea('data_science');
  console.log(idea);
  hatComm.sendMessage( 'Ravenclaw ' + idea.join(', ') );
  res.send('ok');
});

module.exports = router;
