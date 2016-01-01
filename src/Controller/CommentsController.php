<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * Comments Controller
 *
 * @property \App\Model\Table\CommentsTable $Comments
 */
class CommentsController extends AppController
{

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add($postId = null)
    {
        //check for null id
        if($postId == null) {
            throw new NotFoundException('An error occurred. =[');
        }
        
        //get tables
        $postsTable = TableRegistry::get('posts');
        $notificationsTable = TableRegistry::get('notifications');
        
        //create new entity
        $comment = $this->Comments->newEntity();
        
        //if request is a post
        if ($this->request->is('post')) {
            //patch the entity
            $comment = $this->Comments->patchEntity($comment, $this->request->data);
            
            //set comment data
            $comment->user_id = $this->Auth->user('id');
            $comment->post_id = $postId;
            
            //save comment
            if ($this->Comments->save($comment)) {
                
                /*
                 * if post id is not null and notified user isn't session user then
                 * notify posts creator about new comment
                 * 2 - new comment notification id
                 */
                $notifiedUser = $postsTable->findAllById(51)->first()->user_id;
                if($postId != null && $notifiedUser != $this->Auth->user('id')) {
                    $notificationsTable->insertNotification($notifiedUser, 2);
                }
                
                //redirect to view post
                return $this->redirect(['controller' => 'posts', 'action' => 'view', $postId]);
            } else {
                //set flash message to error message
                $this->Flash->error(__('An error occurred. Please, try again.'));
            }
        }
        
        //send empty comment to be filled in view
        $this->set(compact('comment'));
        //$this->set('_serialize', ['comment']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Comment id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        //check for null id
        if($id == null) {
            throw new NotFoundException('An error occurred. Please try again.');
        }
        
        //only allow post/delete requests
        $this->request->allowMethod(['post', 'delete']);
        
        //get comment to be deleted
        $comment = $this->Comments->get($id);
        
        //if delete is successful
        if ($this->Comments->delete($comment)) {

        } else {
            //set flash to error message
            $this->Flash->error(__('An error occurred. Please try again.'));
        }
        
        //redirect to view post
                return $this->redirect(['controller' => 'posts', 'action' => 'view', $id]);
    }
    
    public function isAuthorized($user) {
        //if request is to edit or delete comment
        if(in_array($this->request->action, ['edit', 'delete'])) {
            //make sure comment is owned by user attempting to edit/delete
            if($this->Comments->isOwnedBy($this->request->params['pass'][0], $user['id'])) {
                return true;
            }
            return parent::isAuthorized($user);
        }
        
        //return true for everything else
        return true;
    }
}
