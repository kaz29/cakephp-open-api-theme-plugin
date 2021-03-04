<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Tag Entity
 *
 * @OA\Schema(
 *      schema="Tag",
 *      title="",
 *      description="Tag entity",
 *       @OA\Property(
 *           property="id",
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
 * @property string $title
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Article[] $articles
 */
class Tag extends Entity
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
        'title' => true,
        'created' => true,
        'modified' => true,
        'articles' => true,
    ];
}
