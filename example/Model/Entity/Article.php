<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Article Entity
 *
 * @OA\Schema(
 *      schema="Article",
 *      title="",
 *      description="Article entity",
 *       @OA\Property(
 *           property="id",
 *           type="integer",
 *           format="int32",
 *           description="",
 *       ),
 *       @OA\Property(
 *           property="user_id",
 *           type="integer",
 *           format="int32",
 *           description="",
 *       ),
 *       @OA\Property(
 *           property="title",
 *           type="string",
 *           description="",
 *       ),
 *       @OA\Property(
 *           property="slug",
 *           type="string",
 *           description="",
 *       ),
 *       @OA\Property(
 *           property="body",
 *           type="string",
 *           description="",
 *       ),
 *       @OA\Property(
 *           property="published",
 *           type="boolean",
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
 * @property int $user_id
 * @property string $title
 * @property string $slug
 * @property string|null $body
 * @property bool $published
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Tag[] $tags
 */
class Article extends Entity
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
        'user_id' => true,
        'title' => true,
        'slug' => true,
        'body' => true,
        'published' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
        'tags' => true,
    ];
}
