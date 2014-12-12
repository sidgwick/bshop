<?php
namespace Common\Model;
use \Think\Model\ViewModel;

/*
 * 图书表的视图模型
 */
class AddressViewModel extends ViewModel {
    public $viewFields = array(
        'Address' => array(
            'street',
            'zipcode',
            'receiver',
            'mobile',
        ),
        'Province' => array(
            '_table' => '__REGION__',
            '_as' => 'Province',
            'name' => 'province',
            '_on' => 'Province.id = Address.province',
        ),
        'City' => array(
            '_table' => '__REGION__',
            '_as' => 'City',
            'name' => 'city',
            '_on' => 'City.id = Address.city',
            '_type' => "LEFT",
        ),
        'Country' => array(
            '_table' => '__REGION__',
            '_as' => 'Country',
            'name' => 'country',
            '_on' => 'Country.id = Address.country',
        ),
    );
}
