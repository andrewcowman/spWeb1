<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Notifications Controller
 *
 * @property \App\Model\Table\NotificationsTable $Notifications
 */
class NotificationsController extends AppController
{

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        //NOT SURE IF USED
        
        
        //create empty entity
        $notification = $this->Notifications->newEntity();
        //allow request to be post
        if ($this->request->is('post')) {
            //patch request data into entity
            $notification = $this->Notifications->patchEntity($notification, $this->request->data);
            
            //attempt to save notification
            if ($this->Notifications->save($notification)) {
                //redirect to referring page
                return $this->redirect([]);
            } else {
                //set flash to error message
                $this->Flash->error(__('The notification could not be saved. Please, try again.'));
            }
        }
        
        //send notification to view to be filled
        $this->set(compact('notification'));
        $this->set('_serialize', ['notification']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Notification id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        //check for null id
        if($id == null) {
            throw new NotFoundException('An error occurred. =[');
        }
        
        //only allow post/delete requests
        $this->request->allowMethod(['post', 'delete']);
        
        //get record to delete
        $notification = $this->Notifications->get($id);
        
        //attempt to delete record
        if ($this->Notifications->delete($notification)) {

        } else {
            //set flash to error message
            $this->Flash->error(__('An error occurred. Please try again.'));
        }
        
        //redirect to referring page
        return $this->redirect($this->referer());
    }
    
    public function isAuthorized($user) {
        if(in_array($this->request->action, ['delete'])) {
            return true;
        }
    }
}
