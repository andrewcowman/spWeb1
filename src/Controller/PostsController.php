<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use App\Model\Entity\Post;

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
        $postsTable = TableRegistry::get('posts');
        $followsTable = TableRegistry::get('follows');
        $notificationsTable = TableRegistry::get('notifications');
        $likesTable = TableRegistry::get('likes');
        
        //set up query to retrieve following ids
        $ids = $followsTable->find('all', [
            'fields' => [
                'following_id'
            ],
            'conditions' => [
                'follower_id' => $this->Auth->user('id')
            ]
        ]);
        
        //retrieve notifications
        $notifications = $notificationsTable->find('all', [
           'fields' => [
               'id',
               'nl.description'
           ],
           'join' => [
               'nl' => [
                   'table' => 'notifications_lookup',
                   'type' => 'inner',
                   'conditions' => 'notification_id = nl.id'
               ]
           ],
            'conditions' => [
                'user_id' => $this->Auth->user('id')
            ]
        ]);
        
        
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
        
        /*$posts = $postsTable->find('all', [
            'fields' => [
                'posts.id',
                'posts.content',
                'posts.user_id',
                'posts.created',
                'author.id',
                'author.first_name',
                'author.last_name',
                'f.first_name',
                'f.last_name'
            ],
            'join' => [
                'author' => [
                    'table' => 'users',
                    'type' => 'inner',
                    'conditions' => 'posts.user_id = author.id'
                ],
                'f' => [
                    'table' => 'users',
                    'type' => 'left',
                    'conditions' => 'posts.for_user_id = f.id'
                ]
            ],
            'conditions' => [
                'OR' => [
                    'posts.user_id' => $this->Auth->user('id'),
                    'posts.for_user_id' => $this->Auth->user('id'),
                    'posts.user_id IN' => $ids
                ],
                'group_id IS NULL'
            ],
            'page' => 10,
            'order' => [
                'created' => 'desc'
            ]
        ])->toArray();
        
        foreach($posts as $post) {
            $post['likeId'] = $likesTable->find('all', [
                'fields' => [
                    'id'
                ],
                'conditions' => [
                    'post_id' => $post['id'],
                    'user_id' => $this->Auth->user('id')
                ]
            ])->first();
        }*/
        
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
            'fields' => [
                'posts.id',
                'posts.content',
                'posts.user_id',
                'posts.created',
                'author.id',
                'author.first_name',
                'author.last_name',
                'f.first_name',
                'f.last_name'
            ],
            'join' => [
                'author' => [
                    'table' => 'users',
                    'type' => 'inner',
                    'conditions' => 'posts.user_id = author.id'
                ],
                'f' => [
                    'table' => 'users',
                    'type' => 'left',
                    'conditions' => 'posts.for_user_id = f.id'
                ]
            ],
            'conditions' => [
                'OR' => [
                    'user_id' => $this->Auth->user('id'),
                    'for_user_id' => $this->Auth->user('id'),
                    'user_id IN' => $ids
                ],
                'group_id IS NULL'
            ],
            /*'contain' => ['Users', 'IntendedUser', 'UsersLiked' => [
                'conditions' => [
                    'user_id' => $this->Auth->user('id')
                ]
            ]],*/
            'order' => [
                'created' => 'desc'
            ]
        ];
        
        $posts = $this->paginate($this->Posts);
        
        foreach($posts as $post) {
            $post['likeId'] = $likesTable->find('all', [
                'fields' => [
                    'id'
                ],
                'conditions' => [
                    'post_id' => $post['posts']['id'],
                    'user_id' => $this->Auth->user('id')
                ]
            ])->first();
        }
        
        //send notifications to view
        $this->set(compact('posts', 'notifications'));
        
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
            throw new NotFoundException('An error occurred. ID cannot be null.');
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
                
                //redirect to posts index
                return $this->redirect(['action' => 'index']);
            } else {
                //set flash to error message
                $this->Flash->error(__('An error occurred. Please try again.'));
            }
        }
        
        
        //send post to view
        $this->set(compact('post'));
        $this->set('_serialize', ['post']);
    }
    
    public function addPostForUser($forUserId) {
        //check for null id
        if($forUserId == null) {
            throw new NotFoundException('An error occurred. ID cannot be null.');
        }
        
        //retrieve tables
        $notificationsTable = TableRegistry::get('notifications');
        $followsTable = TableRegistry::get('follows');
        
        //only allow posts on others pages if
        //person1 is following person2 AND person2 is following person1
        if(!$followsTable->isMutualRelationship($this->Auth->user('id'), $forUserId)) {
            $this->Flash->error(__('I\'m sorry, you must each be following each other to post on their page.'));
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
                    
                return $this->redirect(['controller' => 'users', 'action' => 'view', $forUserId]);
            } else {
                //set flash to error message
                $this->Flash->error(__('An error occurred. Please try again.'));
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
            throw new NotFoundException('An error occurred. ID cannot be null.');
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
                //redirect to group page
                return $this->redirect(['controller' => 'discussiongroups', 'action' => 'view', $groupId]);
            } else {
                //set flash to error message
                $this->Flash->error(__('An error occurred. Please try again.'));
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
            throw new NotFoundException('An error occurred. ID cannot be null.');
        }
        
        //allow post/delete requests
        $this->request->allowMethod(['post', 'delete']);
        //retrieve post to delete
        $post = $this->Posts->get($id);
        
        //attempt to delete post
        if ($this->Posts->delete($post)) {

        } else {
            //set flash to error message
            $this->Flash->error(__('An error occurred. Please try again.'));
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
