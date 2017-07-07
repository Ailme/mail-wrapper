<?php

namespace app\modules\mail\models;

use Yii;

/**
 * This is the model class for table "{{%exchange_mails}}".
 *
 * @property integer $id
 * @property string $created_at
 * @property string $modified_at
 * @property string $date
 * @property string $status
 * @property string $mailbox
 * @property string $raw
 * @property string $from
 * @property string $subject
 * @property string $textPlain
 * @property string $textHtml
 * @property string $files
 * @property string $amount_attempts
 * @property string $result
 */
class SDExchangeMail extends \yii\db\ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%exchange_mails}}';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['raw', 'required'],
            ['raw', 'string'],

            ['textPlain', 'string'],

            ['textHtml', 'string'],

            ['files', 'string'],

            ['from', 'required'],
            ['from', 'string', 'max' => 255],

            ['subject', 'string', 'max' => 1000],
        ];
    }

    /**
     * @return mixed
     */
    public static function getDb()
    {
        return Yii::$app->db_sd;
    }
}
