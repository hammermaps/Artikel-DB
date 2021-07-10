<?php

class PageAdd
{
    public function __construct(common $common)
    {
        global $notifications;

        if(!$common->getUsers()->is_logged()) {
            $common->page_login('admin_add');
            return;
        }

        $from_output = ['ean'=>'','name'=>'','tags'=>'','disabled'=>''];

        $rules = ['ean'  => 'required|numeric|exact_len,6',
            'name' => 'required|max_len,200',
            'tags' => 'required'];

        $filters = ['ean'  => 'trim',
            'name' => 'trim|sanitize_string',
            'tags' => 'trim|sanitize_string'];

        $do_post = $common->getValidator()->filter($_POST, $filters); $table = '';
        if($common->getValidator()->validate($do_post, $rules) === true) {
            $from_output['name'] = $do_post['name'];
            $from_output['tags'] = $do_post['tags'];
            $from_output['ean'] = $do_post['ean'];

            //SQL Insert
            $sql = "SELECT `id` FROM `artikel` WHERE `ean` = ".intval($do_post['ean']).";";
            $query = $common->getDatabase()->query($sql);
            if($query->getRowCount()) {
                $from_output['disabled'] = '';
                $_SESSION['ean'] = $do_post['ean'];
                $notifications->addError('Diese EAN: "'.$_SESSION['ean'].'" ist bereits belegt!');

                $common->getSmarty()->clearAllAssign();
                $entities = $common->getSmarty()->fetch(SCRIPT_PATH.'/template/add/search_entities.tpl');

                $common->getSmarty()->clearAllAssign();
                $common->getSmarty()->assign('entities',$entities);
                $table = $common->getSmarty()->fetch(SCRIPT_PATH.'/template/add/search_list.tpl');
            } else {
                $sql = "INSERT INTO `artikel` SET `ean` = ".intval($do_post['ean']).", `name` = '".
                    utf8_encode(htmlentities(str_replace(' ','{blank}',$do_post['name'])))."', `tags` = '".
                    utf8_encode(htmlentities(str_replace(' ','{blank}',$do_post['tags'])))."';";

                $common->sqlLoggerInsert(intval($do_post['ean']),
                    utf8_encode(htmlentities(str_replace(' ','{blank}',$do_post['name']))),
                    utf8_encode(htmlentities(str_replace(' ','{blank}',$do_post['tags']))));

                $common->getDatabase()->query($sql);
                $from_output['disabled'] = 'disabled';
                $notifications->addSuccess('Artikel wurde erfolgreich angelegt',2, 'add.html');
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

        $common->getSmarty()->clearAllAssign();
        $common->getSmarty()->assign('notifications',$notifications->display());
        $common->getSmarty()->assign('from',$from_output);
        $common->getSmarty()->assign('table',$table);
        $common->assign['index']['content'] = $common->getSmarty()->fetch(SCRIPT_PATH.'/template/add/add_from.tpl');
    }
}