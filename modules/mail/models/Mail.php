<?php

namespace app\modules\mail\models;

use app\behaviors\DatetimeBehavior;
use app\modules\mail\Module;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "{{%mail}}".
 *
 * @property integer $id
 * @property string $type
 * @property string $created_at
 * @property string $updated_at
 * @property string $raw
 * @property string $from
 * @property string $to
 * @property string $subject
 * @property string $text
 * @property string $html
 * @property string $files
 * @property string $xml
 */
class Mail extends \yii\db\ActiveRecord
{
    const TYPE_IN = 'in';
    const TYPE_OUT = 'out';

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            DatetimeBehavior::className(),
        ];
    }

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%mail}}';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['raw', 'required'],
            ['raw', 'string'],

            ['text', 'string'],

            ['html', 'string'],

            ['files', 'string'],

            ['xml', 'string'],

            ['to', 'string', 'max' => 255],

            ['from', 'string', 'max' => 255],

            ['subject', 'string', 'max' => 1000],

            ['type', 'string', 'max' => 3],
            ['type', 'default', 'value' => self::TYPE_IN],
            ['type', 'in', 'range' => array_keys(self::getTypesArray())],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'created_at' => Module::t('module', 'CREATED'),
            'raw' => Module::t('module', 'RAW'),
            'from' => Module::t('module', 'FROM'),
            'to' => Module::t('module', 'TO'),
            'subject' => Module::t('module', 'SUBJECT'),
            'text' => Module::t('module', 'TEXT'),
            'html' => Module::t('module', 'HTML'),
            'files' => Module::t('module', 'FILES'),
            'xml' => Module::t('module', 'XML'),
            'type' => Module::t('module', 'TYPE'),
        ];
    }

    /**
     * @inheritdoc
     * @return MailQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new MailQuery(get_called_class());
    }

    /**
     * @return string
     */
    public function getTypesName()
    {
        return ArrayHelper::getValue(self::getTypesArray(), $this->type);
    }

    /**
     * @return array
     */
    public static function getTypesArray()
    {
        return [
            self::TYPE_IN => Module::t('module', 'TYPE_IN'),
            self::TYPE_OUT => Module::t('module', 'TYPE_OUT'),
        ];
    }

    /**
     * @return bool
     */
    public function isIncoming()
    {
        return $this->type === self::TYPE_IN;
    }

    /**
     * @return $this
     */
    public function setTypeIncoming()
    {
        $this->type = self::TYPE_IN;

        return $this;
    }

    /**
     * @return $this
     */
    public function setTypeOutcoming()
    {
        $this->type = self::TYPE_OUT;

        return $this;
    }

    /**
     * Get url link for file
     *
     * @param $name
     *
     * @return string
     */
    public function getDownloadUrlFile($name)
    {
        if (empty($name)) {
            return '';
        }

        $params = ['/mail/default/download-file', 'id' => $this->id, 'name' => trim($name)];

        return Html::a($name, Url::toRoute($params), ['target' => '_blank']);
    }
}
