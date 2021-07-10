<?php


class PageSearch
{
    public function __construct(common $common)
    {
        $common->getSmarty()->clearAllAssign();
        $entities = $common->getSmarty()->fetch(SCRIPT_PATH.'/template/search/search_entities.tpl');

        $common->getSmarty()->clearAllAssign();
        $common->getSmarty()->assign('notifications','');
        $common->getSmarty()->assign('entities',$entities);
        $common->assign['index']['content'] = $common->getSmarty()->fetch(SCRIPT_PATH.'/template/search/search_from.tpl');

    }
}