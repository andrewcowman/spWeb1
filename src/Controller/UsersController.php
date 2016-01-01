<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    
    public function login() {
        //if passed through form
        if($this->request->is('post')) {
            
            //attempt to identify
            $user = $this->Auth->identify();
            
            if($user) {
                //set session user to current user
                $this->Auth->setUser($user);
                //redirect to redirectUrl
                return $this->redirect($this->Auth->redirectUrl());
            }
            //else set flash to error message
            $this->Flash->error('Your username and/or password is incorrect.');
        }
    }
    
    
    public function logout() {
        //set flash to success message
        $this->Flash->success('You are now logged out! Come back soon!');
        //redirect to logout redirect
        return $this->redirect($this->Auth->logout());
    }

    /**
     * View method
     *
     * @param string|null $id User id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        //check for null id
        if($id == null) {
            throw new NotFoundException('An error occurred. ID cannot be null.');
        }
        
        //retrieve tables
        $followsTable = TableRegistry::get('Follows');
        $postsTable = TableRegistry::get('Posts');
        $usersTable = TableRegistry::get('users');
        $likesTable = TableRegistry::get('likes');
        
        /*
         * get user to view
         * .contain following users
         * .contain follower users
         */
        /*$user = $this->Users->get($id, [
            'contain' => ['Followings', 'Followers']
        ]);*/
        
        $user = $usersTable->find('all', [
            'fields' => [
                'users.id',
                'users.email',
                'users.first_name',
                'users.last_name',
            ],
            'conditions' => [
                'users.id' => $id
            ]
        ])->first();
        
        $user['show'] = $usersTable->displayWhat($this->Auth->user('id'), $id);
        
        //set up query to see if session user is following view user
        $ids = $followsTable->find('all', [
            'fields' => [
                'following_id'
            ],
            'conditions' => [
                'follower_id' => $this->Auth->user('id'),
                'following_id' => $id
            ]
        ]);
        
        //set is_following variable
        if($ids->first()) {
            $user['isFollowing'] = true;
        } else {
            $user['isFollowing'] = false;
        }
        
        //set up query to find all posts for user - group_id = NULL
        /*$posts = $postsTable->find('all', [
           'contain' => ['Users', 'UsersLiked'],
           'conditions' => [
               'OR' => [
                   'user_id' => $id,
                   'for_user_id' => $id
               ],
               'group_id IS NULL'
           ],
            'order' => [
                'created' => 'desc'
            ]
        ]);*/
        $this->paginate = [
            'fields' => [
               'posts.id',
               'posts.content',
               'posts.created',
               'a.first_name',
               'a.last_name'
           ],
           'join' => [
               'a' => [
                   'table' => 'users',
                   'type' => 'inner',
                   'conditions' => 'posts.user_id = a.id'
               ]
           ],
           'conditions' => [
               'OR' => [
                   'posts.user_id' => $id,
                   'posts.for_user_id' => $id
               ],
               'posts.group_id IS NULL'
           ],
           'order' => [
               'posts.created' => 'desc'
           ]
        ];
        
        $posts = $this->paginate($postsTable);
        
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
        
        $followings = $usersTable->find('all', [
            'fields' => [
                'users.id',
                'users.first_name',
                'users.last_name'
            ],
            'join' => [
                'f' => [
                    'table' => 'follows',
                    'type' => 'inner',
                    'conditions' => [
                        'users.id = f.following_id'
                    ]
                ]
            ],
            'conditions' => [
                'f.follower_id' => $id
            ],
            'limit' => 6
        ]);
        
        $followers = $usersTable->find('all', [
            'fields' => [
                'users.id',
                'users.first_name',
                'users.last_name'
            ],
            'join' => [
                'f' => [
                    'table' => 'follows',
                    'type' => 'inner',
                    'conditions' => [
                        'users.id = f.follower_id'
                    ]
                ]
            ],
            'conditions' => [
                'f.following_id' => $id
            ],
            'limit' => 6
        ]);
        
        //send variables to view
        $this->set(compact('user', 'posts', 'followings', 'followers'));
        $this->set('_serialize', ['user', 'posts']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        //create empty entity
        $user = $this->Users->newEntity();
        
        //only add if request is post
        if ($this->request->is('post')) {
            //patch request data into entity
            $user = $this->Users->patchEntity($user, $this->request->data);
            
            //attempt to save entity
            if ($this->Users->save($user)) {
                //set flash to success message
                $this->Flash->success('You are now successfully registered!');
                
                //log in newly created user
                $this->Auth->setUser($user->toArray());
                
                //move profile pic to location on server
                // - MOVING TO EDIT ONLY
                /*$path = processImage($this->request->data['image']['name']);
                
                $user = */
                
                //redirect to login redirectUrl
                return $this->redirect($this->Auth->redirectUrl());
            } else {
                //set flash to error message
                $this->Flash->error('An error occurred. Please try again.');
            }
        }
        
        //send empty entity to view
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        //check for null id
        if($id == null) {
            throw new NotFoundException('An error occurred. ID cannot be null.');
        }
        
        //get user to be edited
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        
        //only edit if request is patch, post, put
        if ($this->request->is(['patch', 'post', 'put'])) {
            
            //patch request data into entity
            $user = $this->Users->patchEntity($user, $this->request->data);
            
            //set path to profile picture after processing image
            $user->profile_pic_path = $this->processImage($this->request->data);
            
            //attempt to save user
            if ($this->Users->save($user)) {
                //set flash to success message
                $this->Flash->success(__('Your changes have been saved!'));
                
                //redirect to view
                return $this->redirect(['controller' => 'users', 'action' => 'view', $id]);
            } else {
                //set flash to error message
                $this->Flash->error(__('An error occurred. Please try again.'));
            }
        }
        
        //send user to view
        $this->set(compact('user'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        //check for null id
        if($id == null) {
            throw new NotFoundException('An error occurred. ID cannot be null.');
        }
        
        //only allow post/delete requests
        $this->request->allowMethod(['post', 'delete']);
        
        //get user to be deleted
        $user = $this->Users->get($id);
        
        //attempt to delete user AND LOG OUT FIRST or just clear session maybe
        if ($this->Users->delete($user)) {
            //set flash to success message
            $this->Flash->success(__('Your account has successfully been deleted.'));
        } else {
            //set flash to error message
            $this->Flash->error(__('An error occurred. Please try again.'));
        }
        
        //redirect to register
        return $this->redirect(['controller' => 'users', 'action' => 'add']);
    }
    
    public function search() {
        //retrieve tables
        $usersTable = TableRegistry::get('users');
        
        //get search criteria
        $criteria = $this->request->query['search'];
        
        //query to find all relevent users
        $users = $usersTable->find('all', [
            'conditions' => [
                'OR' => [
                    'first_name LIKE' => '%' . $criteria . '%',
                    'last_name LIKE' => '%' . $criteria . '%'
                ]
            ]
        ]);
        
        //send users to view
        $this->set('users', $users);
    }
    
    public function beforeFilter(\Cake\Event\Event $event) {
        //allow login, register, logout view access while not signed in
        $this->Auth->allow(['login', 'add', 'logout']);
    }
    
    public function isAuthorized($user) {
        if(in_array($this->request->action, ["edit", "delete"])) {
            //always true?
            if($user['id'] == $this->Auth->user('id')) {
                return true;
            }
            return parent::isAuthorized($user);
        }
        return true;
    }
    
    
    
    
    private function processImage($data = null) {
        //if no data, return null
        if($data == null) {
            return null;
        }
        
        //set path to correct path
        $path = WWW_ROOT . 'img' . DS . 'profile_pics' . DS . $this->Auth->user('id') . DS . $data['image']['name'];
        
        //if user does not have a folder, make one
        if(!is_dir(WWW_ROOT . 'img' . DS . 'profile_pics' . DS . $this->Auth->user('id'))) {
            mkdir(WWW_ROOT . 'img' . DS . 'profile_pics' . DS . $this->Auth->user('id'));
        }
        //move image to folder
        move_uploaded_file($data['image']['tmp_name'], $path);
        
        //set folder to relative so html does not have a stroke
        $path = "../../webroot/img/profile_pics/" . $this->Auth->user('id') . DS . $data['image']['name'];
        
        //return relative path
        return $path;
    }
}
