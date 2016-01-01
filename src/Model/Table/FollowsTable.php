<?php
namespace App\Model\Table;

use App\Model\Entity\Follow;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Follows Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Followers
 * @property \Cake\ORM\Association\BelongsTo $Followings
 */
class FollowsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('follows');
        $this->displayField('follower_id');
        $this->primaryKey('id');
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
        $rules->add($rules->existsIn(['follower_id'], 'Users'));
        $rules->add($rules->existsIn(['following_id'], 'Users'));
        return $rules;
    }
    
    public function isMutualRelationship($id1, $id2) {
        //set up query
        $query = $this->query();
        
        //run query to find if person1 is following person2
        $record1 = $query->select('id')
                ->where(['follower_id' => $id1, 'following_id' => $id2])
                ->count();
        
        //reset query
        $query = $this->query();
        
        //run query to find if person2 is following person1
        $record2 = $query->select('id')
                ->where(['follower_id' => $id2, 'following_id' => $id1])
                ->count();
        
        //if either record count is 0 return false
        if($record1 < 1 || $record2 < 1) {
            return false;
        }
        
        //else return true
        return true;
    }

}
