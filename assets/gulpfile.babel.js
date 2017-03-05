/**
 * Created by xandros15 on 2016-11-26.
 */
'use strict';

import gulp from "gulp";
import uglify from "gulp-uglify";
import sass from "gulp-sass";
import gutil from "gulp-util";

const webRoot = '../web/dist';

gulp.task('default', ['scripts', 'sass']);

gulp.task('scripts', () =>
    gulp.src('./scripts/*.js')
        .pipe(uglify({compress: true}))
        .on('error', gutil.log)
        .pipe(gulp.dest(webRoot)));

gulp.task('sass', () =>
    gulp.src('./stylesheets/*.scss')
        .pipe(sass().on('error', sass.logError))
        .pipe(gulp.dest(webRoot)));

gulp.task('sass:watch', () => gulp.watch('./sass/**/*.scss', ['sass']));