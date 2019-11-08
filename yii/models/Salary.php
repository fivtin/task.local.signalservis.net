<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\models;
use app\models\Employe;
use yii\base\Model;
use app\models\Paysalary;
use app\models\Report;

/**
 * Description of Salary
 *
 * @author vitt
 */
class Salary extends Model {
    
    public $employe = array(); // список сотрудников
    public $salary = array();
    public $_salary = array();
    public $month;
    public $year;
    public $sldate;

    public $result = array();
    //public $_result = array();


    public function __construct($month = '', $year = '', $eids = '') {
        
        if ($month == '') $month = date('m');
        if ($year == '') $year = date('Y');
        
        $this->month = $month;
        $this->year = $year;
        $this->sldate = $this->year * 100 + $this->month;
        
        if ($month != 1) {
            $_month = $month - 1;
            $_year = $year;
            
        }
        else {
            $_month = 12;
            $_year = $year - 1;
        }
        
        if ($eids == '')
            $this->employe = Employe::find()->asArray()->select('eid, fio_short, did, post')->where(['!=', 'status', 0])->orderBy('fio_short')->all();
        else
            $this->employe = Employe::find()->asArray()->select('eid, fio_short, did, post')->where(['IN', 'eid', $eids])->orderBy('fio_short')->all();
        
        $this->salary = Paysalary::find()->asArray()->where(['=', 'sldate', $this->sldate])->with('payout')->all();
        $this->_salary = Paysalary::find()->asArray()->where(['=', 'sldate', $_year * 100 + $_month])->with('payout')->all();
        
//        echo var_dump($this->pay);
        $i = 1;
        foreach ($this->employe as $employe) {
            
            $this->result[$i]['eid'] = $employe['eid'];
            $this->result[$i]['did'] = $employe['did'];
            $this->result[$i]['fio_short'] = $employe['fio_short'];
            $this->result[$i]['post'] = $employe['post'];
            $this->result[$i]['sldate'] = $this->sldate;
            
            
            foreach ($this->salary as $pay) {
                if ($this->result[$i]['eid'] == $pay['eid']) {
                    $this->result[$i]['salary'][] = $pay;
                    //$this->payeid['eid'] = $employe['eid'];
                    //$this->payeid[$employe['eid']]['pay'][$i] = $pay['payout'];
                }
            }
            foreach ($this->_salary as $pay) {
                if ($this->result[$i]['eid'] == $pay['eid']) {
                    $this->result[$i]['_salary'][] = $pay;
                    //$this->payeid['eid'] = $employe['eid'];
                    //$this->payeid[$employe['eid']]['pay'][$i] = $pay['payout'];
                }
            }
            
            
            //echo var_dump($employe['eid']).' ';
//            for ($i = 0; $i < count($this->pay); $i++) {
//                
//                if ($employe['eid'] == $this->pay[$i]['eid']) {
//                    echo '=>'.var_dump($this->pay[$i]['payment']).' ';
//                    $employe['pay'][$i] = $this->pay[$i];
//                }
//            }
            $i++;
        }
        
    }
    
    public function addReport($start = '', $finish = '') {
        
        if ($start == '') $start = date('Y-m-01');
        if ($finish == '') $finish = date('Y-m-t', strtotime ($start));
        
        $report = new Report();
        $report = $report->getDateRep($start, $finish, 0);
        $report = $report['report'];
        
        foreach ($this->result as $key => $value) {
            
            foreach ($report as $id => $rp) {
                
                if ($rp['eid'] == $value['eid']) {
                    $this->result[$key]['report'] = $rp;
                }
            }
        }

        $_start = date('Y-m-01', strtotime("-1 months", strtotime($start)));
        $_finish = date('Y-m-t', strtotime("-1 months", strtotime($start)));
        
        $_report = new Report();
        $_report = $_report->getDateRep($_start, $_finish, 0);
        $_report = $_report['report'];
        
        foreach ($this->result as $key => $value) {
            
            foreach ($_report as $id => $rp) {
                
                if ($rp['eid'] == $value['eid']) {
                    $this->result[$key]['_report'] = $rp;
                }
            }
        }
    }
    
    
}
