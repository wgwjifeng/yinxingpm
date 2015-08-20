<?php

class CustomListViewsModel extends BaseModel {

    protected $default_list_views = array(
            'Projects'   => array('title', 'identifier', 'donor','status', 'grant_amount_ratio'),
            'Schools' => array('name', 'province','city', 'leader_name', 'leader_contact', 'grade_amount', 'class_amount', 'class_avg_amount', 'student_amount'),
            'UserInfo' => array(
                           '0'=> array('id','name', 'org', 'classify', 'recommender_name', 'recommender_org', 'recommend_submit_time'),
                           '60,70' => array('name','identifier', 'gender', 'email', 'mobile','recommender_name'),
                           '100'=> array('id','name', 'org', 'classify', 'recommender_name', 'recommender_org', 'recommend_submit_time',"status_note"),
                           ),
        );

    public function getDefaultListViews(){
        return $this->default_list_views;
    }
    
    public function getListView($module, $status) {

        $res = $this->default_list_views[$module];
        if(!$module || !$res) return array();

        if($res[0]) {
            foreach($res as $k=>$v) {
                $ks = explode(',',$k);
                if(in_array($status, $ks)) {
                    return $v;
                }
            }
            return $res[0];
        }

        return $this->default_list_views[$module];
    }

}
?>