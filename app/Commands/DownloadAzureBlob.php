<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

class DownloadAzureBlob extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'azure:download-blobs';

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

       // $container = $this->argument("container");

        $disk = Storage::disk('azure');

        $foldersArray = array();

        $this->task("Fetching directories within container", function () use ($disk, &$foldersArray) {

            try {
                $foldersArray = $disk->allDirectories();
                return true;

            } catch (\Exception $exception) {
                return false;
            }

        });

        $dirIdx = $this->menu('Choose Directory', $foldersArray)->open();
    

        if(!is_integer($dirIdx))
            return;

        $directory = $foldersArray[$dirIdx];

        $filesArray = array();
        $fileSizes = array();

        $this->task("Fetching azure blobs in ...$directory", function () use ($directory, $disk, &$filesArray) {

            try {
                $filesArray = $disk->allFiles($directory);
                return true;

            } catch (\Exception $exception) {
                return false;
            }

        });

        $bar = $this->output->createProgressBar(count($filesArray));
        $fileIds = array_keys($filesArray);

        if ($this->confirm('Do you want to show blob sizes?')){

            foreach ($filesArray as $file) {
                $fileSizes[] = $this->filesize_formatted($disk->size($file));
                $bar->advance();
            }

            $bar->finish();

        }

        $headers = ['Id', 'Name', 'Size'];

        $tableRows = collect($filesArray)->map(function($item, $key) use ($fileIds, $fileSizes){

            return [$fileIds[$key], $item, empty($fileSizes) ? 'X' : $fileSizes[$key]];
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

    function filesize_formatted($size)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }
}
