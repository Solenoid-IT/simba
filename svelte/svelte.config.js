import adapter from '@sveltejs/adapter-static';
import preprocess from 'svelte-preprocess';



import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);



import fs from 'fs';



const core = JSON.parse( fs.readFileSync( `${ __dirname }/.core` ) );



/** @type {import('@sveltejs/kit').Config} */
const config = {
	'preprocess': preprocess(),
	'kit':
	{
		// adapter-auto only supports some environments, see https://kit.svelte.dev/docs/adapter-auto for a list.
		// If your environment is not supported or you settled on a specific environment, switch out the adapter.
		// See https://kit.svelte.dev/docs/adapters for more information about adapters.
		'adapter': adapter({
			'pages':       core['build_path'],
			'assets':      core['build_path'],

			'fallback':    'index.html',
			'precompress': false,
			'strict':      false
		}),

		'appDir': '_svelte',

		'prerender':
		{
			'handleHttpError': 'warn'
		},

		'env':
		{
			'dir': '.',
			'publicPrefix': 'P_'
		}
	}
};

export default config;