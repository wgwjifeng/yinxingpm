<?php

class UserRecommendsModel extends BaseModel {

    function _after_select(&$resultSet,$options){
        foreach ($resultSet as $k => $v) {
            $resultSet[$k] = $this->_after_find($v);
        }
        return $resultSet;
     }

    function  _after_find(&$result,$options){
        if($result) {
            $result['pm_display_toggle'] = unserialize($result['pm_display_toggle']);            
            $result['status_name'] = M("UserStatuses")->where('id=%d',$result['status'])->getField('name');
            $result['address'] = $result['address_province'] . '&nbsp;' . $result['address_city'] . '&nbsp;' . $result['address_area'] . '&nbsp;' . $result['address'];
            $result['recommend_user'] = $ru = D("UserInfo")->getById($result['recommend_user_id']);

            // 更新推荐人信息
            $columns = D("CustomColumns")->getUserRecommendModuleColumns();
            foreach($columns as $k=>$v) {
                if(strpos($k,'recommender_')===0) {
                    $result[$k] = $ru[substr($k,12)];
                }
            }
        }
        return $result;
    }

    private function _buildFilter($filter) {

        if(!$filter['status'] || $filter['status']==='all') {
            $filter['status'] = array('gt', 1);
        }

        return $filter;
    }

    public function getCount($filter) {
        $filter = $this->_buildFilter($filter);
        $count = $this->where($filter)->count();

        return $count;
    }


    public function gets($filter, $page=0, $size=0, $order = null) {

        $filter = $this->_buildFilter($filter);
        $user_obj = D("UserRecommends");
        if(!$order) {
            $order = 'update_time desc';
        }

        $users = $user_obj->where($filter)->order($order);
        if($page && $size) {
            $users->page($page, $size);
        }
        $result = $users->select();

        // var_dump($users->getLastSql());
        // return $result;

        foreach($result as $k => $v) {
            $result[$k] = $this->_rich($v);
        }

        return $result;
    }


    public function getRecommend($id) {
        $u = $this->find($id);
        if(!$u) return null;

        return $this->_rich($u);
    }

    private function _rich($one) {
        if(!$one) return NULL;

        if(has_submit_apply($one['status'])) {
            $one['userinfo'] = D("UserInfo")->getByInviteCode($one['invite_code']);
        }

        return $one;
    }

    public function getStatusCountMap() {
        $filter['status'] = array('gt',1);
        // $filter['apply_type_id'] = $apply_type_id;
        return $this->where($filter)->group('status')->getField('status,count(*) count',true);
    } 



    //传入一个数组，返回按照出现数量排序的二维数组
    private function _getSortRank($target) {

        //统计数组元素出现的次数
        $count_res = array_count_values($target);     
        //按数组的值降序排列
        arsort($count_res);
        return $count_res;

    }


    //得到推荐人的所有去除重复后的ID数组
    private function _getAllRecommedID($filter) {

        //初始化空数组，用来保存不重复的推荐人ID
        $recommend_id = array();
        //查找包括成为银杏伙伴的被推荐人
        $filter_str = $this->_filterToString($filter);
        $target = $this->query("SELECT distinct user_info.id
                                FROM  `user_recommends` ,  `user_info`
                                WHERE user_recommends.recommend_user_id = user_info.id" . $filter_str);
        foreach ($target as $key => $val) {
                array_push($recommend_id, $val['id']);
        }
        return $recommend_id;
    }

     //传入一个数组，以及限制数量，返回截取后的数组
    private function _silceByMax($target, $max = 10) {
        //如果当前数量小于指定的最大数量，那么直接输出，否则切出最大数量个数
        return count($target) <= $max?$target:array_slice($target, 0, $max);
    }

    //得到推荐人的数量，被推荐人成为银杏伙伴也要统计
    public function getRecommedCount($filter) {
        $recommend_id = $this->_getAllRecommedID($filter);
        return count($recommend_id);
    }

    //得到推荐人的详细信息
    public function getRecommendInfos($filter, $page=0, $size=0, $order = 'id asc') {
        $user_obj = D("UserInfo");
        $users = $user_obj->where($filter)->order($order);
        if($page && $size) {
            $users->page($page, $size);
        }
        $result = $users->select();
        return $result;
        // return $this->_rich($data, $rich);
    }

    //这里指的是满足条件的推荐人所推荐候选人的总数量
    public function getRecommendCandidateCount($filter) {
        $filter_str = $this->_filterToString($filter);
        $target = $this->query("SELECT distinct user_recommends.id
                                FROM  `user_recommends` ,  `user_info`
                                WHERE user_recommends.recommend_user_id = user_info.id" . $filter_str);
        return count($target);
    }


    //推荐人按照性别排名
    public function getRecommendGenderRank($filter) {
        $filter_str = $this->_filterToString($filter);
        //要统计的数组
        $target = $this->table("user_recommends,user_info")->distinct(true)->where("user_recommends.recommend_user_id =
            user_info.id" . $filter_str)->getField("user_info.id,gender",true);
        $rank_result = $this->_getSortRank($target);
        //如果不存在男或者女，则增补数量0
        if (!$rank_result['男']) {
            $rank_result['男'] = 0;
        }
        if (!$rank_result['女']) {
            $rank_result['女'] = 0;
        }
        return $rank_result;
    }


    //按照推荐人推荐的候选人数量排名
    public function getRecommendNumRank($filter) {
        $filter_str = $this->_filterToString($filter);
        $recommed_name = array();
        //要统计的数组
        $target = $this->query("SELECT user_info.name
                                FROM  `user_recommends` ,  `user_info`
                                WHERE user_recommends.recommend_user_id = user_info.id" . $filter_str);
        //取出name字段，然后变为一维数组
        foreach ($target as $val) {
            array_push($recommed_name, $val['name']);
        }
        //按照出现的个数统计
        $rank_result = $this->_getSortRank($recommed_name);
        return $this->_silceByMax($rank_result);
    }


    //得到候选人的数量，不包括银杏伙伴,不管成功失败均统计
    public function getCandidateCount($filter) {
        $filter["user_recommends.status"] = array("lt", 99);
        $result = $this->where($filter)->count();
        return $result;
    }


    //候选人按照省份排名
    public function getCandidateProvinceRank($filter) {
        $filter["user_recommends.status"] = array("lt", 99);
        //要统计的数组
        $target = $this->where($filter)->getField("address_province",true);
        $rank_result = $this->_getSortRank($target);
        return $this->_silceByMax($rank_result);
    }

    //把filter转化为字符串，多表链接查询时构造sql语句，因为直接用thinkphp方法会出现一些问题
    private function _filterToString($filter) {
        $filter_items = array();
        foreach ($filter as $key => $value) {
            array_push($filter_items, "$key ".$value[0]." '".$value[1]."'");
        }
        $str = join(" and ",$filter_items);
        //如果不为空，在前面加and
        if ($str) {
            $str = " and " . $str;
        }
        return $str;
    }

    //返回候选人的性别统计，没有成为合作伙伴
    public function getCandidateGenderRank($filter) {
        $filter_str = $this->_filterToString($filter);
        //通过Invite_code来获得所有的候选人,两个表中invite_code一致且不为空，status小于99，即没有成为合作伙伴
        $target = $this->table("user_recommends,user_info")->where("user_recommends.invite_code =
            user_info.invite_code and user_recommends.invite_code !='' and user_recommends.status < 99 "
            . $filter_str)->getField("gender",true);
        $rank_result = $this->_getSortRank($target);
        //如果不存在男或者女，则增补数量0
        if (!$rank_result['男']) {
            $rank_result['男'] = 0;
        }
        if (!$rank_result['女']) {
            $rank_result['女'] = 0;
        }
        return $rank_result;
    }


    //返回候选人的年龄统计，没有成为合作伙伴
    public function getCandidateAgeRank($filter) {
        $filter_str = $this->_filterToString($filter);
        //通过Invite_code来获得所有的候选人,两个表中invite_code一致且不为空，status小于99，即没有成为合作伙伴
        $target = $this->table("user_recommends,user_info")->where("user_recommends.invite_code =
            user_info.invite_code and user_recommends.invite_code !='' and user_recommends.status < 99" . $filter_str)->getField("birthday",true);
        $ages = array();
        foreach ($target as $val) {
            //echo intval(date("Y",time())) ."-" . intval(explode("-", $val)[0])."<br>";
            array_push($ages, intval(date("Y",time())) - intval(explode("-", $val)[0]));
        }
        $rank_result = $this->_getSortRank($ages);
        $age_seg = array("20-25" => 0,
                        "26-30" => 0,
                        "31-35" => 0,
                        "36-40" => 0
                        );
        foreach ($ages as $age) {
            if ($age >= 20 && $age <= 40) {
                if ($age <= 25) {
                    $age_seg['20-25'] += 1;
                } else if ($age <= 30) {
                    $age_seg['26-30'] += 1;
                } else if ($age <= 35) {
                    $age_seg['31-35'] += 1;
                } else {
                    $age_seg['36-40'] += 1;
                }
            }
        }
        return $age_seg;
    }

    //得到银杏伙伴的数量
    public function getPartnerCount($filter) {
        $filter["user_recommends.status"] = array("eq", 99);
        $result = $this->where($filter)->select();
        return count($result);
    }


    //银杏伙伴按照省份排名
    public function getPartnerProvinceRank($filter) {
        $filter["user_recommends.status"] = array("eq", 99);
        //要统计的数组
        $target = $this->where($filter)->getField("address_province",true);
        $rank_result = $this->_getSortRank($target);
        return $this->_silceByMax($rank_result);
    }



    //银杏伙伴按照性别排名
    public function getPartnerGenderRank($filter) {
        $filter_str = $this->_filterToString($filter);
        //通过Invite_code来获得所有的银杏伙伴其他信息
        $target = $this->table("user_recommends,user_info")->where("user_recommends.invite_code =
            user_info.invite_code and user_recommends.invite_code !='' and user_recommends.status = 99" . $filter_str)->getField("gender",true);
        $rank_result = $this->_getSortRank($target);
        //如果不存在男或者女，则增补数量0
        if (!$rank_result['男']) {
            $rank_result['男'] = 0;
        }
        if (!$rank_result['女']) {
            $rank_result['女'] = 0;
        }
        return $rank_result;
    }

    //统计数据通用方法，传入status获得人数
    private function _getNumByStatus($status) {
        return $this->where("status=".$status)->getField("count(*)");
    }

    //统计所有类别的数据
    public function getNumOfAllStatus() {
        //保存状态和数量对应关系的数组
        $num_of_status = array();
        //获得各个状态及代号
        $user_statuses = D('UserStatuses')->getStatusIdNameMap();
        $status_count = count($user_statuses);
        foreach ($user_statuses as $key => $value) {
           array_push($num_of_status,array($value => intval($this->_getNumByStatus($key))));
        }
        return $num_of_status;

        /*
        结果类似如下内容：
        array (size=11)
          0 =>
            array (size=1)
              '推荐表审核' => int 1
          1 =>
            array (size=1)
              '待背景调查' => int 2

        */
    }
}
?>