var gulp = require('gulp');
var uglify = require('gulp-uglify');
var gulpIf = require('gulp-if');
var jshint = require('gulp-jshint');
var rename = require('gulp-rename');
var sass = require('gulp-sass');
var livereload = require('gulp-livereload');

gulp.task('minify', function() {
	gulp.src('./js/*.js')
    .pipe(uglify())
	.pipe(rename({
		suffix: '.min'
	}))
    .pipe(gulp.dest('./js/build/'))
    .pipe(livereload());
});

gulp.task('jsLint', function () {
	gulp.src('./js/*.js')
	.pipe(jshint())
	.pipe(jshint.reporter());
});

gulp.task('sass', function() {
	gulp.src('./css/*.scss')
	.pipe(sass())
	.pipe(gulp.dest('./css/'))
    .pipe(livereload());
});

gulp.task('watch-js', function() {
	livereload.listen();
	gulp.watch(['./js/*.js', './css/*.scss'], ['minify', 'jsLint', 'sass']);
});