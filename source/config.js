var settings = {},
    config = {};

try {
  settings = require('./config.json');
} catch (e) {
  settings = {};
}

config.port = settings.port || 8000;

config.hat = settings.hat || {};

module.exports = config;
