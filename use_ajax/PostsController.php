<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use App\Model\Entity\Post;
use Cake\Log\Log;

/**
 * Posts Controller
 *
 * @property \App\Model\Table\PostsTable $Posts
 */
class PostsController extends AppController
{
    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        //retrieve tables
        $follows = TableRegistry::get('follows');
        $notificationsTable = TableRegistry::get('notifications');
        
        //set up query to retrieve following ids
        $ids = $follows->find('all', [
            'fields' => [
                'following_id'
            ],
            'conditions' => [
                'follower_id' => $this->Auth->user('id')
            ]
        ]);
        
        //retrieve notifications
        $notifications = $notificationsTable->find('all')
                ->select(['id', 'nl.description'])
                ->join([
                    'nl' => [
                        'table' => 'notifications_lookup',
                        'type' => 'INNER',
                        'conditions' => 'notification_id = nl.id'
                    ]
                ])
                ->where(['user_id' => $this->Auth->user('id')]);
        
        
        //TODO: Have group posts show up in index
        
        /*$groupUsersTable = TableRegistry::get('DiscussiongroupsUsers');
        $groupIds = $groupUsersTable->find('all', [
            'fields' => [
                'discussiongroup_id'
            ],
            'conditions' => [
                'user_id' => $this->Auth->user('id')
            ]
        ])*/
        
        
        /*
         * set up paginate
         * .posts by user
         * .posts for user
         * .posts by users that user is following
         * .not having group posts show up
         * ..contain creator of post and user post is meant for
         * .. order by date created desc
         */
        $this->paginate = [
            'conditions' => [
                'OR' => [
                    'user_id' => $this->Auth->user('id'),
                    'for_user_id' => $this->Auth->user('id'),
                    'user_id IN' => $ids
                ],
                'group_id IS NULL'
            ],
            'contain' => ['Users', 'IntendedUser'],
            'order' => [
                'created' => 'desc'
            ]
        ];
        
        //send posts to view
        $this->set('posts', $this->paginate($this->Posts));
        
        //send notifications to view
        $this->set(compact('notifications'));
        
        $this->set('_serialize', ['posts']);
    }

    /**
     * View method
     *
     * @param string|null $id Post id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        //check for null id
        if($id == null) {
            throw new NotFoundException('An error occurred. =[');
        }
        
        /*
         * get post to view
         * .contain creator and all comments
         */
        $post = $this->Posts->get($id, [
            'contain' => ['Users', 'Comments']
        ]);
        
        //send post to view
        $this->set('post', $post);
        $this->set('_serialize', ['post']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        //retrieve tables
        //$notificationsTable = TableRegistry::get('notifications');
        
        //create new entity
        $post = $this->Posts->newEntity();
        
        //only add when request is post
        if ($this->request->is('post')) {
            //patch request data into entity
            $post = $this->Posts->patchEntity($post, $this->request->data);
            //default user_id to session user
            $post->user_id = $this->Auth->user('id');
            
            //if posting on own page, set forUserId = null
            //if($forUserId == $post->user_id) {
            //    $forUserId = null;
            //}
            
            //if post is for other user, call addPostForUser
            /*if($forUserId != null) {
                if($this->addPostForUser($post, $forUserId)) {
                    //set flash to success message
                    $this->Flash->success(__('Thanks for sharing! :)'));
                    
                    //redirect to posts index
                    return $this->redirect(['action' => 'index']);
                } else {
                    //set flash to error message
                    $this->Flash->error(__('An error occurred. Please try again. :('));
                    return $this->redirect(['action' => 'index']);
                }
                
            }*/
            
            //set for_user_id
            //$post->for_user_id = $forUserId;
            
            //attempt to save post
            if ($this->Posts->save($post)) {
                
                //if post has for_user_id, insert notification for that user
                /*if($forUserId != null) {
                    $notificationsTable->insertNotification($forUserId, 3);
                }*/
                
                //set flash to success message
                $this->Flash->success(__('Thanks for sharing! :)'));
                
                //redirect to posts index
                return $this->redirect(['action' => 'index']);
            } else {
                //set flash to error message
                $this->Flash->error(__('An error occurred. Please try again. :('));
            }
        }
        
        
        //send post to view
        $this->set(compact('post'));
        $this->set('_serialize', ['post']);
    }
    
    public function add_ajax() {
        
        if($this->request->is('ajax')) {
            
            $this->layout = 'ajax';
            //Prevent view from being called
            $this->autoRender = false;

            //$postsTable = TableRegistry::get('posts');

            $post = $this->Posts->newEntity();

            //$post = $this->Posts->patchEntity($this->request->data);

            $post->content = 'blafiweh';
            $post->user_id = 9;

            //$post = $this->Posts->patchEntity($post, $this->request->data);

            //echo $post->content;
            
            $this->Posts->save($post);
            Log::write('debug', 'test');
            echo $post->content;

            //echo $post->content;
            /*if($this->Posts->save($post)) {
                echo $post->content;
            } else {
                echo 'error';
            }*/
        }
    }
    
    public function addPostForUser($forUserId) {
        //check for null id
        if($forUserId == null) {
            throw new NotFoundException('An error occurred. =[');
        }
        
        //retrieve tables
        $notificationsTable = TableRegistry::get('notifications');
        $followsTable = TableRegistry::get('follows');
        
        //only allow posts on others pages if
        //person1 is following person2 AND person2 is following person1
        if(!$followsTable->isMutualRelationship($this->Auth->user('id'), $forUserId)) {
            $this->Flash->error(__('I\'m sorry, you must each be following each other to post on their page. :('));
            return $this->redirect($this->referer());
        }
        
        //create new entity
        $post = $this->Posts->newEntity();
        
        if($this->request->is('post')) {
            //patch request data into entity
            $post = $this->Posts->patchEntity($post, $this->request->data);
            
            //default user_id to session user
            $post->user_id = $this->Auth->user('id');
            //set for_user_id
            $post->for_user_id = $forUserId;

            if($this->Posts->save($post)) {

                //insert notification for user
                $notificationsTable->insertNotification($forUserId, 3);
                    
                //set flash to success message
                $this->Flash->success(__('Thanks for sharing! :)'));
                return $this->redirect(['controller' => 'users', 'action' => 'view', $forUserId]);
            } else {
                //set flash to error message
                $this->Flash->error(__('An error occurred. Please try again. :('));
            }
        }
        
        //send post to view
        $this->set(compact('post'));
        $this->set('_serialize', ['post']);
    }
    
    public function addGroupPost($groupId) {
        //no view, so do not render
        $this->render = false;
        
        //check for null id
        if($groupId == null) {
            throw new NotFoundException('An error occurred. =[');
        }
        
        //only add on post request
        if($this->request->is('post')) {
            //create empty entity
            $post = $this->Posts->newEntity();
            //patch request data into entity
            $post = $this->Posts->patchEntity($post, $this->request->data);
            
            //set defaults
            //user_id = session user
            $post->user_id = $this->Auth->user('id');
            //group_id = passed parameter
            $post->group_id = $groupId;
            
            //attempt to save post
            if($this->Posts->save($post)) {
                //set flash to success message
                $this->Flash->success(__('Thanks for sharing! :)'));
                //redirect to group page
                return $this->redirect(['controller' => 'discussiongroups', 'action' => 'view', $groupId]);
            } else {
                //set flash to error message
                $this->Flash->error(__('An error occurred. Please try again. :('));
            }
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Post id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        //check for null id
        if($id == null) {
            throw new NotFoundException('An error occurred. =[');
        }
        
        //allow post/delete requests
        $this->request->allowMethod(['post', 'delete']);
        //retrieve post to delete
        $post = $this->Posts->get($id);
        
        //attempt to delete post
        if ($this->Posts->delete($post)) {
            //set flash to success message
            $this->Flash->success(__('Your post has successfully been deleted. :('));
        } else {
            //set flash to error message
            $this->Flash->error(__('An error occurred. Please try again. :('));
        }
        //redirect to posts index
        return $this->redirect(['action' => 'index']);
    }
    
    public function isAuthorized($user) {
        //retrieve tables
        $groupUsersTable = TableRegistry::get('DiscussiongroupsUsers');
        
        
        if(in_array($this->request->action, ['edit', 'delete'])) {
            
            //return true if post is owned by user
            if($this->Posts->isOwnedBy($this->request->params['pass'][0], $user['id'])) {
                return true;
            }
            
            return parent::isAuthorized($user);
        } elseif(in_array($this->request->action, ['addGroupPost'])) {
            
            //make sure user is member of group before adding post
            if($groupUsersTable->find('all', [
                'conditions' => [
                    'user_id' => $user['id'],
                    'discussiongroup_id' => $this->request->params['pass'][0]
                ]
            ])
                    ->first()) {
                return true;
            }
            
            return parent::isAuthorized($user);
        }
        
        //return true for everything else
        return true;
    }
}
