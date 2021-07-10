<?php

class PageEdit
{
    public function __construct(common $common)
    {
        global $notifications;

        if(!$common->getUsers()->is_logged()) {
            $common->page_login('admin_edit');
            return;
        }

        //Edit From
        if(isset($_GET['id']) && intval($_GET['id']) >= 1) {
            $from_output = ['ean'=>'','name'=>'','tags'=>'','disabled'=>''];

            $rules = ['ean'  => 'required|numeric|exact_len,6',
                'name' => 'required|max_len,200',
                'tags' => 'required'];

            $filters = ['ean'  => 'trim',
                'name' => 'trim|sanitize_string',
                'tags' => 'trim|sanitize_string'];

            $do_post = $common->getValidator()->filter($_POST, $filters);
            if($common->getValidator()->validate($do_post, $rules) === true) {
                $from_output['name'] = $do_post['name'];
                $from_output['tags'] = $do_post['tags'];
                $from_output['ean'] = $do_post['ean'];

                //SQL Insert
                $sql = "SELECT `id` FROM `artikel` WHERE `id` = ".intval($_GET['id']).";";
                $query = $common->getDatabase()->query($sql);
                if($query->getRowCount()) {
                    $sql = "UPDATE `artikel` SET `ean` = ".
                        intval($do_post['ean']).", `name` = '".
                        utf8_encode(htmlentities(str_replace(' ','{blank}',$do_post['name']), ENT_COMPAT))."', `tags` = '".
                        utf8_encode(htmlentities(str_replace(' ','{blank}',$do_post['tags']), ENT_COMPAT))."' WHERE `id` = ".intval($_GET['id']).";";

                    $common->sqlLogger($sql);
                    $common->getDatabase()->query($sql);
                    $common->getCache()->deleteItemsByTag('data');
                    $from_output['disabled'] = 'disabled';
                    $notifications->addSuccess('Artikel wurde erfolgreich bearbeitet',2, 'edit.html');
                }
            } else {
                if($_POST) {
                    foreach ($common->getValidator()->get_readable_errors() as $error) {
                        $notifications->addError($error . '!');
                    }
                }

                $from_output['name'] = isset($do_post['name']) ? $do_post['name'] : '';
                $from_output['tags'] = isset($do_post['tags']) ? $do_post['tags'] : '';
                $from_output['ean'] = isset($do_post['ean']) ? $do_post['ean'] : '';
            }

            $sql = "SELECT * FROM `artikel` WHERE `id` = ".intval($_GET['id']).";";
            $query = $common->getDatabase()->query($sql);
            if($query->getRowCount()) {
                $get = $query->fetch();

                $from_output['name'] = str_replace('{blank}',' ',html_entity_decode(utf8_decode($get['name'])));
                $from_output['tags'] = str_replace('{blank}',' ',html_entity_decode(utf8_decode($get['tags'])));
                $from_output['ean'] = intval($get['ean']);

                $common->getSmarty()->clearAllAssign();
                $common->getSmarty()->assign('notifications',$notifications->display());
                $common->getSmarty()->assign('from',$from_output);
                $common->getSmarty()->assign('id',intval($_GET['id']));
                $common->assign['index']['content'] = $common->getSmarty()->fetch(SCRIPT_PATH.'/template/edit/edit_from.tpl');
                return;
            }
        }

        $common->getSmarty()->clearAllAssign();
        $entities = $common->getSmarty()->fetch(SCRIPT_PATH.'/template/edit/search_entities.tpl');

        $common->getSmarty()->clearAllAssign();
        $common->getSmarty()->assign('entities',$entities);
        $common->assign['index']['content'] = $common->getSmarty()->fetch(SCRIPT_PATH.'/template/edit/search_from.tpl');
    }
}