const { Toolkit } = require('actions-toolkit')
const tools = new Toolkit();
const path = require( 'path');
console.log('Hi Roy');
var ncp = require('ncp').ncp;
const rimraf = require( 'rimraf' );
ncp.limit = 16;
const sourcePath =  path.join(__dirname, '../..');
const resultPath = __dirname + '/build';
const zipPath = __dirname + '/caldera-forms.zip';
const zipFolder = require('zip-folder');
const fs = require( 'fs-extra' );




const clients = [
	'pro',
	'blocks',
	'render',
	'privacy'
];



let i = 0;
rimraf(resultPath, () => {
	//Create directories
	fs.mkdirSync(resultPath);
	fs.mkdirSync(path.join(resultPath,'/clients'));
	clients.forEach( client => {
		[
			`/clients/${client}`,
			`/clients/${client}/build`
		].forEach( dir => {
			if (!fs.existsSync(path.join(resultPath,dir))){
				fs.mkdirSync(path.join(resultPath,dir));
			}
		});

	});
	const excludes = require( './excludes' );
	const options = {
		filter: (filePath) => {

			const fileName = filePath.substring(sourcePath.length).substring(1);
			const dirName = fileName.split('/')[0];
			i++;
			//console.log(i, `processing ${fileName}`)
			return -1 === excludes.files.indexOf(fileName) && -1 === excludes.dirs.indexOf(dirName)
		}
	}; //https://www.npmjs.com/package/ncp#programmatic-usage
	ncp(sourcePath, resultPath, options, function (err) {
		if (err) {
			return console.error('ERROR!');
		}
		console.log('Directory created! Copying clients');
		clients.forEach( client => {
			[
				`/clients/${client}/build/index.min.js`,
				`/clients/${client}/build/style.min.css`
			].forEach( dir => {
				console.log( path.join(sourcePath,dir) );
				if (fs.existsSync(path.join(sourcePath,dir))){
					fs.copySync(path.join(sourcePath,dir),path.join(resultPath,dir));
				}
			});

		});
		console.log( 'Clients created'  );
		rimraf(zipPath, () => {
			zipFolder(resultPath, zipPath, function(err) {
				if(err) {
					console.log('Zip Error', err);
				} else {
					console.log('Zip Created');
				}
			});


		});
	});
});
