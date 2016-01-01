<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * DiscussiongroupsUsers Controller
 *
 * @property \App\Model\Table\DiscussiongroupsUsersTable $DiscussiongroupsUsers
 */
class DiscussiongroupsUsersController extends AppController
{

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add($id = null)
    {
        //check for null id
        if($id == null) {
            throw new NotFoundException('An error occurred. ID cannot be null.');
        }
        
        //retrieve tables
        $membersTable = TableRegistry::get('Discussiongroups_users');
        
        /*
         * set up query to find if user is member of discussion group
         * maybe move to table entity
         */
        $findQuery = $membersTable->find('all', [
            'conditions' => [
                'user_id' => $this->Auth->user('id'),
                'discussiongroup_id' => $id
            ]
        ]);
        
        //set up query
        $query = $membersTable->query();
        /*
         * make sure member is not already member of group
         * attempt to insert user as member
         */
        if($findQuery->count() < 1 && $query->insert(['user_id', 'discussiongroup_id'])
            ->values([
                'user_id' => $this->Auth->user('id'),
                'discussiongroup_id' => $id
            ])
            ->execute()) {
            //redirect to view discussion group
            $this->redirect(['controller' => 'discussiongroups', 'action' => 'view', $id]);
        } else {
            //set flash to error message
            $this->Flash->error(__('An error occurred. Please try again.'));
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Discussiongroups User id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($groupId, $userId = null)
    {
        //check for null id
        if($groupId == null) {
            throw new NotFoundException('An error occurred. ID cannot be null');
        }
        
        //allow request to be post/delete
        $this->request->allowMethod(['post', 'delete']);
        
        //retrieve tables
        $discussionGroupUsersTable = TableRegistry::get('DiscussiongroupsUsers');
        
        //default to session user
        if($userId == null) {
            $userId = $this->Auth->user('id');
        }
        
        //TODO: Prevent group creator from leaving group
        
        /*$discussionGroupTable = TableRegistry::get('Discussiongroups');
        if($userId == $discussionGroupTable->find('all', ['conditions' => ['id' => $groupId]])->first()->id) {
            throw new Exception("You cannot leave your own group!");
        }*/
        
        //retrieve record of member
        $discussionGroup = $discussionGroupUsersTable->find('all', [
            'conditions' => [
                'user_id' => $userId,
                'discussiongroup_id' => $groupId
            ]
        ])
                ->first();
        
        //attempt to delete record
        if ($discussionGroupUsersTable->delete($discussionGroup)) {

        } else {
            //set flash to error message
            $this->Flash->error(__('An error occurred. Please try again.'));
        }
        
        //redirect to discussiongroup index
        return $this->redirect(['controller' => 'discussiongroups', 'action' => 'index']);
    }
    
    public function isAuthorized($user) {
        if(in_array($this->request->action, ['delete'])) {
            
            //this does not make sense - always true
            if($user['id'] == $this->Auth->user('id')) {
                return true;
            }
            
            //return parent funcction
            return parent::isAuthorized($user);
        }
        
        //return true for everything else
        return true;
    }
}
