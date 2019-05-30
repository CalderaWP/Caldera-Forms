/**
 * Delete everything in webpack client build dirs
 *
 * @since 1.8.6
 *
 * @type {module:fs}
 */

const fs = require('fs');
const path = require( 'path' );
[
    __dirname + '/admin/build',
    __dirname + '/render/build',
    __dirname + '/privacy/build',
    //__dirname + '/blocks/build',
].forEach( directory  => {
    fs.readdir(directory, (err, files) => {
        if (err) throw err;
        for (const file of files) {
            if (! ['style.min.css','index.min.js'].includes(path.basename(file))) {
                console.log(`Deleting ${path.join(directory, file)}`);
                fs.unlink(path.join(directory, file), err => {
                    if (err) throw err;
                });
            }else{
                console.log(`NOT deleting ${path.join(directory, file)}`);
            }
        }
    });
});
