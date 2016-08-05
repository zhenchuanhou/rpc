<?php

class DoctorSearch extends ESearchModel {

    public function __construct($searchInputs, $with = null) {
        $searchInputs['order'] = 't.is_contracted DESC, t.role, t.medical_title, convert(t.name using gbk)';
        parent::__construct($searchInputs, $with);
    }

    public function model() {
        $this->model = new Doctor();
    }

    public function getQueryFields() {
        return array('city', 'disease', 'hospital', 'hpdept', 'mtitle', 'disease_name', 'disease_category', 'is_contracted', 'disease_sub_category');
    }

    public function addQueryConditions() {
        $this->criteria->addCondition('t.date_deleted is NULL');

        if ($this->hasQueryParams()) {
            // Doctor.medical_title
            if (isset($this->queryParams['mtitle'])) {
                $mtitle = $this->queryParams['mtitle'];
                $this->criteria->compare('t.medical_title', $mtitle);
            }
            // Doctor.is_contracted
            if (isset($this->queryParams['is_contracted'])) {
                $is_contracted = $this->queryParams['is_contracted'];
                $this->criteria->compare('t.is_contracted', $is_contracted);
            }
            // Doctor.city
            if (isset($this->queryParams['city'])) {
                $cityId = $this->queryParams['city'];
                $this->criteria->compare('t.city_id', $cityId);
            }
            // Disease.
            if (isset($this->queryParams['disease'])) {
                $diseaseId = $this->queryParams['disease'];
                $this->criteria->join .= 'left join disease_doctor_join ddj on (t.`id`=ddj.`doctor_id`)';
                $this->criteria->compare("ddj.disease_id", $diseaseId);
                $this->criteria->distinct = true;
            }
            // DiseaseName.
            if (isset($this->queryParams['disease_name'])) {
                $disease_name = $this->queryParams['disease_name'];
                $this->criteria->join = 'left join disease_doctor_join ddj on (t.`id`=ddj.`doctor_id`) left join disease d on d.id=ddj.disease_id';
                $this->criteria->addSearchCondition('d.name', $disease_name);
                $this->criteria->distinct = true;
            }
            if (isset($this->queryParams['hospital'])) {
                $hospitalId = $this->queryParams['hospital'];
                $this->criteria->compare("t.hospital_id", $hospitalId);
            }
            if (isset($this->queryParams['hpdept'])) {
                $hpdeptId = $this->queryParams['hpdept'];
                $this->criteria->join .= 'left join hospital_dept_doctor_join hddj on (t.`id`=hddj.`doctor_id`)';
                $this->criteria->compare("hddj.hp_dept_id", $hpdeptId);
                $this->criteria->distinct = true;
            }
            // disease_category.
            if (isset($this->queryParams['disease_category'])) {
                $cateId = $this->queryParams['disease_category'];
                $this->criteria->join = 'left join disease_doctor_join b on t.id=b.doctor_id left join disease c on c.id=b.disease_id left join disease_category d on c.category_id=d.sub_cat_id';
                $this->criteria->addCondition("d.cat_id=:cateId");
                $this->criteria->params[":cateId"] = $cateId;
                $this->criteria->distinct = true;
            }
            // disease_sub_category.
            if (isset($this->queryParams['disease_sub_category'])) {
                $category_id= $this->queryParams['disease_sub_category'];
                $this->criteria->join = 'left join disease_doctor_join b on t.id=b.doctor_id left join disease c on c.id=b.disease_id ';
                $this->criteria->addCondition("c.category_id=:category_id");
                $this->criteria->params[":category_id"] = $category_id;
                $this->criteria->distinct = true;
            }
        }
    }

}
