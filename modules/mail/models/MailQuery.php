<?php

namespace app\modules\mail\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Mail]].
 *
 * @see Mail
 */
class MailQuery extends ActiveQuery
{
    /**
     * @param int $days
     *
     * @return $this
     */
    public function old($days = 30)
    {
        return $this->andWhere(['<=', 'DATE(created_at)', date(DATE_ATOM_SHORT, strtotime("-$days days"))]);
    }

    /**
     * @inheritdoc
     * @return Mail[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Mail|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
