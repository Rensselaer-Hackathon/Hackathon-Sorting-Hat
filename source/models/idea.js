/**
 * idea.js
 */
'use strict';

var data = require('./data.json'),
    _ = require('underscore');

/**
 * Generate a random idea spec for a given topic
 * @param topic {string} idea topic
 * @returns {string[]} list of length 3 containing the idea specs
 */
module.exports.getIdea = function(topic) {
  if ( _.contains(_.keys(data), topic) === false ) {
    throw new Exception('Invalid topic identifier');
  }
  let i = Math.floor(Math.random() * data[topic]['1'].length),
      j = Math.floor(Math.random() * data[topic]['2'].length),
      k = Math.floor(Math.random() * data[topic]['3'].length);
  return [
    data[topic]['1'][i],
    data[topic]['2'][j],
    data[topic]['3'][k]
  ]
}
