
'use strict'

const importStart = Date.now()
const gutil = require('gulp-util')
gutil.log(`Importing packages ...`)

const path = require('path')
const browserify = require('browserify')
const gulp = require('gulp')
const cleanCSS = require('gulp-clean-css')
const concat = require('gulp-concat')
const flatten = require('gulp-flatten')
const sass = require('gulp-sass')
const sourcemaps = require('gulp-sourcemaps')
const uglify = require('gulp-uglify')
const assign = require('lodash.assign')
const buffer = require('vinyl-buffer')
const source = require('vinyl-source-stream')
const watchify = require('watchify')
const babelify = require('babelify')
const vueify = require('vueify')

const cyan = gutil.colors.cyan
const importTime = (Date.now() - importStart) /  1000
gutil.log(cyan(`Imports`) + ` (${importTime} seconds)`)


// -------------------------------------------------------------
// File paths
// -------------------------------------------------------------
const assetPath = './assets'
const vendorPath = assetPath + '/vendor'
const distPath = './web/compiled'


// -------------------------------------------------------------
// Browserify + watchify setup - this caches the object for faster compilation
// https://github.com/gulpjs/gulp/blob/master/docs/recipes/fast-browserify-builds-with-watchify.md
// https://github.com/substack/watchify
// -------------------------------------------------------------
const pollInterval = 350
const customOpts = {
    entries: [`${assetPath}/js/main.js`],
    debug: true
}
const browserifyOpts = assign({}, watchify.args, customOpts)
const watchifyOpts = {poll: pollInterval, delay: pollInterval, ignoreWatch: ['**/node_modules/**', 'vendor/**', '**/*.php']}

// note: this uses the options set in .babelrc so we can use those features in .vue files
// @link https://github.com/vuejs/vueify/issues/71#issuecomment-202013630
const b = browserify(browserifyOpts)
    .transform(babelify)
    .transform(vueify)

// Make sure to have the NODE_ENV environment variable set to "production" when building for production!
// This strips away unnecessary code (e.g. hot-reload) for smaller bundle size.
// @link https://github.com/vuejs/vueify#building-for-production
process.env.NODE_ENV = 'production'


// -------------------------------------------------------------
// Tasks and events
// -------------------------------------------------------------

let jsFiles
let cssFiles
let isBuildOnly = true

// Build task
gulp.task('default', ['build'])
gulp.task('build', function() {
    jsFiles = 'First run'
    buildAssets()
})

// Watch updates for js/vue files
// the b.on('update') is called before the b.on('log')
// we need both to compute the display text (files and msg)
b.on('update', function(files) {
    // convert array of files to string (basename only)
    for (let i=0; i<files.length; i++) {
        files[i] = path.basename(files[i])
    }
    jsFiles = files.join(' / ')
    buildAssets()
})
b.on('log', function(msg) {
    if (jsFiles) {
        jsFiles = cyan(jsFiles) + ' ' + msg
    }
})

// Watch task for sass files
gulp.task('watch', ['build'], function() {
    watchify(b, watchifyOpts)
    gulp.watch(`${assetPath}/sass/*`, function(e) {
        cssFiles = path.basename(e.path)
        buildAssets()
    })
    isBuildOnly = false
})


// -------------------------------------------------------------
// Build
// -------------------------------------------------------------

function buildAssets() {

    const bundle = b.bundle()
    const buildStart = Date.now()

    // -----------------------------------------------------
    // build compiled js files
    const jsStream = bundle
        .on('error', function(err) {
            gutil.log(gutil.colors.red('Browserify error:'), err.message)
        })
        .pipe(source('compiled.js'))
        .pipe(buffer())
        .on('end', function() {
            if (isBuildOnly) {
                const buildTime = (Date.now() - buildStart) / 1000
                gutil.log(cyan(`Bundle`) + `  (${buildTime} seconds)`)
            } else if (jsFiles) {
                gutil.log(jsFiles)
            }
            jsFiles = null
        })
    jsStream
        .pipe(sourcemaps.init({loadMaps: true})) // loads map from browserify file
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(distPath))
    jsStream
        .pipe(concat(`compiled.min.js`))
        .pipe(uglify())
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(distPath))
        .on('end', function() {
            if (isBuildOnly) {
                const buildTime = (Date.now() - buildStart) / 1000
                const totalTime = (Date.now() - importStart) / 1000
                gutil.log(cyan(`Scripts`) + ` (${buildTime} seconds)`)
                gutil.log(cyan(`-----------------------`))
                gutil.log(cyan(`Total`) + `   (${totalTime} seconds)`)
            }
        })

    // -----------------------------------------------------
    // build vendor js files
    gulp.src([`${vendorPath}/**/jquery-*.js`, `${vendorPath}/**/*.js`, `!${vendorPath}/**/*.min.js`])
        .pipe(concat(`vendor.js`))
        .pipe(gulp.dest(distPath))
    gulp.src([`${vendorPath}/**/jquery-*.min.js`, `${vendorPath}/**/*.min.js`])
        .pipe(concat(`vendor.min.js`))
        .pipe(gulp.dest(distPath))

    // -----------------------------------------------------
    // build compiled css files
    const cssStream = gulp.src(`${assetPath}/sass/main.scss`)
        .pipe(sourcemaps.init())
        .pipe(sass().on('error', function(err) {
            gutil.log(gutil.colors.red('Sass error:'), err.message)
        }))
    cssStream
        .pipe(concat(`compiled.css`))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(distPath))
    cssStream
        .pipe(concat(`compiled.min.css`))
        .pipe(cleanCSS(/*{compatibility: 'ie8'}*/))
        .pipe(sourcemaps.write('./'))
        .pipe(gulp.dest(distPath))
        .on('end', function() {
            const buildTime = (Date.now() - buildStart) / 1000
            if (isBuildOnly) {
                gutil.log(cyan(`Sass`) + `    (${buildTime} seconds)`)
            } else if (cssFiles) {
                gutil.log(cyan(cssFiles) + ` (${buildTime} seconds)`)
            }
            cssFiles = null
        })

    // -----------------------------------------------------
    // build vendor css files
    gulp.src([`${vendorPath}/**/*.css`, `!${vendorPath}/**/*.min.css`])
        .pipe(concat(`vendor.css`))
        .pipe(gulp.dest(distPath))
    gulp.src([`${vendorPath}/**/*.min.css`])
        .pipe(concat(`vendor.min.css`))
        .pipe(gulp.dest(distPath))

    // -----------------------------------------------------
    // copy map files
    gulp.src(`${vendorPath}/**/*.map`)
        .pipe(flatten())
        .pipe(gulp.dest(distPath))
    // -----------------------------------------------------
}
