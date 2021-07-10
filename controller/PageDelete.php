<?php

class PageDelete
{
    public function __construct(common $common)
    {
        global $notifications;

        if(!$common->getUsers()->is_logged()) {
            $common->page_login('admin_edit');
            return;
        }

        if(isset($_GET['id']) && intval($_GET['id']) >= 1) {
            $sql = "SELECT `id` FROM `artikel` WHERE `id` = ".intval($_GET['id']).";";
            $query = $common->getDatabase()->query($sql);
            if($query->getRowCount()) {
                $sql = "DELETE FROM `artikel` WHERE `id` = ".intval($_GET['id']).";";
                $common->sqlLogger($sql);
                $common->getDatabase()->query($sql);
                $common->getCache()->deleteItemsByTag('data');
                $notifications->addSuccess('Artikel wurde erfolgreich gelöscht',2, 'edit.html');
            }
        }

        $common->getSmarty()->clearAllAssign();
        $entities = $common->getSmarty()->fetch(SCRIPT_PATH.'/template/edit/search_entities.tpl');

        $common->getSmarty()->clearAllAssign();
        $common->getSmarty()->assign('notifications',$notifications->display());
        $common->getSmarty()->assign('entities',$entities);
        $common->assign['index']['content'] = $common->getSmarty()->fetch(SCRIPT_PATH.'/template/edit/search_from.tpl');
    }
}