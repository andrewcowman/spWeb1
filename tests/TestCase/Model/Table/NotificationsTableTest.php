<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\NotificationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\NotificationsTable Test Case
 */
class NotificationsTableTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.notifications',
        'app.users',
        'app.posts',
        'app.comments',
        'app.intended_user',
        'app.discussiongroups',
        'app.creators',
        'app.discussiongroups_users'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Notifications') ? [] : ['className' => 'App\Model\Table\NotificationsTable'];
        $this->Notifications = TableRegistry::get('Notifications', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Notifications);

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

    /**
     * Test insertNotification method
     *
     * @return void
     */
    public function testInsertNotification()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
