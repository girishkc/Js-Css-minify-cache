<?php
set_time_limit(0);


class JsCssLoader
{

    /**
     * @var string Javascript source path
     */
    private $_js_source;

    /**
     * @var string CSS source path
     */
    private $_css_source;

    /**
     * @var string cached file path [absolute path]
     */
    private $_cache_path;

    /**
     * @var string file read path [relative]
     */
    private $_read_path;

    /**
     * @var cache file name
     */
    private $_file_name;

    /**
     * @var mixed Current working directory
     */
    private $_working_dir;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_js_source   = dirname(__DIR__) . '/static_files/js/';
        $this->_css_source  = dirname(__DIR__) . '/static_files/css/';
        $this->_cache_path  = dirname(__DIR__) . '/static_files/cache/';
        $this->_read_path   = dirname(__DIR__) . '/static_files/cache/';
        //$this->_working_dir = str_replace('\\', '_', str_replace(ROOT, '', getcwd()));

    }


    /**
     * Function to get the matching file name
     *
     * @param string $haystack file name
     * @param string $needle   starting string
     *
     * @return bool
     */
    private function _startsWith( $haystack, $needle )
    {
        return $needle === "" || strpos($haystack, $needle) === 0;
    }


    /**
     * Function to delete the old cached files
     *
     * @param string $extn file extension
     *
     * @return void
     */
    private function _deleteOldCachedFile( $extn )
    {
        $dircontent = scandir($this->_cache_path);
        foreach ($dircontent as $filename)
        {
            if (pathinfo($filename, PATHINFO_EXTENSION) == $extn &&
                $this->_startsWith($filename, $this->_working_dir . '_')
            )
            {
                unlink($this->_cache_path . $filename);
            }
        }
    }


    /**
     * Function to check the folder modified time and md5 of it
     *
     * @param string $folder source folder name
     *
     * @return bool|string
     */
    private function _md5OfDir( $folder )
    {
        $dircontent = scandir($folder);
        $ret        = '';
        foreach ($dircontent as $filename)
        {
            if ($filename != '.' && $filename != '..')
            {
                if (filemtime($folder . $filename) === FALSE)
                {
                    return FALSE;
                }
                $ret .= date("YmdHis", filemtime($folder . $filename)) . $filename;
            }
        }
        return md5($ret);
    }


    /**
     * Function to load the css files
     *
     * @param array $files list of files to be included
     *
     * @return void
     */
    public function loadCssFiles( $files )
    {
        $this->_file_name = $this->_working_dir . '_' . $this->_md5OfDir($this->_css_source) . '.css';

        if (!file_exists($this->_cache_path . $this->_file_name))
        {
            $this->_minifyCssFiles($files);
        }

        echo '<link type="text/css" rel="stylesheet" href="' . $this->_read_path . $this->_file_name . '" ></link>';
    }


    /**
     * Function to load the JS files
     *
     * @param array $files list of files to be included
     *
     * @return void
     */
    public function loadJsFiles( $files )
    {
        $this->_file_name = $this->_working_dir . '_' . $this->_md5OfDir($this->_js_source) . '.js';

        if (!file_exists($this->_cache_path . $this->_file_name))
        {
            $this->_minifyJsFiles($files);
        }

        echo '<script type="text/javascript" src="' . $this->_read_path . $this->_file_name . '" ></script>';
    }


    /**
     * Function to read all JS files and minify
     *
     * @param array $files list of files to be included
     *
     * @return void
     */
    private function _minifyJsFiles( $files )
    {
        $this->_deleteOldCachedFile('js');
        $js = '';

        foreach ($files as $file)
        {
            $js .= file_get_contents($this->_js_source . $file);
        }

        //minify the JS
        $js = JSMin::minify($js);
        $this->_writeCacheFile($js);
    }


    /**
     * Function to read all CSS files and minify
     *
     * @param array $files list of files to be included
     *
     * @return void
     */
    private function _minifyCssFiles( $files )
    {
        $this->_deleteOldCachedFile('css');
        $css = '';

        foreach ($files as $file)
        {
            $css .= file_get_contents($this->_css_source . $file);
        }

        //minify the css
        $css = CssMin::minify($css);
        $this->_writeCacheFile($css);
    }


    /**
     * Function to write the cache file
     *
     * @param string $content minified content
     *
     * @return void
     */
    private function _writeCacheFile( $content )
    {
        file_put_contents($this->_cache_path . $this->_file_name, $content);
    }


}