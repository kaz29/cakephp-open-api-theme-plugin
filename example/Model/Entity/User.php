<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * User Entity
 *
 * @OA\Schema(
 *      schema="User",
 *      title="",
 *      description="User entity",
 *       @OA\Property(
 *           property="id",
 *           type="integer",
 *           format="int32",
 *           description="",
 *       ),
 *       @OA\Property(
 *           property="email",
 *           type="string",
 *           description="",
 *       ),
 *       @OA\Property(
 *           property="password",
 *           type="string",
 *           description="",
 *       ),
 *       @OA\Property(
 *           property="created",
 *           type="string",
 *           format="datetime",
 *           description="",
 *       ),
 *       @OA\Property(
 *           property="modified",
 *           type="string",
 *           format="datetime",
 *           description="",
 *       ),
 * )
 * @property int $id
 * @property string $email
 * @property string $password
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Article[] $articles
 */
class User extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'email' => true,
        'password' => true,
        'created' => true,
        'modified' => true,
        'articles' => true,
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'password',
    ];
}
