var gulp = require('gulp'),
    nodemon = require('gulp-nodemon');

gulp.task('default', ['nodemon']);

gulp.task('nodemon', function(done) {
  nodemon({ script: './source/server.js' });
});
