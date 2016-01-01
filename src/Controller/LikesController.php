<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * Likes Controller
 *
 * @property \App\Model\Table\LikesTable $Likes
 */
class LikesController extends AppController
{

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add($postId)
    {
        //retrieve table objects
        $notificationsTable = TableRegistry::get('notifications');
        $postsTable = TableRegistry::get('posts');
        $followsTable = TableRegistry::get('follows');
        
        //get associated post
        $post = $postsTable->findById($postId)->first();
        
        //is mutual relationship
        if(!$followsTable->isMutualRelationship($this->Auth->user('id'), $post->user_id)) {
            return $this->redirect($this->referer());
        }
        
        //create new like entity
        $like = $this->Likes->newEntity();
        
        //set like fields
        $like->user_id = $this->Auth->user('id');
        $like->post_id = $postId;
        
        //attempt to save like entity
        if ($this->Likes->save($like)) {
            //send notification to user
            $notificationsTable->insertNotification($post->user_id, 4);
            //redirect to referer
            return $this->redirect($this->referer());
        } else {
            //set flash to error message
            $this->Flash->error(__('An error occurred. Please try again.'));
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Like id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        //retrieve table objects
        $likesTable = TableRegistry::get('likes');
        
        $this->request->allowMethod(['post', 'delete']);
        
        if(!$likesTable->isOwnedBy($id, $this->Auth->user('id'))) {
            return $this->redirect($this->referer());
        }
        
        $like = $this->Likes->get($id);
        if ($this->Likes->delete($like)) {
            
        } else {
            $this->Flash->error(__('The like could not be deleted. Please try again.'));
        }
        return $this->redirect($this->referer());
    }
    
    public function isAuthorized($user) {
        
        if(in_array($this->request->action, ['add', 'delete'])) {
            //auth->user(id) == likes->user_id
            return true;
        }
        
        return parent::isAuthorized($user);
    }
}
