<?php
namespace App\Model\Table;

use App\Model\Entity\User;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

/**
 * Users Model
 *
 * @property \Cake\ORM\Association\HasMany $Posts
 */
class UsersTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('users');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->hasMany('Posts', [
            'foreignKey' => 'user_id'
        ]);
        $this->addBehavior('Timestamp');
        $this->belongsToMany('Followers', [
            'className' => 'Users',
            'joinTable' => 'follows',
            'foreignKey' => 'following_id',
            'targetForeignKey' => 'follower_id',
            
        ]);
        $this->belongsToMany('Followings', [
            'className' => 'Users',
            'joinTable' => 'follows',
            'foreignKey' => 'follower_id',
            'targetForeignKey' => 'following_id', 
        ]);
        $this->belongsToMany('Discussiongroups', [
            'foreignKey' => 'user_id',
            'targetForeignKey' => 'discussiongroup_id',
            'joinTable' => 'discussiongroups_users'
        ]);
        $this->hasOne('Settings', [
            'className' => 'UserSettings',
            'foreignKey' => 'user_id'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create');
            
        $validator
            ->add('email', 'valid', ['rule' => 'email'])
            ->requirePresence('email', 'create')
            ->notEmpty('email');
            
        $validator
            ->requirePresence('password', 'create')
            ->notEmpty('password');
            
        $validator
            ->allowEmpty('first_name');
            
        $validator
            ->allowEmpty('last_name');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['email']));
        return $rules;
    }
    
    public function getRole($role_id) {
        $query = $this->query();
        $role = $query->select(['roles.role'])
                ->join([
                    'roles' => [
                        'table' => 'roles',
                        'type' => 'INNER',
                        'conditions' => 'users.role_id = roles.id'
                    ]
                ])
                ->where(['roles.id' => $role_id])
                ->first();
        
        return $role->roles['role'];
    }
    
    public function displayWhat($viewerId, $viewingId) {
        $followsTable = TableRegistry::get('follows');
        $settingsTable = TableRegistry::get('user_settings');
        
        $result = $settingsTable->query()->select('profile')->where(['user_id' => $viewingId])->first();
        
        /*
         * 1 = everybody
         * 2 = friends
         */
        if($result['profile'] == 'friends') {
            if($followsTable->isMutualRelationship($viewerId, $viewingId) || $viewerId == $viewingId) {
                return 1;
            }
            
            return 2;
        }
        
        return 1;
    }
}
