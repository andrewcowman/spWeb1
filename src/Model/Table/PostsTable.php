<?php
namespace App\Model\Table;

use App\Model\Entity\Post;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;

/**
 * Posts Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 */
class PostsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('posts');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
        $this->hasMany('Comments', [
            'foreignKey' => 'post_id'
        ]);
        $this->belongsTo('IntendedUser', [
            'className' => 'Users',
            'foreignKey' => 'for_user_id'
        ]);
        $this->hasMany('UsersLiked', [
           'className' => 'Likes',
           'foreignKey' => 'post_id'
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
            ->allowEmpty('content');

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
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        return $rules;
    }
    
    public function isOwnedBy($postId, $userId) {
        return $this->exists(['id' => $postId, 'user_id' => $userId]);
    }
    
    public function deletePostsBetweenUsers($id1, $id2) {
        $query = $this->query();
        $query = $this->find('all', [
            'conditions' => [
                'OR' => [
                    ['user_id' => $id1, 'for_user_id' => $id2],
                    ['user_id' => $id2, 'for_user_id' => $id1]
                ]
            ]
        ]);
        //attempt to delete all posts between users
        if($query->count() == 0 || $this->deleteAll([
            'OR' => [
                ['user_id' => $id1, 'for_user_id' => $id2],
                ['user_id' => $id2, 'for_user_id' => $id1]
            ]
        ])) {
            return true;
        }
        
        return false;
    }
    
    /*public function beforeSave(\Cake\Event\Event $event, \Cake\Model\Entity $entity, $options) {
        if($entity->for_user_id == $entity->user_id) {
            $entity->for_user_id = null;
        }
    }*/
}
