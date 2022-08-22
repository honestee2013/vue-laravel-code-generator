<?php

namespace Honestee\VueCodeGen;

use Illuminate\Support\ServiceProvider;

class VueApiServiceProvider extends ServiceProvider
{
  
  protected $commands = [
      'Honestee\VueCodeGen\Generate'
  ];
  
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
      
      $this->mergeConfigFrom(
          __DIR__ . '/config/vueApi.php', 'vueApi'
      );
    
        $this->commands($this->commands);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {

        
         $this->loadViewsFrom(__DIR__.'/templates', 'vueApi');
        
        $this->publishes([
             __DIR__ . '/config/vueApi.php' => config_path('vueApi.php'),
         ], 'config');
         
    
        
        $this->publishes([
         __DIR__.'/templates' => resource_path('views/vendor/vueApi'),
       ],'templates');
         
    }
    
    
    
}
