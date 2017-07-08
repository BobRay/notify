<?php

 /*               DO NOT EDIT THIS FILE

  Edit the file in the MyComponent config directory
  and run ExportObjects

 */



$packageNameLower = 'notify'; /* No spaces, no dashes */

$components = array(
    /* These are used to define the package and set values for placeholders */
    'packageName' => 'Notify',  /* No spaces, no dashes */
    'packageNameLower' => $packageNameLower,
    'packageDescription' => 'Notify project for MyComponent extra',
    'version' => '1.4.1',
    'release' => 'pl',
    'author' => 'Bob Ray',
    'email' => '<https://bobsguides.com>',
    'authorUrl' => 'https://bobsguides.com',
    'authorSiteName' => "Bob's Guides",
    'packageDocumentationUrl' => 'https://bobsguides.com/notify-tutorial.html',
    'copyright' => '2013-2017',

    /* no need to edit this except to change format */
    'createdon' => strftime('%m-%d-%Y'),

    'gitHubUsername' => 'BobRay',
    'gitHubRepository' => 'Notify',

    /* two-letter code of your primary language */
    'primaryLanguage' => 'en',

    /* Set directory and file permissions for project directories */
    'dirPermission' => 0755,  /* No quotes!! */
    'filePermission' => 0644, /* No quotes!! */

    /* Define source and target directories */

    /* path to MyComponent source files */
    'mycomponentRoot' => $this->modx->getOption('mc.root', null,
        MODX_CORE_PATH . 'components/mycomponent/'),

    /* path to new project root */
    'targetRoot' => MODX_ASSETS_PATH . 'mycomponents/' . $packageNameLower . '/',


    /* *********************** NEW SYSTEM SETTINGS ************************ */

    /* If your extra needs new System Settings, set their field values here.
     * You can also create or edit them in the Manager (System -> System Settings),
     * and export them with exportObjects. If you do that, be sure to set
     * their namespace to the lowercase package name of your extra */

    'newSystemSettings' => array(
        'nf_status_resource_id' => array( // key
             'key'         => 'nf_status_resource_id',
             'name'        => 'Notify Status Resource ID',
             'description' => 'ID of Notify Status Resource',
             'namespace'   => 'notify',
             'xtype'       => 'integer',
             'value'       => '',
             'area'        => '',
        ),
    ),

    /* ************************ NEW SYSTEM EVENTS ************************* */

    /* Array of your new System Events (not default
     * MODX System Events). Listed here so they can be created during
     * install and removed during uninstall.
     *
     * Warning: Do *not* list regular MODX System Events here !!! */

    'newSystemEvents' => array(
    ),

    /* ************************ NAMESPACE(S) ************************* */
    /* (optional) Typically, there's only one namespace which is set
     * to the $packageNameLower value. Paths should end in a slash
    */

    'namespaces' => array(
        'notify' => array(
            'name' => 'notify',
            'path' => '{core_path}components/notify/',
            'assets_path' => '{assets_path}components/notify/',
        ),

    ),



    /* ************************* CATEGORIES *************************** */
    /* (optional) List of categories. This is only necessary if you
     * need to categories other than the one named for packageName
     * or want to nest categories.
    */

    'categories' => array(
        'Notify' => array(
            'category' => 'Notify',
            'parent' => '',  /* top level category */
        ),
    ),

    /* *************************** MENUS ****************************** */

    /* If your extra needs Menus, you can create them here
     * or create them in the Manager, and export them with exportObjects.
     * Be sure to set their namespace to the lowercase package name
     * of your extra.
     *
     * Every menu should have exactly one action */

    'menus' => array(

    ),


    /* ************************* ELEMENTS **************************** */

    /* Array containing elements for your extra. 'category' is required
       for each element, all other fields are optional.
       Property Sets (if any) must come first!

       The standard file names are in this form:
           SnippetName.snippet.php
           PluginName.plugin.php
           ChunkName.chunk.html
           TemplateName.template.html

       If your file names are not standard, add this field:
          'filename' => 'actualFileName',
    */


    'elements' => array(

        'propertySets' => array( /* all three fields are required */

        ),

        'snippets' => array(
            'Notify' => array( /* notify with static and property set(s)  */

                'description' => 'Send site updates to Subscribers and Twitter',
                'static' => false,
                'category' => 'Notify',
            ),

        ),
        'plugins' => array(
            'Notify' => array( /* minimal notify */
                'description' => 'Plugin for Notify extra',
                'category' => 'Notify',
                'events' => array(
                    'OnDocFormPrerender' => array(
                        'priority' => '0',
                        /* priority of the event -- 0 is highest priority */
                        'group' => 'plugins',
                        /* should generally be set to 'plugins' */
                        'propertySet' => '',
                        /* property set to be used in this pluginEvent */
                    ),
                ),
            ),
        ),
        'chunks' => array(
            'NfTweetTplExisting' => array(
                'name'        => 'NfTweetTplExisting',
                'description' => 'Notify Tweet Tpl for existing resource',
                'category' => 'Notify',
            ),
            'NfTweetTplNew' => array(
                'name'        => 'NfTweetTplNew',
                'description' => 'Notify Tweet Tpl for new resource',
                'category' => 'Notify',
            ),
            'NfTweetTplCustom' => array(
                'name'        => 'NfTweetTplCustom',
                'description' => 'Custom Notify Tweet Tpl',
                'category' => 'Notify',
            ),
            'NfEmailSubjectTpl' => array(
                'name'        => 'NfEmailSubjectTpl',
                'description' => 'Subject for Notify Email to subscribers',
                'category'    => 'Notify',
            ),
            'NfSubscriberEmailTplExisting' => array(
                'name'        => 'NfSubscriberEmailTplExisting',
                'description' => 'HTML for Notify existing resource subscriber email',
                'category'    => 'Notify',
            ),
            'NfSubscriberEmailTplNew' => array(
                'name'        => 'NfSubscriberEmailTplNew',
                'description' => 'HTML for Notify new resource subscriber email',
                'category'    => 'Notify',
            ),
            'NfSubscriberEmailTplCustom' => array(
                'name'        => 'NfSubscriberEmailTplCustom',
                'description' => 'HTML for Notify custom subscriber email',
                'category'    => 'Notify',
            ),
            'NfSubscriberEmail-fancyTpl' => array(
                'name'        => 'NfSubscriberEmail-fancyTpl',
                'description' => 'HTML for Fancy Notify subscriber email',
                'category'    => 'Notify',
            ),
            'NfNotifyFormTpl' => array(
                'name'        => 'NfNotifyFormTpl',
                'description' => 'Form Tpl for Notify Extra',
                'category'    => 'Notify',
            ),
            'NfUnsubscribeTpl' => array(
                'name'        => 'NfUnsubscribeTpl',
                'description' => 'Unsubscribe link chunk for Notify Extra',
                'category'    => 'Notify',
            ),
            'NfProgressbarJs' => array(
                'name'        => 'NfProgressbarJs',
                'description' => 'JS code for Notify progress bar',
                'category'    => 'Notify',
            ),
            'NfStatus' => array(
                'name'        => 'NfStatus',
                'description' => 'Status chunk for Notify progress bar',
                'category'    => 'Notify',
            ),

        ),
        'templates' => array(
            'NotifyTemplate' => array(
                'property_preprocess' => '0',
                'templatename'        => 'NotifyTemplate',
                'description'         => 'Template for Notify Resource',
                'icon'                => '',
                'template_type'       => '0',
                'properties'          => '',
                'category' => 'Notify',
            ),
        ),
        'templateVars' => array(
            'NotifySubscribers' => array(
                'property_preprocess' => '0',
                'type'                => 'option',
                'name'                => 'NotifySubscribers',
                'caption'             => '<button id="nf-b" onClick="nf()"> Launch Notify </button>',
                'description'         => 'Click to launch Notify',
                'elements'            => 'Notify for new resource==new||Update to existing resource==existing||Use custom Tpl==custom||Use blank Tpl==blank',
                'rank'                => '1',
                'display'             => 'default',
                'default_text'        => 'existing',
                'properties'          => '',
                'input_properties'    => 'a:2:{s:10:"allowBlank";s:4:"true";s:7:"columns";s:1:"1";}',
                'output_properties'   => 'a:0:{}',
                'category' => 'Notify',
                'templates' => array(
                    'default'   => 1,
                ),
            ),
        ),
    ),
    /* (optional) will make all element objects static - 'static' field above will be ignored */
    'allStatic' => false,


    /* ************************* RESOURCES ****************************
     Important: This list only affects Bootstrap. There is another
     list of resources below that controls ExportObjects.
     * ************************************************************** */
    /* Array of Resource pagetitles for your Extra; All other fields optional.
       You can set any resource field here */
    'resources' => array(
        'Notify' => array(
            'type'                  => 'document',
            'contentType'           => 'text/html',
            'pagetitle'             => 'Notify',
            'longtitle'             => 'Notify',
            'description'           => '',
            'alias'                 => 'notify',
            'link_attributes'       => '',
            'published'             => '1',
            'isfolder'              => '0',
            'introtext'             => '',
            'richtext'              => '0',
            'template'              => 'NotifyTemplate',
            'menuindex'             => '0',
            'searchable'            => '1',
            'cacheable'             => '1',
            'createdby'             => '1',
            'editedby'              => '',
            'deleted'               => '0',
            'deletedon'             => '0',
            'deletedby'             => '0',
            'menutitle'             => '',
            'donthit'               => '0',
            'privateweb'            => '0',
            'privatemgr'            => '0',
            'content_dispo'         => '0',
            'hidemenu'              => '1',
            'class_key'             => 'modDocument',
            'context_key'           => 'web',
            'content_type'          => '1',
            'hide_children_in_tree' => '0',
            'show_in_tree'          => '1',
            'properties'            => '',
        ),
        'NotifyPreview' => array(
            'parent'                => 'Notify',
            'type'                  => 'document',
            'contentType'           => 'text/html',
            'pagetitle'             => 'NotifyPreview',
            'longtitle'             => '',
            'description'           => '',
            'alias'                 => 'notify-preview',
            'link_attributes'       => '',
            'published'             => '1',
            'isfolder'              => '0',
            'introtext'             => '',
            'richtext'              => '0',
            'template'              => 'NotifyTemplate',
            'menuindex'             => '',
            'searchable'            => '0',
            'cacheable'             => '0',
            'createdby'             => '1',
            'editedby'              => '1',
            'deleted'               => '0',
            'deletedon'             => '0',
            'deletedby'             => '0',
            'menutitle'             => '',
            'donthit'               => '0',
            'privateweb'            => '0',
            'privatemgr'            => '0',
            'content_dispo'         => '0',
            'hidemenu'              => '1',
            'class_key'             => 'modDocument',
            'context_key'           => 'web',
            'content_type'          => '1',
            'hide_children_in_tree' => '0',
            'show_in_tree'          => '1',
            'properties'            => '',
        ),
        'NotifyStatus' => array(
            'parent' => 'Notify',
            'type'                  => 'document',
            'contentType'           => 'text/html',
            'pagetitle'             => 'NotifyStatus',
            'longtitle'             => '',
            'description'           => '',
            'alias'                 => 'notify-status',
            'link_attributes'       => '',
            'published'             => '1',
            'isfolder'              => '0',
            'introtext'             => '',
            'richtext'              => '0',
            'template'              => 0,
            'menuindex'             => '',
            'searchable'            => '0',
            'cacheable'             => '0',
            'createdby'             => '1',
            'editedby'              => '1',
            'deleted'               => '0',
            'deletedon'             => '0',
            'deletedby'             => '0',
            'menutitle'             => '',
            'donthit'               => '0',
            'privateweb'            => '0',
            'privatemgr'            => '0',
            'content_dispo'         => '0',
            'hidemenu'              => '1',
            'class_key'             => 'modDocument',
            'context_key'           => 'web',
            'content_type'          => '1',
            'hide_children_in_tree' => '0',
            'show_in_tree'          => '1',
            'properties'            => '',
            'content'               => '[[!$NfStatus]]',
        ),
    ),


    /* Array of languages for which you will have language files,
     *  and comma-separated list of topics
     *  ('.inc.php' will be added as a suffix). */
    'languages' => array(
        'en' => array(
            'default',
            'properties',
            'form',
        ),
    ),
    /* ********************************************* */
    /* Define optional directories to create under assets.
     * Add your own as needed.
     * Set to true to create directory.
     * Set to hasAssets = false to skip.
     * Empty js and/or css files will be created.
     */
    'hasAssets' => true,

    'assetsDirs' => array(
        /* If true, a default (empty) CSS file will be created */
        'css' => true,

    ),
    /* minify any JS files */
    'minifyJS' => false,
    /* Create a single JS file from all JS files */
    'createJSMinAll' => false,
    /* if this is false, regular jsmin will be used.
       JSMinPlus is slower but more reliable */
    'useJSMinPlus' => false,

    /* These will automatically go under assets/components/yourcomponent/js/
       Format: directory:filename
       (no trailing slash on directory)
       if 'createCmpFiles is true, these will be ignored.
    */
    $jsFiles = array(

    ),


    /* ********************************************* */
    /* Define basic directories and files to be created in project*/

    'docs' => array(
        'readme.txt',
        'license.txt',
        'changelog.txt',
        'tutorial.html'
    ),

    /* (optional) Description file for GitHub project home page */
    'readme.md' => true,
    /* assume every package has a core directory */
    'hasCore' => true,

    /* ********************************************* */
    /* (optional) Array of extra script resolver(s) to be run
     * during install. Note that resolvers to connect plugins to events,
     * property sets to elements, resources to templates, and TVs to
     * templates will be created automatically -- *don't* list those here!
     *
     * 'default' creates a default resolver named after the package.
     * (other resolvers may be created above for TVs and plugins).
     * Suffix 'resolver.php' will be added automatically */
    'resolvers' => array(
        'default',
        'install.script',
    ),

    /* Dependencies */
    'requires' => array(
        'subscribe' => '>=1.2.1',
    ),
    /* (optional) Validators can abort the install after checking
     * conditions. Array of validator names (no
     * prefix of suffix) or '' 'default' creates a default resolver
     *  named after the package suffix 'validator.php' will be added */

    'validators' => array(),

    /* (optional) install.options is needed if you will interact
     * with user during the install.
     * See the user.input.php file for more information.
     * Set this to 'install.options' or ''
     * The file will be created as _build/install.options/user.input.php
     * Don't change the filename or directory name. */
    'install.options' => '',


    /* Suffixes to use for resource and element code files (not implemented)  */
    'suffixes' => array(
        'modPlugin' => '.php',
        'modSnippet' => '.php',
        'modChunk' => '.html',
        'modTemplate' => '.html',
        'modResource' => '.html',
    ),


    /* ********************************************* */
    /* (optional) Only necessary if you will have class files.
     *
     * Array of class files to be created.
     *
     * Format is:
     *
     * 'ClassName' => 'directory:filename',
     *
     * or
     *
     *  'ClassName' => 'filename',
     *
     * ('.class.php' will be appended automatically)
     *
     *  Class file will be created as:
     * yourcomponent/core/components/yourcomponent/model/[directory/]{filename}.class.php
     *
     * Set to array() if there are no classes. */
    'classes' => array(
        'Notify' => 'notify:notify',
        'MailgunX' => 'notify:mailgunx',
        'UrlShortener',
    ),

    /* ************************************
     *  These values are for CMPs.
     *  Set any of these to an empty array if you don't need them.
     *  **********************************/

    /* If this is false, the rest of this section will be ignored */

    'createCmpFiles' => false,

    /* IMPORTANT: The array values in the rest of
       this section should be all lowercase */

    /* This is the main action file for your component.
       It will automatically go in core/component/yourcomponent/
    */

    'actionFile' => 'index.php',

    /* CSS file for CMP */

    'cssFile' => 'mgr.css',

    /* These will automatically go to core/components/yourcomponent/processors/
       format directory:filename
       '.class.php' will be appended to the filename

       Built-in processor classes include getlist, create, update, duplicate,
       import, and export. */

    'processors' => array(
        'mgr/snippet:getlist',
        'mgr/snippet:changecategory',
        'mgr/snippet:remove',

        'mgr/chunk:getlist',
        'mgr/chunk:changecategory',
        'mgr/chunk:remove',
    ),

    /* These will automatically go to core/components/yourcomponent/controllers[/directory]/filename
       Format: directory:filename */

    'controllers' => array(
        ':index.php',
        'mgr:header.php',
        'mgr:home.php',
    ),

    /* These will automatically go in assets/components/yourcomponent/ */

    'connectors' => array(
        'connector.php'

    ),
    /* These will automatically go to assets/components/yourcomponent/js[/directory]/filename
       Format: directory:filename */

    'cmpJsFiles' => array(
        ':notify.js',
        'sections:home.js',
        'widgets:home.panel.js',
        'widgets:snippet.grid.js',
        'widgets:chunk.grid.js',
    ),


    /* *******************************************
     * These settings control exportObjects.php  *
     ******************************************* */
    /* ExportObjects will update existing files. If you set dryRun
       to '1', ExportObjects will report what it would have done
       without changing anything. Note: On some platforms,
       dryRun is *very* slow  */

    'dryRun' => '0',

    /* Array of elements to export. All elements set below will be handled.
     *
     * To export resources, be sure to list pagetitles and/or IDs of parents
     * of desired resources
    */
    'process' => array(
        'snippets',
        'plugins',
        'templateVars',
        'templates',
        'chunks',
        'resources',
        'propertySets',
        'systemSettings',
    ),
    /*  Array  of resources to process. You can specify specific resources
        or parent (container) resources, or both.

        They can be specified by pagetitle or ID, but you must use the same method
        for all settings and specify it here. Important: use IDs if you have
        duplicate pagetitles */
    'getResourcesById' => false,

    'exportResources' => array(
        'Notify',
        'NotifyPreview',
        'NotifyStatus'
    ),
    /* Array of resoure parent IDs to get children of. */
    'parents' => array(),
    /* Also export the listed parent resources
      (set to false to include just the children) */
    'includeParents' => false,


    /* ******************** LEXICON HELPER SETTINGS ***************** */
    /* These settings are used by LexiconHelper */
    'rewriteCodeFiles' => true,  /* remove ~~descriptions */
    'rewriteLexiconFiles' => true, /* automatically add missing strings to lexicon files */
    /* ******************************************* */

    /* Array of aliases used in code for the properties array.
     * Used by the checkproperties utility to check properties in code against
     * the properties in your properties transport files.
     * if you use something else, add it here (OK to remove ones you never use.
     * Search also checks with '$this->' prefix -- no need to add it here. */
    'scriptPropertiesAliases' => array(
        'props',
        'sp',
        'config',
        'scriptProperties'
    ),
);

return $components;