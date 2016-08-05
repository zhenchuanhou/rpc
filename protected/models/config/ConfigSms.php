<?php

/**
 * This is the model class for table "config_sms".
 *
 * The followings are the available columns in table 'config_sms':
 * @property integer $id
 * @property string $sms_name
 * @property string $sms_id
 * @property string $key
 * @property string $url
 * @property integer $type
 * @property integer $is_active
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class ConfigSms extends DBKeyActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'config_sms';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type, is_active', 'numerical', 'integerOnly'=>true),
			array('sms_name, sms_id', 'length', 'max'=>100),
			array('key, url', 'length', 'max'=>200),
			array('date_created, date_updated, date_deleted', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, sms_name, sms_id, key, url, type, is_active, date_created, date_updated, date_deleted', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'sms_name' => 'sms名称 （用于区分在多个sms配置的情况下使用那个配置）',
			'sms_id' => 'Sms',
			'key' => 'Key',
			'url' => 'Url',
			'type' => '1通知类 2营销类',
			'is_active' => '是否激活（0 未激活 1激活）',
			'date_created' => 'Date Created',
			'date_updated' => 'Date Updated',
			'date_deleted' => 'Date Deleted',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('sms_name',$this->sms_name,true);
		$criteria->compare('sms_id',$this->sms_id,true);
		$criteria->compare('key',$this->key,true);
		$criteria->compare('url',$this->url,true);
		$criteria->compare('type',$this->type);
		$criteria->compare('is_active',$this->is_active);
		$criteria->compare('date_created',$this->date_created,true);
		$criteria->compare('date_updated',$this->date_updated,true);
		$criteria->compare('date_deleted',$this->date_deleted,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ConfigSms the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
