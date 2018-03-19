<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use MicrosoftAzure\Storage\Common\ServicesBuilder;

class DeleteContainer extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'delete-container {name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Delete Azure container on the fly.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $name = $this->argument("name");

        if ($this->confirm("Do you wish to continue deleting '{$name}''?")) {

            $endpoint = sprintf('DefaultEndpointsProtocol=https;AccountName=%s;AccountKey=%s', env('AZURE_STORAGE_NAME'), env('AZURE_STORAGE_KEY'));
            $blobRestProxy = ServicesBuilder::getInstance()->createBlobService($endpoint);
            $listContainersResult = $blobRestProxy->listContainers();

            $containerExists = false;
            foreach ($listContainersResult->getContainers() as $container) {
                if ($container->getName() == $name) {
                    // The container exists.
                    $containerExists = true;
                    break;
                }
            }
            if ($containerExists) {
                $blobRestProxy->deleteContainer($name);
                $this->info("Container '" . $name . "' successfully deleted.\n");
            } else
                $this->error("Container '" . $name . "' not found.");
        } else
            $this->comment("You saved the chickens.\n");
    }

    /**
	 * Define the command's schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule $schedule
	 *
	 * @return void
	 */
	public function schedule(Schedule $schedule): void
	{
		// $schedule->command(static::class)->everyMinute();
	}
}
