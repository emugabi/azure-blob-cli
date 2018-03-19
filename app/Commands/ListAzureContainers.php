<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use MicrosoftAzure\Storage\Common\ServicesBuilder;

class ListAzureContainers extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'azure:list-containers';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'List Azure containers on the fly';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->comment("Fetching azure containers...");
        $endpoint = sprintf('DefaultEndpointsProtocol=https;AccountName=%s;AccountKey=%s', env('AZURE_STORAGE_NAME'), env('AZURE_STORAGE_KEY'));

        $blobRestProxy = ServicesBuilder::getInstance()->createBlobService($endpoint);
        $listContainersResult = $blobRestProxy->listContainers();
        $i = 0;
        foreach ($listContainersResult->getContainers() as $container) {
            ++$i;
            echo "\n$i. " . $container->getName();
        }
        echo "\n\n";
    
        $this->comment("All done...");
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
