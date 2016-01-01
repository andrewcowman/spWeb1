<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;

/**
 * Follows Controller
 *
 * @property \App\Model\Table\FollowsTable $Follows
 */
class FollowsController extends AppController
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
        $followsTable = TableRegistry::get('Follows');
        $notificationsTable = TableRegistry::get('notifications');
        //create new query on follows table
        $query = $followsTable->query();
        //attempt to insert follow
        if($query->insert(['follower_id', 'following_id'])
            ->values([
                'follower_id' => $this->Auth->user('id'),
                'following_id' => $id
            ])
            ->execute()) {
            
            /*
             * insert notification to user being followed
             * 1 - follow notification
             */
            $notificationsTable->insertNotification($id, 1);
            
            /*
             * redirect to referring page
             * change to redirect to $id? 
             */
            $this->redirect($this->referer());
        } else {
            //set flash to error message
            $this->Flash->error(__('An error occurred. Please try again.'));
        }
        
    }


    /**
     * Delete method
     *
     * @param string|null $id Follow id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        //check for null id
        if($id == null) {
            throw new NotFoundException('An error occurred. ID cannot be null.');
        }
        
        //allow request to be post/delete
        $this->request->allowMethod(['post', 'delete']);
        
        //retrieve tables
        $followsTable = TableRegistry::get('Follows');
        $postsTable = TableRegistry::get('posts');
        
        //find record to delete
        $follow = $followsTable->find('all', [
            'conditions' => [
                'follower_id' => $this->Auth->user('id'),
                'following_id' => $id
            ]
        ])
                ->first();
        
        //attempt to delete follow
        if ($followsTable->delete($follow)) {
            
            //delete posts between users
            if($postsTable->deletePostsBetweenUsers($this->Auth->user('id'), $id)) {
                
            } else {
                //set flash to error message
                $this->Flash->error(__('An error occurred. Please try again.'));
            }
            
        } else {
            //set flash to error message
            $this->Flash->error(__('An error occurred. Please try again.'));
        }
        
        //redirect to referring page
        return $this->redirect($this->referer());
    }
    
    public function isAuthorized($user) {
        if(in_array($this->request->action, ['delete'])) {
            //does not make sense
            if($user['id'] == $this->Auth->user('id')) {
                return true;
            }
            return parent::isAuthorized($user);
        }
        return true;
    }
}
