<?php

namespace ExpressionEngine\Addons\Rte\Service;

use ExpressionEngine\Library\Rte\RteFilebrowserInterface;

class RedactorService implements RteService {

    public $output;
    public $class = 'rte-textarea redactor-box';
    public $handle;
    protected $settings;
    protected $toolset;
    private static $_includedFieldResources = false;
    private static $_includedConfigs;
    private $_fileTags;
    private $_pageTags;
    private $_extraTags;
    private $_sitePages;
    private $_pageData;

    public function init($settings, $toolset = null)
    {
        $this->settings = $settings;
        $this->toolset = $toolset;
        $this->includeFieldResources();
        $this->insertConfigJsById();
        return $this->handle;
    }

    protected function includeFieldResources()
    {
        if (! static::$_includedFieldResources) {
            $version = ee('Addon')->get('rte')->getVersion();
            // Styles
            ee()->cp->add_to_head('<link rel="stylesheet" href="' . URL_THEMES . 'rte/scripts/redactor/redactor.css?v=' . $version . '" type="text/css" media="print, projection, screen" />');
            ee()->cp->add_to_head('<link rel="stylesheet" href="' . URL_THEMES . 'rte/styles/redactor/addon_pbf.css?v=' . $version . '" type="text/css" media="print, projection, screen" />');

            ee()->cp->add_to_foot('<script type="text/javascript" src="' . URL_THEMES . 'rte/scripts/rte.js?v=' . $version . '"></script>');
            ee()->cp->add_to_foot('<script type="text/javascript" src="' . URL_THEMES . 'rte/scripts/redactor/redactor.js?v=' . $version . '"></script>');
            foreach (static::defaultToolbars()['Redactor Full']['plugins'] as $plugin) {
                ee()->cp->add_to_foot('<script type="text/javascript" src="' . URL_THEMES . 'rte/scripts/redactor/plugins/' . $plugin . '/' . $plugin . '.js?v=' . $version . '"></script>');
            }
            $language = isset(ee()->session) ? ee()->session->get_language() : ee()->config->item('deft_lang');
            $lang_code = ee()->lang->code($language);
            if ($lang_code != 'en') {
                ee()->cp->add_to_foot('<script type="text/javascript" src="' . URL_THEMES . 'rte/scripts/redactor/langs/' . $lang_code . '.js?v=' . $version . '"></script>');
            }

            $action_id = ee()->db->select('action_id')
                ->where('class', 'Rte')
                ->where('method', 'pages_autocomplete')
                ->get('actions');

            $filedir_urls = ee('Model')->get('UploadDestination')->all()->getDictionary('id', 'url');

            ee()->javascript->set_global([
                'Rte.pages_autocomplete' => ee()->functions->fetch_site_index(0, 0) . QUERY_MARKER . 'ACT=' . $action_id->row('action_id') . '&t=' . ee()->localize->now,
                'Rte.filedirUrls' => $filedir_urls
            ]);

            static::$_includedFieldResources = true;
        }
    }

    public function getClass()
    {
        return $this->class;
    }

    protected function insertConfigJsById()
    {
        ee()->lang->loadfile('rte');

        // starting point
        $baseConfig = static::defaultConfigSettings();

        if (!$this->toolset && !empty(ee()->config->item('rte_default_toolset'))) {
            $configId = ee()->config->item('rte_default_toolset');
            $toolsetQuery = ee('Model')->get('rte:Toolset');
            $toolsetQuery->filter('toolset_type', 'redactor');
            if (!empty($configId)) {
                $toolsetQuery->filter('toolset_id', $configId);
            }
            $this->toolset = $toolsetQuery->first();
        }

        if (!empty($this->toolset)) {
            $configHandle = preg_replace('/[^a-z0-9]/i', '_', $this->toolset->toolset_name) . $this->toolset->toolset_id;
            $config = array_merge($baseConfig, $this->toolset->settings);
        } else {
            $config = $baseConfig;
            $configHandle = 'redactordefault0';
        }

        $this->handle = $configHandle;

        // skip if already included
        if (isset(static::$_includedConfigs) && in_array($configHandle, static::$_includedConfigs)) {
            return $configHandle;
        }

        // language
        $language = isset(ee()->session) ? ee()->session->get_language() : ee()->config->item('deft_lang');
        $config['toolbar']['lang'] = ee()->lang->code($language);

        if (!empty(ee()->config->item('site_pages'))) {
            ee()->cp->add_to_foot('<script type="text/javascript">
                EE.Rte.configs.' . $configHandle . '.mention = {"feeds": [{"marker": "@", "feed": getPages, "itemRenderer": formatPageLinks, "minimumCharacters": 3}]};
            </script>');
        }

        // -------------------------------------------
        //  File Browser Config
        // -------------------------------------------
        if (in_array('image', $config['toolbar']['buttons']) || in_array('file', $config['toolbar']['buttons'])) {
            $uploadDir = (isset($config['upload_dir']) && !empty($config['upload_dir'])) ? $config['upload_dir'] : 'all';
            unset($config['upload_dir']);

            $fileBrowserOptions = ['filepicker'];
            if (!empty(ee()->config->item('rte_file_browser'))) {
                array_unshift($fileBrowserOptions, ee()->config->item('rte_file_browser'));
            }
            $fileBrowserOptions = array_unique($fileBrowserOptions);
            foreach ($fileBrowserOptions as $fileBrowserName) {
                $fileBrowserAddon = ee('Addon')->get($fileBrowserName);
                if ($fileBrowserAddon !== null && $fileBrowserAddon->isInstalled() && $fileBrowserAddon->hasRteFilebrowser()) {
                    $fqcn = $fileBrowserAddon->getRteFilebrowserClass();
                    $fileBrowser = new $fqcn();
                    if ($fileBrowser instanceof RteFilebrowserInterface) {
                        $fileBrowser->addJs($uploadDir);

                        break;
                    }
                }
            }

            $config['toolbar']['fileUpload'] = 'admin.php?/cp/addons/settings/filepicker/ajax-upload';
            $config['toolbar']['fileData'] = [
                'csrf_token' => CSRF_TOKEN,
                'directory' => 1,
                'fileUploadParam' => 'file'
            ];
        }

        /*if (stripos($fqcn, 'filepicker_rtefb') !== false && REQ != 'CP') {
            unset($config['image']);
            $config['toolbar']['fileUpload'] = 'admin.php?/cp/addons/settings/filepicker/upload&directory=1';
            $config['toolbar']['fileData'] = ['csrf_token' => CSRF_TOKEN];
            $filemanager_key = array_search('filemanager', $config['toolbar']->items);
            if ($filemanager_key) {
                $items = $config['toolbar']->items;
                unset($items[$filemanager_key]);
                $config['toolbar']->items = array_values($items);
            }
        }*/
        
        if (isset($config['height']) && !empty($config['rheight'])) {
            $config['toolbar']['minHeight'] = (int) $config['height'] . 'px';
        }
        if (isset($config['max_height']) && !empty($config['max_height'])) {
            $config['toolbar']['maxHeight'] = (int) $config['max_height'] . 'px';
        }

        //link
        $config['linkValidation'] = false;
        $config['linkTarget'] = false;

        // -------------------------------------------
        //  JSONify Config and Return
        // -------------------------------------------
        ee()->javascript->set_global([
            'Rte.configs.' . $configHandle => $config['toolbar'],
        ]);

        static::$_includedConfigs[] = $configHandle;

        return $configHandle;
    }

    /**
     * Returns the default config settings.
     *
     * @return array $configSettings
     */
    public static function defaultConfigSettings()
    {

        return array(
            'type' => 'redactor',
            'toolbar' => static::defaultToolbars()['Redactor Basic'],
            'height' => '200',
            'upload_dir' => 'all'
        );
    }

    public function toolbarInputHtml($config)
    {
            ee()->cp->add_to_head('<link rel="stylesheet" href="' . URL_THEMES . 'rte/scripts/redactor/redactor.css" type="text/css" media="print, projection, screen" />');

            ee()->cp->add_js_script([
                'file' => ['cp/form_group'],
            ]);

            $selection = isset($config->settings['toolbar']['buttons']) ? $config->settings['toolbar']['buttons'] : $config->settings['toolbar'];

            $fullToolbar = array_merge($selection, static::defaultToolbars()['Redactor Full']['buttons']);
            $fullToolset = [];
            foreach ($fullToolbar as $i => $tool) {
                $fullToolset[$tool] = lang($tool . '_rte');
            }

            return ee('View')->make('rte:redactor-toolbar')->render(
                [
                    'buttons' => $fullToolset,
                    'selection' => $selection,
                    'type' => 'buttons'
                ]
            );
    }

    public function pluginsInputHtml($config)
    {
            $selection = isset($config->settings['toolbar']['plugins']) ? $config->settings['toolbar']['plugins'] : $config->settings['toolbar'];

            $fullToolbar = array_merge($selection, static::defaultToolbars()['Redactor Full']['plugins']);
            $fullToolset = [];
            foreach ($fullToolbar as $i => $tool) {
                $fullToolset[$tool] = lang($tool . '_rte');
            }

            return ee('View')->make('rte:redactor-toolbar')->render(
                [
                    'buttons' => $fullToolset,
                    'selection' => $selection,
                    'type' => 'plugins'
                ]
            );
    }

    public static function defaultToolbars()
    {
        return [
            'Redactor Basic' => [
                'buttons' => [
                    'bold',
                    'italic',
                    'underline',
                    'ol',
                    'ul',
                    'link',
                ],
                'plugins' => [

                ],
            ],
            'Redactor Full' => [
                'buttons' => [
                    'html',
                    'format',
                    'bold',
                    'italic',
                    'deleted',
                    'underline',
                    'redo',
                    'undo',
                    'ol',
                    'ul',
                    'indent',
                    'outdent',
                    'sup',
                    'sub',
                    //'image',
                    //'file',
                    'link',
                    'line'
                ],
                'plugins' => [
                    'alignment',
                    //'definedlinks',
                    //'filemanager',
                    //'handle',
                    //'imagemanager',
                    'inlinestyle',
                    //'limiter',
                    //'counter',
                    'properties',
                    'specialchars',
                    'table',
                    'video',
                    'widget',
                    'fullscreen',
                ]
            ]
        ];
    }

}
