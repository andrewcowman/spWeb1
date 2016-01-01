<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Like Entity.
 */
class Like extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'user_id' => true,
        'post_id' => true,
        'user' => true,
        'post' => true,
    ];
}
