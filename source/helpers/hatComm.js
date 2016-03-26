/**
 * hatComm.js
 */
'use strict';

var net = require('net'),
    config = require('../config.json');

var ip = config.hat.address,
    port = config.hat.port,
    port_ = parseInt(port, 16);

/**
 * Send a message to the sorting hat
 * @param message {string} text to send
 * @returns ?
 */
module.exports.sendMessage = function(message) {
  var client = new net.Socket();
  client.connect(port_, ip, function() {
    console.log('>> connection established');
    client.write(message);
    console.log('>> message sent');
    client.destroy();
  });
}
