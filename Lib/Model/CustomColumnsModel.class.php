<?php
/*
    TODO：解释备注：
*/
class CustomColumnsModel extends BaseModel {

    public function init($team_id, $module) {
        if(!$team_id || !$module) return NULL;

        $cond['team_id'] = $team_id;
        $cond['module'] = $module;

        $rs = M("CustomColumns")->where($cond)->find();

        //set result
        $new_columns = unserialize($rs['new_columns']);
        $disabled_columns = unserialize($rs['disabled_columns']);

        return array($new_columns, $disabled_columns);
    }

    public function getProjectColumnsDefine($manage_group_id) {
        $fields = array(
            'identifier'  => array('display_name' => '项目编号', 'hidden' => 'detail'),
            "title" => array("display_name" => "项目名称", "hidden" => "detail", 'link' => array('link_type' => 'project', 'column'=>'id'),),
            'donor' => array('display_name' => '资助方', ),
            'status'  => array('display_name' => '项目状态', 'type'=>'select', 'options'=> D('ProjectStatuses')->getStatusIdNameMap(), 'hidden' => 'detail'),
            'status_name' => array('display_name'=>'状态', 'hidden'=>1),
            'donor_contact' => array('display_name' => '资助方联系方式', ),
            'manager_id' => array('display_name' => '项目负责人', "class"=>"span2", 'type' => 'select', 'options'=> D('Users')->getProjectManager()),
            'manager_name' => array('display_name'=>'项目负责人', 'hidden'=>1),
            'type' => array('display_name' => '项目设计方向', ),
            'address'  => array('display_name' => '项目执行地址', ),
            'contract_budget' => array('display_name' => '项目资金', 'type'=>'number', 'extra_type' => 'money', 'class'=>'span2', ),
            'contract' => array('display_name' => '合同编号/备注',),
            'contract_time' => array('display_name' => '合同签订日期', 'type'=>'date', 'tip' => '格式：yyyy-mm-dd', 'class'=>'span2'),
            'related_link' => array('display_name' => '资源库外链', 'tip' => 'http://'),
            'summary' => array('display_name' => '项目摘要', 'type'=>'text'),
            'expect_result' => array('display_name' => '主要成效', 'type'=>'text'),
            'grant_amount_ratio' => array('display_name'=>'已资助', 'hidden'=>1),

        );
        return $fields;
    }

    public function getProjectModuleColumns() {

        list($new_columns, $disabled_columns) = $this->init($team_id, 'Projects');
        $fields = $this->getProjectColumnsDefine($manage_group_id);
        if($new_columns) {
            $fields = array_merge($fields, $new_columns);
        }

        if($disabled_columns) {
            foreach ($disabled_columns as $dc) {
                unset($fields[$dc]);
            }            
        }

        return $fields;
    }


    public function getUserInfoModuleColumns($apply_type_id) {
        $columns = array(
            // "name" => array("display_name" => "姓名", "type" => "text", 'link' => array('link_type' => 'user', 'column'=>'id')),
            // "identifier" => array("display_name" => "编号", 'hidden' => 1),
            // "status_name" => array("display_name" => "状态", 'hidden' => 1),
            // "email" => array("display_name" => "邮箱", ),
            // "mobile" => array("display_name" => "手机", ),
            // "gender" => array("display_name" => "性别" , "type" => "select", "param" => array("options" => array('女', '男')) ),
            // "identity_id" => array("display_name" => "身份证号", ),
            // "people" => array("display_name" => "民族", "type"=>'select', "param" =>array("require" => 1, 'options'=>array('汉族','壮族','回族','满族','维吾尔族','苗族','彝族','土家族','藏族','蒙古族','侗族','布依族','瑶族','白族','朝鲜族','哈尼族','黎族','哈萨克族','傣族','畲族','傈僳族','东乡族','仡佬族','拉祜族','佤族','水族','纳西族','羌族','土族','仫佬族','锡伯族','柯尔克孜族','景颇族','达斡尔族','撒拉族','布朗族','毛南族','塔吉克族','普米族','阿昌族','怒族','鄂温克族','京族','基诺族','德昂族','保安族','俄罗斯族','裕固族','乌孜别克族','门巴族','鄂伦春族','独龙族','赫哲族','高山族','珞巴族','塔塔尔族','其它'))),
            // "birthday" => array("display_name" => "生日", "type" => "date", "param" =>array("placeholder" => "格式：YYYY-MM-DD",)),
            // "edu_degree" => array("display_name" => "最高学历", ),
            // "score" => array("display_name" => "平均分", ),
            // "language_level" => array("display_name" => "外语水平", ),
            // "language_score" => array("display_name" => "外语成绩", ),
            // "qq" => array("display_name" => "QQ", ),

            // "is_disease" => array("display_name" => "传染性/精神类/重大疾病" , "type" => "select", "param" => array("options" => array('否', '是'))),
            // // "health" => array("display_name" => "健康情况", ),

            // "howto_know" => array("display_name" => "了解TFC的途径", "li_class"=>"fw", "type" => "select" , 'hidden'=>1, 
            //     "param" => array("options" => array('宣讲会/招聘会','海报、传单、图片展','社交网站','朋友介绍','高校就业指导中心','公益类网站','求职类网站', '其它'))),

            // "howto_know_string" => array("display_name" => "了解TFC的途径", ),
            // "howto_know_detail" => array("display_name" => "了解TFC的途径具体名称", ),

            // "submit_time" => array("display_name" => "申请日期", ),

            // "mailing_address" => array("display_name" => "邮寄地址", "li_class"=>"fw", 'display_colspan' => 3),
            // "mailing_address_zipcode" => array("display_name" => "邮编",'hidden'=>1),

            // "home_address" => array("display_name" => "长期有效地址", "li_class"=>"fw", 'display_colspan' => 3),
            // "home_address_zipcode" => array("display_name" => "邮编",'hidden'=>1),

            // "emergent_contact" => array("display_name"=>'紧急联系人'),
            // "contract_school" => array("display_name"=>'签约学校' ),
            // "contract_school_course" => array("display_name"=>'任教科目'),

            // "edu_info" => array("display_name" => "教育背景", "type" => "group" , 
            //     'options' => array('name'=>array('name'=>'时间段', 'class'=>'span2', ),
            //                          'school' => array('name'=>'就读学校','class'=>'span2'),
            //                          'degree' => array('name'=>'所获学位','class'=>'span2'),
            //                          'major' => array('name'=>'专业','class'=>'span2'),
            //                          'type' => array('name'=>'学历类型(统招/其他)', 'class'=>'span2', ))),

            // "experience" => array("display_name" => "实践经历", "type" => "group" , 
            //     'options' => array('name'=>array('name'=>'时间段', 'class'=>'span2', ),
            //                         'duty' => array('name'=>'职务', 'class'=>'span2', ),
            //                          'detail' => array('name'=>'社团/实践/工作/实习经历','type'=>'textarea', 'class'=>'span5'),
            //                          )),

            // "self_introduction" => array("display_name" => "自传", "type" => "textarea", 'display_colspan'=>3),
            "id" => array("display_name" => "编号",),
            "name" => array("display_name" => "姓名",),
            "gender" => array("display_name" => "性别" , "type" => "select", "param" => array("options" => array('女', '男'))),
            "mobile" => array("display_name" => "手机",),
            "email" => array("display_name" => "邮箱",),
            "org" => array("display_name" => "机构",),
            "birthday" => array("display_name" => "出生日期", "type" => "date", "param" =>array("placeholder" => "格式：YYYY-MM-DD",)),
            "people" => array("display_name" => "民族", "type"=>'select', "param" =>array("require" => 1, 'options'=>array('汉族','壮族','回族','满族','维吾尔族','苗族','彝族','土家族','藏族','蒙古族','侗族','布依族','瑶族','白族','朝鲜族','哈尼族','黎族','哈萨克族','傣族','畲族','傈僳族','东乡族','仡佬族','拉祜族','佤族','水族','纳西族','羌族','土族','仫佬族','锡伯族','柯尔克孜族','景颇族','达斡尔族','撒拉族','布朗族','毛南族','塔吉克族','普米族','阿昌族','怒族','鄂温克族','京族','基诺族','德昂族','保安族','俄罗斯族','裕固族','乌孜别克族','门巴族','鄂伦春族','独龙族','赫哲族','高山族','珞巴族','塔塔尔族','其它'))),
            "marital" => array("display_name" => "婚姻状况" , "type" => "select", "param" => array("options" => array('未婚', '已婚'))),
            "work_from" => array("display_name" => "工作时间", "param" =>array("require" => 1, 'placeholder'=>'XXXX年参加工作')),
            "identity_id" => array("display_name" => "身份证号",),
            "language" => array("display_name" => "语言能力", ),
            "address" => array("display_name" => "通讯地址", 'display_colspan' => 2),
            "address_zipcode" => array("display_name" => "邮编", ),

            "edu_info" => array("display_name" => "教育情况", "type" => "group" ,  
                'options' => array('time'=>array('name'=>'时间段', 'class'=>'span2', 'extra_attr' => 'placeholder=""'),
                                     'school' => array('name'=>'就读学校','class'=>'span2'),
                                     'major' => array('name'=>'专业','class'=>'span2'),
                                     'degree' => array('name'=>'学历','class'=>'span2'),
                                     )),

            "training_info" => array("display_name" => "培训情况", "type" => "group" ,
                'options' => array('time'=>array('name'=>'时间段', 'class'=>'span2', 'extra_attr' => 'placeholder=""'),
                                     'org' => array('name'=>'培训机构','class'=>'span2'),
                                     'content' => array('name'=>'培训内容','type'=>'textarea', 'class'=>'span5'),
                                     )),

            "work_info" => array("display_name" => "工作经历", "type" => "group" , 
                'options' => array('time'=>array('name'=>'时间段', 'class'=>'span2', 'extra_attr' => 'placeholder=""'),
                                    'duty' => array('name'=>'职务', 'class'=>'span2', ),
                                     'detail' => array('name'=>'主要经历','type'=>'textarea', 'class'=>'span5'),
                                     )),

            "other_info" => array("display_name" => "其他社会工作/义工经历", "type" => "group" , 
                'options' => array('time'=>array('name'=>'时间段', 'class'=>'span2', 'extra_attr' => 'placeholder=""'),
                                    'duty' => array('name'=>'职务', 'class'=>'span2', ),
                                     'detail' => array('name'=>'主要经历','type'=>'textarea', 'class'=>'span5'),
                                     )),

            "hobby" => array("display_name" => "兴趣爱好", "type" => "textarea", 'display_colspan' => 3),
            "self_introduction" => array("display_name" => "自我评价", "type" => "textarea", 'display_colspan' => 3),
            "honor" => array("display_name" => "所获荣誉", "type" => "textarea", 'display_colspan' => 3),
            
            //     "contract_time" => array("display_name" => "签合同日期", 'status_range'=>'80,100'),
            //     "contract_note" => array("display_name" => "合同信息", 'display_colspan'=>2, 'status_range'=>'80,100'),
            //     "bank_account_name" => array("display_name" => "银行账户名", 'status_range'=>'80,100'),
            //     "bank_account" => array("display_name" => "银行账号", 'status_range'=>'80,100'),
            //     "bank_name" => array("display_name" => "开户行名称", 'status_range'=>'80,100'),
        );


        return $columns;
    }


    public function getUserRecommendModuleColumns() {
        $columns = array(
            "recommender_name" => array("display_name" => "姓名", "type" => "text",),
            "recommender_gender" => array("display_name" => "性别" , "type" => "select", "param" => array("options" => array('女', '男'))),
            "recommender_org" => array("display_name" => "工作单位" , ),
            "recommender_duty" => array("display_name" => "职务" ),
            "recommender_mobile" => array("display_name" => "联系电话" , ),
            "recommender_email" => array("display_name" => "联系邮件", ),
            "recommender_birthday" => array("display_name" => "出生日期", "type" => "date", "param" =>array("placeholder" => "格式：YYYY-MM-DD",)),
            "recommender_work_from" => array("display_name" => "参加工作时间", ),
            "recommender_address" => array("display_name" => "通讯地址", "li_class"=>"fw", 'display_colspan' => 2),
            "recommender_address_zipcode" => array("display_name" => "邮编", ),

            "recommender_work_info" => array("display_name" => "主要工作经历", "type" => "group" , 
                'options' => array('time'=>array('name'=>'时间', 'class'=>'span3', 'extra_attr' => 'placeholder=""'),
                                     'detail' => array('name'=>'主要经历','type'=>'textarea', 'class'=>'span6'),
                                     )),
            "recommender_honor" => array("display_name" => "所获荣誉", "type" => "textarea", 'display_colspan' => 3),

            "name" => array("display_name" => "姓名", "type" => "text", 'link' => array('link_type' => 'user', 'column'=>'id'),),
            "org" => array("display_name" => "工作单位", "param" =>array("placeholder" => "",)),
            "mobile" => array("display_name" => "联系电话" , ),
            "id" => array("display_name" => "编号" , ),
            "classify" => array("display_name" => "分类" , ),
            "status_note" => array("display_name" => "评级" , ),
            "email" => array("display_name" => "联系邮件", ),
            //为了便于构建filter，增加了一个column，可以通过URL参数来查询省份，同时为了便于统计页面的超链接省份跳转
            "address_province" => array("display_name" => "所在省份", ),
            "address" => array("display_name" => "所在地区", "type"=>"address", "li_class" => "fw", ),
        );

        return $columns;
    }

    //current user 管理的人的ID和Name的对应关系
    public function getOwnedUsersMap($manage_group_ids) {
        if(!$manage_group_ids) return NULL;
        $str = implode(',', $manage_group_ids);
        
        // 暂时不要条件
        //where id in ( select user_id from user_group_mapping where user_group_id in ($str))
        $sql = "select id, realname name from users";
        $raw = M()->query($sql, true);
        foreach ($raw as $v) {
            $rs[$v['id']] = $v['name'];
        }

        return $rs;
    }


}
?>