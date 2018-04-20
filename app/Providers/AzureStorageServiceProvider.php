<?php
/**
 * Created by PhpStorm.
 * User: Pluto
 * Date: 20/06/2017
 * Time: 00:53
 */

namespace App\Providers;


use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Azure\AzureAdapter;
use League\Flysystem\Filesystem;
use MicrosoftAzure\Storage\Common\ServicesBuilder;

class AzureStorageServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */

    public function boot()

    {

        Storage::extend('azure', function ($app, $config) {

            $endpoint = sprintf(

                'DefaultEndpointsProtocol=https;AccountName=%s;AccountKey=%s',

                $config['name'],

                $config['key']

            );

            $blobRestProxy = ServicesBuilder::getInstance()->createBlobService($endpoint);

            return new Filesystem(new AzureAdapter($blobRestProxy, config('filesystems.disk.azure.container')));

        });

    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */

    public function register()

    {

        //

    }
}