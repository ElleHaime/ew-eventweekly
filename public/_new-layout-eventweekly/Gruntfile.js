module.exports = function(grunt) {
    grunt.initConfig({

        // define source files and their destinations
        uglify: {
            files: {
                src: ['src/**/*.js', 'src/*.js'], // source files mask
                dest: 'js/', // destination folder
                // src: ['src/**/*.js', 'src/*.js'], // source files mask
                // dest: '', // destination folder
                expand: true, // allow dynamic building
                flatten: true, // remove all unnecessary nesting
                ext: '.min.js', // replace .js to .min.js
                extDot: 'last'
                // rename  : function (dest, src) {
                //   var folder    = src.substring(0, src.lastIndexOf('/'));
                //   var filename  = src.substring(src.lastIndexOf('/'), src.length);

                //   filename  = filename.substring(0, filename.lastIndexOf('.'));

                //   return dest + folder + filename + '.min.js';
                // }
            }
        },
        watch: {
            js: {
                files: 'js/*.js',
                tasks: ['uglify']
            },
        }
    });

    // load plugins
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-uglify');

    // register at least this one task
    grunt.registerTask('default', ['uglify']);

};