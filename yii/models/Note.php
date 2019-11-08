<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "note".
 *
 * @property integer $nid
 * @property string $dtcreate
 * @property integer $uid
 * @property string $title
 * @property string $info
 * @property string $shedule
 * @property integer $status
 */
class Note extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'note';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dtcreate', 'info'], 'safe'],
            [['uid', 'title', 'prompt'], 'required'],
            [['uid', 'prompt', 'status'], 'integer'],
            [['info', 'notedate'], 'string'],
            [['title'], 'string', 'max' => 256],
            [['shedule'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'nid' => 'Nid',
            'dtcreate' => 'DtCreate',
            'uid' => 'Uid',
            'title' => 'Title',
            'info' => 'Info',
            'prompt' => 'Prompt',
            'notedate' => 'NoteDate',
            'shedule' => 'Shedule',
            'status' => 'Status',
        ];
    }
}
