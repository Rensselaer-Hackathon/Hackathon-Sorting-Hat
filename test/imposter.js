/**
 * imposter.js
 */
'use strict';

var net = require('net'),
    config = require('../source/config.json');

var ip = config.hat.address,
    port = config.hat.port,
    port_ = parseInt(port, 16);

var server = net.createServer(function(socket) {
  // foo ?
}).on('error', function(err) {
  console.log('ERROR: ', err);
}).on('connection', function(socket) {
  console.log('>> new connection made!');
  socket.on('data', function(data) {
    console.log('received: %s', data);
  });
});

server.listen({
  host: ip,
  port: port_,
  exclusive: true
}, function() {
  console.log('TCP server opened on %s:%s', ip, port_);
});
