<?php

/**
 * This is the model class for table "booking_service_config".
 *
 * The followings are the available columns in table 'booking_service_config':
 * @property integer $id
 * @property string $service_name
 * @property string $deposit
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class BookingServiceConfig extends EActiveRecord {

    const BOOKING_SERVICE_REGULAR = 1;//普通预约
    const BOOKING_SERVICE_FREE_LIINIC = 2;//义诊
    const BOOKING_SERVICE_ZERO_LIINIC = 3;//0元面诊

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'booking_service_config';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('date_created', 'required'),
            array('service_name', 'length', 'max' => 50),
            array('deposit', 'length', 'max' => 10),
            array('date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, service_name, deposit, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'service_name' => 'Service Name',
            'deposit' => 'Deposit',
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
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('service_name', $this->service_name, true);
        $criteria->compare('deposit', $this->deposit, true);
        $criteria->compare('date_created', $this->date_created, true);
        $criteria->compare('date_updated', $this->date_updated, true);
        $criteria->compare('date_deleted', $this->date_deleted, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return BookingServiceConfig the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
