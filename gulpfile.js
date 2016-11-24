var gulp = require("gulp"),
    minCss = require("gulp-clean-css"),
    sass = require("gulp-sass")//,
    /*less = require("gulp-less"),
    
    minJs = require("gulp-uglify")*/;

gulp.task("sass", function () {
    return gulp.src("html/assets/sass/*.scss")
        .pipe(sass())
        .pipe(minCss())
        .pipe(gulp.dest("html/assets/css"));
})

gulp.task("default", ['sass']);