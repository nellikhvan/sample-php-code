<?php

namespace App\Console\Commands;

use App\Generators\HREntityGenerator;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateJiraEntityManager extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backoffice:update_jira_entity_manager';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update manager field in Jira HR entity';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        try {
            $entities = DB::table('jira_hr_entities')->get();

            foreach ($entities as $entity) {
                $superior = DB::select(
                    'SELECT crowd_key FROM  users WHERE id = (SELECT superior_id FROM users WHERE id=:id)',
                    ['id' => $entity->user_id]
                )[0];
                if ($superior) {
                    $generator = new HREntityGenerator();
                    $generator->updateManager($entity->jira_entity_key, $superior->crowd_key);
                }
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
