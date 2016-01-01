<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Model\Entity\Post;
use Cake\ORM\TableRegistry;
use Cake\Log\Log;

/**
 * Discussiongroups Controller
 *
 * @property \App\Model\Table\DiscussiongroupsTable $Discussiongroups
 */
class DiscussiongroupsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        //retrieve tables
        $groupsTable = TableRegistry::get('discussiongroups');
        
        //set paginate to include creators of discussion group
        $this->paginate = [
            'limit' => 10
        ];

        $joinedGroups = $groupsTable->find('all', [
            'join' => [
                'u' => [
                    'table' => 'discussiongroups_users',
                    'type' => 'inner',
                    'conditions' => 'u.discussiongroup_id = discussiongroups.id'
                ],
                'c' => [
                    'table' => 'users',
                    'type' => 'inner',
                    'conditions' => 'c.id = discussiongroups.creater_id'
                ]
            ],
            'fields' => [
                'discussiongroups.id',
                'discussiongroups.name',
                'discussiongroups.created',
                'discussiongroups.creater_id',
                'c.first_name',
                'c.last_name'
            ],
            'conditions' => [
               'u.user_id' => $this->Auth->user('id')
            ],
        ]);
        
        
        //set up query for retrieving ids for groups that user has not joined
        /*$ids = $this->Discussiongroups->find('all', [
            'fields' => 'u.discussiongroup_id',
        ])
                ->join([
                    'u' => [
                        'table' => 'discussiongroups_users',
                        'type' => 'inner',
                        'conditions' => 'u.discussiongroup_id = discussiongroups.id'
                    ]
                ])
                ->where(['u.user_id' => $this->Auth->user('id')])->distinct();*/
        $ids = $groupsTable->find('all', [
            'fields' => [
                'u.discussiongroup_id'
            ],
            'join' => [
                'u' => [
                    'table' => 'discussiongroups_users',
                    'type' => 'inner',
                    'conditions' => 'u.discussiongroup_id = discussiongroups.id'
                ]
            ],
            'conditions' => [
                'u.user_id' => $this->Auth->user('id')
            ]
        ]);
        
        // retrieving groups that user has not joined
        $availableGroups = $groupsTable->find('all', [
            'fields' => [
                'discussiongroups.id',
                'discussiongroups.name',
                'discussiongroups.created',
                'discussiongroups.creater_id',
                'c.first_name',
                'c.last_name'
            ],
            'join' => [
                'c' => [
                    'table' => 'users',
                    'type' => 'inner',
                    'conditions' => 'c.id = discussiongroups.creater_id'
                ]
            ],
            'conditions' => [
                'discussiongroups.id NOT IN' => $ids
            ],
            'limit' => 5
        ]);
        
        //paginate queries
        $joinedGroups = $this->paginate($joinedGroups);
        
        //send results to view
        $this->set(compact('joinedGroups', 'availableGroups'));
        $this->set('_serialize', ['discussiongroups']);
    }

    /**
     * View method
     *
     * @param string|null $id Discussiongroup id.
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
        $groupsTable = TableRegistry::get('discussiongroups');
        $discussionGroupUsersTable = TableRegistry::get('discussiongroups_users');
        $usersTable = TableRegistry::get('users');
        $postsTable = TableRegistry::get('posts');
        $likesTable = TableRegistry::get('likes');
        
        
        /*
         * set up query to retrieve discussion group
         * .include all members, creator, posts
         * ..include creator of posts
         */
        /*$discussionGroup = $this->Discussiongroups->get($id, [
            'contain' => ['Users', 'Creators', 'Posts' => [
                'Users',
                'UsersLiked',
                'sort' => ['Posts.created desc']
            ]]
        ]);*/
        
        $discussionGroup = $groupsTable->find('all', [
            'fields' => [
                'discussiongroups.id',
                'discussiongroups.creater_id',
                'discussiongroups.name',
                'discussiongroups.created',
                'c.first_name',
                'c.last_name'
            ],
            'join' => [
                'c' => [
                    'table' => 'users',
                    'type' => 'inner',
                    'conditions' => 'discussiongroups.creater_id = c.id'
                ]
            ],
            'conditions' => [
                'discussiongroups.id' => $id
            ],
        ])->first();
        
        $memberIds = $discussionGroupUsersTable->find('all', [
            'fields' => [
                'user_id'
            ],
            'conditions' => [
                'discussiongroup_id' => $id
            ]
        ]);
        
        $members = $usersTable->find('all', [
            'fields' => [
                'id',
                'first_name',
                'last_name'
            ],
            'conditions' => [
                'id IN' => $memberIds
            ]
        ]);
        
        /*$test = $this->Discussiongroups->find('all', [
            'contain' => ['Posts' => [
                'Users',
                'UsersLiked',
                'sort' => ['Posts.created desc']
            ]],
            'conditions' => [
                'id' => $id
            ]
        ]);*/
        
        $posts = $postsTable->find('all', [
           'fields' => [
               'posts.id',
               'posts.content',
               'c.id',
               'c.first_name',
               'c.last_name'
           ],
           'join' => [
               'c' => [
                   'table' => 'users',
                   'type' => 'inner',
                   'conditions' => 'posts.user_id = c.id'
               ]
           ],
           'conditions' => [
               'group_id' => $id
           ]
        ])->all();
        
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
        }
        
        //send discussion group to view
        $this->set(compact('discussionGroup', 'members', 'posts'));
        $this->set('_serialize', ['discussiongroup']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        //create new entity
        $discussiongroup = $this->Discussiongroups->newEntity();
        
        //if request is post
        if ($this->request->is('post')) {
            //patch new request data into entity
            $discussiongroup = $this->Discussiongroups->patchEntity($discussiongroup, $this->request->data);
            //set creater_id
            $discussiongroup->creater_id = $this->Auth->user('id');
            
            //attempt to save discussion group
            if ($result = $this->Discussiongroups->save($discussiongroup)) {
                //automatically add creator to members
                return $this->redirect(['controller' => 'DiscussiongroupsUsers', 'action' => 'add', $result->id]);
            } else {
                //set flash to error message
                $this->Flash->error(__('An error occurred. Please, try again.'));
            }
        }
        
        //send empty discussion group to view
        $this->set(compact('discussiongroup'));
        $this->set('_serialize', ['discussiongroup']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Discussiongroup id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        //check for null id
        if($id == null) {
            throw new NotFoundException('An error occurred. ID cannot be null.');
        }
        
        /*
         * retrieve discussion group to edit
         * .contain the group members
         */
        $discussiongroup = $this->Discussiongroups->get($id, [
            'contain' => ['Users']
        ]);
        
        //allow the request to be patch, post, put
        if ($this->request->is(['patch', 'post', 'put'])) {
            //patch new request data into entity
            $discussiongroup = $this->Discussiongroups->patchEntity($discussiongroup, $this->request->data);
            
            //attempt to save discussion group
            if ($this->Discussiongroups->save($discussiongroup)) {
                //set flash to success message
                $this->Flash->success(__('Your changes have been saved.'));
                //redirect to discussion group index
                return $this->redirect(['controller' => 'discussiongroups', 'action' => 'view', $id]);
            } else {
                //set flash to error message
                $this->Flash->error(__('An error occurred. Please try again.'));
            }
        }
        
        //send discussiongroup entity to view
        $this->set(compact('discussiongroup'));
        $this->set('_serialize', ['discussiongroup']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Discussiongroup id.
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
        
        //retrieve entity to edit
        $discussiongroup = $this->Discussiongroups->get($id);
        
        //attempt to delete discussiongroup
        if ($this->Discussiongroups->delete($discussiongroup)) {
            
        } else {
            //set flash message to error
            $this->Flash->error(__('An error occurred. Please try again.'));
        }
        
        //redirect to discussion group index
        return $this->redirect(['controller' => 'discussiongroups', 'action' => 'index']);
    }
    
    public function search() {
        //retrieve tables
        $discussionGroupsTables = TableRegistry::get('discussiongroups');
        
        //get search criteria
        $criteria = $this->request->query['search'];
        
        //query to find all relevent users
        $discussionGroups = $discussionGroupsTables->find('all', [
            'conditions' => [
                'name LIKE' => '%' . $criteria . '%'
            ]
        ]);
        
        //send users to view
        $this->set(compact('discussionGroups'));
    }
    
    public function isAuthorized($user) {
        
        if(in_array($this->request->action, ['edit', 'delete'])) {
            
            //this does not make sense
            if($user['id'] == $this->Auth->user('id')) {
                return true;
            }
            
            //return parent function
            return parent::isAuthorized($user);
        } elseif(in_array($this->request->action, ['view'])) {
            
            //retrieve tables
            $groupUsersTable = TableRegistry::get('DiscussiongroupsUsers');
            
            //see if user is member of group
            if($groupUsersTable->find('all', [
                'conditions' => [
                    'user_id' => $user['id'],
                    'discussiongroup_id' => $this->request->params['pass'][0]
                ]
            ])
                    ->first()) {
                return true;
            }
            
            //return parent function
            return parent::isAuthorized($user);
        }
        
        //return true for everything else
        return true;
    }
}
