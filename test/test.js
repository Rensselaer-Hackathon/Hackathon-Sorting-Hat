'use strict';

let net = require('net');
    config = require('./source/config.json');

let ip = config.hat.address,
    port = config.hat.port,
    port_ = parseInt(port, 16);

let client = new net.Socket();

client.connect(port_, ip, function() {
  console.log('>> connection established');
  client.write('hello,world,meh');
  
  client.destroy();
});

