<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class DownloadAzureBlob extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'azure:download-blobs {container : The name of the container }';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List Blobs in container on the fly';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {

        $container = $this->argument("container");
        $disk = Storage::disk('azure');
        $filesArray = array();
        $fileSizes = array();

        $this->task("Fetching azure blobs in ...$container", function () use ($container, $disk, &$filesArray) {

            try {
                $filesArray = $disk->allFiles($container);
                return true;

            } catch (\Exception $exception) {
                return false;
            }

        });

        $bar = $this->output->createProgressBar(count($filesArray));
        $fileIds = array_keys($filesArray);

        if ($this->confirm('Do you want to show blob sizes?'))
            foreach ($filesArray as $file) {
                $fileSizes[] = filesize_formatted($disk->size($file));
                $bar->advance();
            }

        $bar->finish();

        $headers = ['Id', 'Name', 'Size'];

        $tableRows = collect($filesArray)->map(function($item, $key) use ($fileIds, $fileSizes){
            return [$fileIds[$key], $item, ''];
        });

        $this->table($headers, $tableRows);

        if ($this->confirm('Do you want to download a blob?')) {

            $keepLooping = true;

            do {
                $response = $this->ask('Select a Blob Id to download');
                //if(!is_integer($response) || intval($response) < 0 || intval($response) > count($fileIds))

                if (!array_key_exists($response, $fileIds)) {
                    $this->error("Invalid input. Try again");
                    continue;
                }

                $this->info('Starting download from ' . $filesArray[$response]);
                $contents = $disk->get($filesArray[$response]);

                $this->info('Commencing write to disk' . storage_path());
                Storage::put($filesArray[$response], $contents);

                $this->info("Saved $filesArray[$response]\n");


            } while ($keepLooping);

        }

        $this->comment("All done...");
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
