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
            console.log(path.join(directory, file));
            fs.unlink(path.join(directory, file), err => {
                if (err) throw err;
            });
        }
    });
});
