<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace craft\records;

use craft\db\ActiveRecord;
use craft\db\Query;
use craft\db\Table;
use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;

/**
 * Field group record class.
 *
 * @property int $id ID
 * @property int $fieldLayoutId Field layout ID
 * @property string $name Name
 * @property string $handle Handle
 * @property int $sortOrder Sort order
 * @property Element $element Element
 * @property FieldLayout $fieldLayout Field layout
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.0.0
 */
class GlobalSet extends ActiveRecord
{
    /**
     * @inheritdoc
     * @return string
     */
    public static function tableName(): string
    {
        return Table::GLOBALSETS;
    }

    /**
     * @return ActiveQuery
     */
    public static function find()
    {
        return parent::find()
            ->where([
                'exists', (new Query())
                    ->from(['e' => Table::ELEMENTS])
                    ->where('[[e.id]] = ' . static::tableName() . '.[[id]]')
                    ->andWhere(['e.dateDeleted' => null]),
            ]);
    }

    /**
     * @return ActiveQuery
     */
    public static function findWithTrashed(): ActiveQuery
    {
        return static::find()->where([]);
    }

    /**
     * @return ActiveQuery
     */
    public static function findTrashed(): ActiveQuery
    {
        return static::find()->where([
            'not exists', (new Query())
                ->from(['e' => Table::ELEMENTS])
                ->where('[[e.id]] = ' . static::tableName() . '.[[id]]')
                ->andWhere(['e.dateDeleted' => null]),
        ]);
    }

    /**
     * Returns the global set’s element.
     *
     * @return ActiveQueryInterface The relational query object.
     */
    public function getElement(): ActiveQueryInterface
    {
        return $this->hasOne(Element::class, ['id' => 'id']);
    }

    /**
     * Returns the global set’s fieldLayout.
     *
     * @return ActiveQueryInterface The relational query object.
     */
    public function getFieldLayout(): ActiveQueryInterface
    {
        return $this->hasOne(FieldLayout::class,
            ['id' => 'fieldLayoutId']);
    }
}
