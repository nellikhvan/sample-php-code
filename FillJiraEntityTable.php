<?php

namespace App\Console\Commands;

use App\Generators\HREntityGenerator;
use App\JiraHrEntities;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FillJiraEntityTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backoffice:fill_jira_entity_table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill table jira_hr_entities in backoffice';

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
            DB::table('jira_hr_entities')->truncate();
            $jiraHrEntities = new HREntityGenerator();
            $jiraHrEntities->fillTable();
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }
}
