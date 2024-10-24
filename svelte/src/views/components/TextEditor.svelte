<script context="module">

    import { Core } from '../../lib/solenoid/solenoid.core.js';
    import loader from '@monaco-editor/loader';

    /*

    import * as monaco from 'monaco-editor';

    import editorWorker from 'monaco-editor/esm/vs/editor/editor.worker?worker';
    import cssWorker from 'monaco-editor/esm/vs/language/css/css.worker?worker';
    import htmlWorker from 'monaco-editor/esm/vs/language/html/html.worker?worker';
    import jsonWorker from 'monaco-editor/esm/vs/language/json/json.worker?worker';
    import tsWorker from 'monaco-editor/esm/vs/language/typescript/ts.worker?worker';



    self.MonacoEnvironment =
    {
        'getWorker': function (_, label)
        {
            switch (label)
            {
                case 'json':
                    // Returning the value
                    return new jsonWorker();
                case 'css':
                case 'scss':
                case 'less':
                    // Returning the value
                    return new cssWorker();
                case 'html':
                case 'handlebars':
                case 'razor':
                    // Returning the value
                    return new htmlWorker();
                case 'typescript':
                case 'javascript':
                    // Returning the value
                    return new tsWorker();
                default:
                    // Returning the value
                    return new editorWorker();
            }
        }
    }
    ;

    */

</script>

<script>

    import { createEventDispatcher, onMount, onDestroy } from 'svelte';



    const dispatch = createEventDispatcher();



    // (Setting the values)
    const pvt = {};
    const pub = {};



    // (Setting the values)
    pvt.visible       = true;
    pvt.editor        = null;
    pvt.editorElement = null;

    pub.element       = null;



    // Returns [string]
    pub.getValue = function ()
    {
        // Returning the value
        return pvt.editor.getValue();
    }

    // Returns [pub]
    pub.setValue = function (value)
    {
        // (Setting the value)
        pvt.editor.setValue(value);



        // Returning the value
        return pub;
    }



    export let id       = null;
    export let api      = null;

    export let settings = {};



    // (Listening for the events)
    onMount
    (
        async function ()
        {
            // (Setting the config)
            loader.config
            (
                {
                    'paths':
                    {
                        //'vs': '/node_modules/monaco-editor/min/vs'
                        'vs': `https://${ Core.envs['BE_HOST'] }/assets/lib/monaco-editor/min/vs`
                    }
                }
            )
            ;

            // (Loading the config)
            pvt.monaco = await loader.init();



            if ( typeof settings['automaticLayout'] === 'undefined' ) settings['automaticLayout'] = true;
            if ( typeof settings['theme'] === 'undefined' ) settings['theme'] = 'vs-dark';
            if ( typeof settings['fontSize'] === 'undefined' ) settings['fontSize'] = '20px';
            if ( typeof settings['language'] === 'undefined' ) settings['language'] = 'text';
            if ( typeof settings['value'] === 'undefined' ) settings['value'] = '';
            if ( typeof settings['bracketPairColorization.enabled'] === 'undefined' ) settings['bracketPairColorization.enabled'] = false;



            // (Creating the editor)
            pvt.editor = pvt.monaco.editor.create( pvt.editorElement, settings );



            // (Listening for the event)
            pvt.editor.getModel().onDidChangeContent(function (event) {
                // (Triggering the event)
                dispatch('change');
            });



            // (Triggering the event)
            dispatch('ready');
        }
    )
    ;

    onDestroy
    (
        function ()
        {
            // (Iterating each model)
            pvt.monaco?.editor.getModels().forEach( function (model) { model.dispose(); });

            // (Disposing the editor)
            pvt.editor?.dispose();
        }
    )
    ;



    // (Getting the value)
    api = pub;

</script>

{ #if api }
    { #if pvt.visible }
        <div class="swg swg-text-editor { $$restProps.class }" id={ id } bind:this={ pub.element }>
            <div class="editor" bind:this={ pvt.editorElement }></div>
            <div class="resize-handle-controls">
                <div class="resize-handle" title="resize"></div>
            </div>
        </div>
    { /if }
{ /if }

<style>

    .swg.swg-text-editor
    {
        height: 360px;
    }

    .editor
    {
        width: 100%;
        height: 100%;

        display: block;
    }

    .resize-handle-controls
    {
        width: 100%;

        display: block;

        position: relative;
    }

    .resize-handle
    {
        width: 100%;
        height: 14px;

        position: absolute;
        top: -1px;

        background-color: #232323;

        cursor: pointer;

        transition: .2s all ease-in-out;
    }

    .resize-handle:hover
    {
        background-color: #004fc6;
    }

</style>