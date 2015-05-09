module.exports = function(grunt) {
  grunt.initConfig({
    copy: {
      dist: {
        cwd: 'src/', expand: true, src: '**', dest: 'dist/'
      }
    },
    cssmin: {
      dist: {
        files: [
          { src: 'dist/libs/dxspottv.css', dest: 'dist/libs/dxspottv.min.css' }
        ],
        options: {
          keepSpecialComments: 0
        }
      }
    },
    concat: {
        dist: {
            files: {
                'dist/libs/jquerybootstrap.min.js': [
                    'dist/libs/jquery-1.11.2.min.js',
                    'dist/libs/bootstrap-3.3.4-dist/js/bootstrap.min.js'
                ],
                'dist/libs/compiled.min.css': [
                    'dist/libs/bootstrap-3.3.4-dist/css/bootstrap.min.css',
                    'dist/libs/dxspottv.min.css'
                ],
            },
        },
    },
    processhtml: {
      dist: {
        files: {
        'dist/index.html': ['src/index.html'],
        }
      }
    },
    compress: {
      dist: {
        options: {
          mode: 'gzip',
          level: 9
        },
        expand: true,
        cwd: 'dist/libs/',
        src: ['**/*'],
        dest: 'dist/libs/',
	rename: function(dest, src) {
            return dest + src + '.gz';
        }      
     }
    }
  });
  // Load the plugins
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-processhtml');
  grunt.loadNpmTasks('grunt-contrib-compress');
  // Default tasks.
  grunt.registerTask('default', ['copy', 'cssmin', 'concat', 'processhtml', 'compress']);
};
