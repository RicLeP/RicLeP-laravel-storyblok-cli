<?php

namespace Riclep\StoryblokCli\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Storyblok\ManagementClient;

class ImportStoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ls:import-story {filename} {slug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import a story from JSON - it will be created in your space’s root';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->client = new ManagementClient(config('storyblok-cli.oauth_token'));

		parent::__construct();
	}

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
		// TODO - interactive console for selecting save folder?

	    $storyExists = $this->client->get('spaces/' . config('storyblok-cli.space_id') . '/stories/', [
			'with_slug' => $this->argument('slug')
	    ])->getBody()['stories'];

		if (!$storyExists) {
			$source = json_decode(Storage::get($this->argument('filename')), true);

			$story = [
				"story" =>  [
					"name" => $source['story']['name'] . ' (Imported)',
					"slug" => $this->argument('slug'),
					"content" => $source['story']['content'],
				],
				"publish" =>  1
			];

			$importedStory = $this->client->post('spaces/' . config('storyblok-cli.space_id') . '/stories/', $story)->getBody()['story'];

			$this->info('Imported into Storyblok: ' . $importedStory['name']);
		} else {
			$this->warn('Story already exists for: ' . $this->argument('slug'));
		}
    }
}
