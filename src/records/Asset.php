<?php
/**
 * @link      https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license   https://craftcms.com/license
 */

namespace craft\records;

use yii\db\ActiveQueryInterface;
use craft\db\ActiveRecord;
use craft\validators\DateTimeValidator;

/**
 * Class Asset record.
 *
 * @todo   Create save function which calls parent::save and then updates the meta data table (keywords, author, etc)
 *
 * @property integer      $id           ID
 * @property integer      $volumeId     Volume ID
 * @property integer      $folderId     Folder ID
 * @property string       $filename     Filename
 * @property string       $kind         Kind
 * @property integer      $width        Width
 * @property integer      $height       Height
 * @property integer      $size         Size
 * @property \DateTime    $dateModified Date modified
 * @property Element      $element      Element
 * @property Volume       $volume       Volume
 * @property VolumeFolder $folder       Folder
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since  3.0
 */
class Asset extends ActiveRecord
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     *
     * @return string
     */
    public static function tableName()
    {
        return '{{%assets}}';
    }

    /**
     * Returns the asset file’s element.
     *
     * @return ActiveQueryInterface The relational query object.
     */
    public function getElement()
    {
        return $this->hasOne(Element::class, ['id' => 'id']);
    }

    /**
     * Returns the asset file’s volume.
     *
     * @return ActiveQueryInterface The relational query object.
     */
    public function getVolume()
    {
        return $this->hasOne(Volume::class, ['id' => 'volumeId']);
    }

    /**
     * Returns the asset file’s folder.
     *
     * @return ActiveQueryInterface The relational query object.
     */
    public function getFolder()
    {
        return $this->hasOne(VolumeFolder::class, ['id' => 'folderId']);
    }
}
