<?php

namespace App\Generators;

use App\JiraHrEntities;
use App\User;
use chobie\Jira\Api;
use chobie\Jira\Api\Authentication\Basic;
use Illuminate\Support\Facades\Log;

/**
 * Class HREntityGenerator
 * @package App\Generators
 */
class HREntityGenerator
{
    /**
     * @var
     */
    private $api;

    /**
     * @return bool
     */
    private function initializeJiraClient()
    {
        try {
            $this->api = new Api(config('jira.full_host'), new Basic(config('jira.username'), config('jira.password')), null, ['Host: ' . config('jira.server')]);
        } catch (Api\Exception $exception) {
            return false;
        }
        return true;
    }

    /**
     * @return string
     */
    public function fillTable()
    {
        $users = User::whereNull('deleted_at')->get();

        try {
            foreach ($users as $user) {
                if ($this->initializeJiraClient()) {
                    $result = $this->api->api(
                        'POST',
                        '/rest/api/2/search',
                        [
                            'jql' => 'project = HR AND issuetype = Entity and "status"=active and "User"=' . $user->crowd_key,
                            'fields' => ["issues"]
                        ]
                    );
                    $result = $result->getIssues();
                    if ($result) {
                        $jiraHrEntity = new JiraHrEntities();
                        $jiraHrEntity->user_id = $user->id;
                        $jiraHrEntity->crowd_key = $user->crowd_key;
                        $jiraHrEntity->jira_entity_key = $result[0]->getKey();
                        $jiraHrEntity->save();
                    }
                }
            }
        } catch (Api\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param $key
     * @param $superior
     */
    public function updateManager($key, $superior)
    {
        if ($this->initializeJiraClient()) {
            $createdIssue = $this->api->editIssue($key, ['fields' => ['customfield_10607' => ['name' => $superior]]]);
            if ($createdIssue !== false) {
                Log::error('Jira HR entity update: ' . $createdIssue->getResult()['errors']['customfield_10607']);
            }
        }
    }
}