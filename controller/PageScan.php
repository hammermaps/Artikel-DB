<?php


class PageScan
{
    public function __construct(common $common)
    {
        $common->getSmarty()->clearAllAssign();

        //  $this->smarty->assign('entities',$entities);
        $common->assign['index']['content'] = $common->getSmarty()->fetch(SCRIPT_PATH.'/template/scan/scanner.tpl');

    }
}