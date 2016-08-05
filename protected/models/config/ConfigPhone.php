<?php

/**
 * This is the model class for table "phone_config".
 *
 * The followings are the available columns in table 'phone_config':
 * @property integer $id
 * @property string $phone_name
 * @property string $username
 * @property string $password
 * @property string $enterprise_id
 * @property string $hotline
 * @property integer $is_active
 * @property string $interface_server_ip
 * @property string $preview_outcall_url
 * @property string $queue_monitoring_url
 * @property string $in_list_url
 * @property string $in_detail_url
 * @property string $out_list_url
 * @property string $out_detail_url
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class ConfigPhone extends DB2ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'config_phone';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('is_active', 'numerical', 'integerOnly'=>true),
			array('phone_name, username, password, enterprise_id, hotline, interface_server_ip, preview_outcall_url, queue_monitoring_url, in_list_url, in_detail_url, out_list_url, out_detail_url', 'length', 'max'=>100),
			array('date_created, date_updated, date_deleted', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, phone_name, username, password, enterprise_id, hotline, is_active, interface_server_ip, preview_outcall_url, queue_monitoring_url, in_list_url, in_detail_url, out_list_url, out_detail_url, date_created, date_updated, date_deleted', 'safe', 'on'=>'search'),
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
			'phone_name' => 'phone名称 （用于区分在多个phone配置的情况下使用那个配置）',
			'username' => 'Username',
			'password' => 'Password',
			'enterprise_id' => '企业ID',
			'hotline' => '企业热线号码',
			'is_active' => '是否激活（0 未激活 1激活）',
			'interface_server_ip' => 'Interface Server Ip',
			'preview_outcall_url' => '第三方外呼调用',
			'queue_monitoring_url' => '队列座席状态',
			'in_list_url' => '来电通话记录',
			'in_detail_url' => '来电通话记录详细',
			'out_list_url' => '外呼通话记录',
			'out_detail_url' => '外呼通话记录详情',
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
		$criteria->compare('phone_name',$this->phone_name,true);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('enterprise_id',$this->enterprise_id,true);
		$criteria->compare('hotline',$this->hotline,true);
		$criteria->compare('is_active',$this->is_active);
		$criteria->compare('interface_server_ip',$this->interface_server_ip,true);
		$criteria->compare('preview_outcall_url',$this->preview_outcall_url,true);
		$criteria->compare('queue_monitoring_url',$this->queue_monitoring_url,true);
		$criteria->compare('in_list_url',$this->in_list_url,true);
		$criteria->compare('in_detail_url',$this->in_detail_url,true);
		$criteria->compare('out_list_url',$this->out_list_url,true);
		$criteria->compare('out_detail_url',$this->out_detail_url,true);
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
	 * @return PhoneConfig the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
