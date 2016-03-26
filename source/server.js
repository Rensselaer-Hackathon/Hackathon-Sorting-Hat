'use strict';

var express = require('express'),
    lessMiddleware = require('less-middleware'),
    config = require('./config'),
    app = express();

app.use(lessMiddleware(
  __dirname + '/public',
  { force: true }
));

app.use( express.static(__dirname + '/public') );

app.use( '/api', require('./controllers') );

var server = app.listen(config.port, function() {
  let host = server.address().address,
      port = server.address().port;
  console.log('App running at http://%s%s', host, port);
});
