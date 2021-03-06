<?php

class AttachmentsModel extends BaseModel {

    public function getAttachmentsByModuleId($module_name, $module_id) {
        if(!$module_name || !$module_id) {
            //log
            return NULL;
        }

        $table_name = ucfirst($module_name) . 'Attachments';
        $module_id_name = strtolower($module_name) . '_id';

        $data = D($table_name)->where($module_id_name . '='. $module_id)->order('id desc')->select();
        $user_ids = Utility::GetColumn($data, 'update_user_id');
        $users = Utility::Asscolumn(D('Users')->select(implode(',', $user_ids)));

        foreach ($data as $k => $n) {
            $data[$k]['update_user'] = $users[$n['update_user_id']];
        }
        return $data;
    }

    public function saveAttachment($module_name, $module_id, $file) {
        if(!$module_name || !$module_id) {
            //log
            return 0;
        }

        $table_name = ucfirst($module_name) . 'Attachments';
        $module_id_name = strtolower($module_name) . '_id';
        
        $note = D($table_name);

        $file[$module_id_name] = $module_id;
        $file['type'] = $module_name;

        $note->create($file);

        return $note->saveOrUpdate();
    }

    public function getModuleAttachments($module_name,$filter,$page,$size,$order) {

        $table_name = ucfirst($module_name) . 'Attachments';

        $_model = D($table_name);

        if(!$order) {
            $order = 'id desc';
        }

        $_model = $_model->where($filter)->order($order);
        if($page && $size) {
            $_model->page($page, $size);
        }
        $res = $_model->select();

        foreach($res as $k => $v) {            
            if($module_name=='user') {
                $res[$k]['related_name'] = 
                    '<a target="_blank" href="/user/detail/' . $v["user_id"] .'">'
                     . M("UserRecommends")->where('id='.$v['user_id'])->getField('name') . '</a>';
            }
            if($module_name=='project') {
                $res[$k]['related_name'] = 
                    '<a target="_blank" href="/project/detail/' . $v["project_id"] .'">'
                     . M("Projects")->where('id='.$v['project_id'])->getField('title') . '</a>';
            }

            $res[$k]['create_time'] = substr($res[$k]['create_time'],0,16);
            $res[$k]['size'] = formatBytes($res[$k]['size']);

            if($v['create_user_id']<2000) {
                $res[$k]['create_user_name'] = M("Users")->where('id='.$v['create_user_id'])->getField('realname');
            } else {
                $res[$k]['create_user_name'] = M("UserInfo")->where('id='.$v['create_user_id'])->getField('name');
            }
        }

        return $res;
    }
}

?>