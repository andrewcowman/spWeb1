<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UserSettingsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\UserSettingsTable Test Case
 */
class UserSettingsTableTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.user_settings',
        'app.users',
        'app.posts',
        'app.comments',
        'app.intended_user',
        'app.discussiongroups',
        'app.creators',
        'app.discussiongroups_users',
        'app.settings',
        'app.users_liked'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('UserSettings') ? [] : ['className' => 'App\Model\Table\UserSettingsTable'];
        $this->UserSettings = TableRegistry::get('UserSettings', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->UserSettings);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
