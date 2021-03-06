<?php namespace RainLab\BlogVideo;

use Backend;
use Controller;
use Backend\Classes\BackendController;
use System\Classes\PluginBase;
use RainLab\Blog\Controllers\Posts as PostsController;
use RainLab\Blog\Classes\TagProcessor;

class Plugin extends PluginBase
{
    public $require = ['RainLab.Blog'];

    public function pluginDetails()
    {
        return [
            'name'        => 'Blog Video Extension',
            'description' => 'Adds responsive video embedding features to the RainLab Blog module.',
            'author'      => 'Alexey Bobkov, Samuel Georges',
            'icon'        => 'icon-video-camera',
            'homepage'    => 'https://github.com/rainlab/blogvideo-plugin'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     */
    public function register()
    {
        PostsController::extend(function($controller) {
            if (!in_array(BackendController::$action, ['create', 'update'])) {
                return;
            }

            $controller->addJs('/plugins/rainlab/blogvideo/assets/js/blog-video.js');
            $controller->addCss('/plugins/rainlab/blogvideo/assets/css/blog-video.css');
        });

        /*
         * Register the video tag processing callback
         */
        TagProcessor::instance()->registerCallback(function($input, $preview) {
            if (!$preview) {
                return $input;
            }

            $popup = file_get_contents(__DIR__.'/partials/popup.htm');

            return preg_replace('|\<img src="video" alt="([0-9]+)" \/>|m', 
                '<span class="video-placeholder" data-index="$1">
                    <a href="#">Click to embed a video...</a>
                    '.$popup.'
                </span>',
            $input);
        });
    }
}
