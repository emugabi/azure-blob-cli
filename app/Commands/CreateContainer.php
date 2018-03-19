<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use MicrosoftAzure\Storage\Common\ServicesBuilder;

class CreateContainer extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'azure:create-container {name}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create Azure container on the fly.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {

        $name = $this->argument("name");

        if ($this->confirm("Do you wish to continue creating '{$name}''?")) {

            $endpoint = sprintf('DefaultEndpointsProtocol=https;AccountName=%s;AccountKey=%s', env('AZURE_STORAGE_NAME'), env('AZURE_STORAGE_KEY'));
            $blobRestProxy = ServicesBuilder::getInstance()->createBlobService($endpoint);
            $listContainersResult = $blobRestProxy->listContainers();

            $containerExists = false;
            foreach ($listContainersResult->getContainers() as $container) {
                if ($container->getName() == $name) {
                    // The container exists.
                    $containerExists = true;
                    // No need to keep checking.
                    $this->error("Container {$name} already exits. Aborting request.");
                    break;
                }
            }
            if (!$containerExists) {
                $this->info("Creating container.\n");
                $blobRestProxy->createContainer($name);
                $this->info("Container '" . $name . "' successfully created.\n");
            }
        } else
            $this->comment("You chickened out of glory.\n");
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
