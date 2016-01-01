<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * UserSetting Entity.
 */
class UserSetting extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'profile' => true,
        'user' => true,
    ];
}
