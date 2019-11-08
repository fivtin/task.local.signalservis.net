<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ts_comment".
 *
 * @property integer $cmid
 * @property integer $eid
 * @property string $cmdate
 * @property string $comment
 */
class TsComment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ts_comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['eid', 'cmdate', 'comment'], 'required'],
            [['eid', 'uid'], 'integer'],
            [['comment'], 'string'],
            [['cmdate'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cmid' => 'Cmid',
            'uid' => 'Uid',
            'eid' => 'Eid',
            'cmdate' => 'Cmdate',
            'comment' => 'Comment',
        ];
    }
}
