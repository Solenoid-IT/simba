import { sveltekit } from '@sveltejs/kit/vite';
import { defineConfig } from 'vite';

import fs from 'fs';



const core = JSON.parse( fs.readFileSync( `${ __dirname }/.core` ) );



export default defineConfig({
	'plugins': [sveltekit()],
	'server':
	{
		'host': core['dev_server']['host'],
		'https':
		{
			'key': fs.readFileSync( core['dev_server']['https']['key'] ),
			'cert': fs.readFileSync( core['dev_server']['https']['cert'] )
		}
	}
});



/*

export default
	defineConfig
	(
		function (cmd, mode)
		{
			// (Getting the values)
			//const env = loadEnv( mode, process.cwd(), '');



			// (Getting the value)
			const object =
			{
				'plugins': [ sveltekit() ],
				//'define': { ...Object.keys( env ).reduce( function (prev, key) { prev[`process.env.${ key }`] = JSON.stringify( env[key] ); return prev; }, {} ) }
			}
			;



			// Returning the value
			return object;
		}
	)
;

*/