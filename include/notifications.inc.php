<?php
/**
 * Created by PhpStorm.
 * User: Lucas
 * Date: 19.03.2018
 * Time: 21:23
 */

class notifications
{
    public $common;
    private $notifications;

    public function __construct(common $common)
    {
        $this->notifications = [];
        $this->common = $common;
    }

    public function addSuccess(string $msg,int $refresh=4, string $url = 'search.html') {
        $this->notifications[] = ['msg'=>$msg,'type'=>'alert-success','refresh'=>$refresh,'url'=>$url];
    }

    public function addError(string $msg) {
        $this->notifications[] = ['msg'=>$msg,'type'=>'alert-danger','refresh'=>0,'url'=>''];
    }

    public function display() {
        $notifications = '';
        foreach ($this->notifications as $data) {
            $this->common->smarty->clearAllAssign();
            $this->common->smarty->assign('notification',$data,true);
            $notifications .= $this->common->smarty->fetch(SCRIPT_PATH.'/template/notification/notification.tpl');
        }

        return $notifications;
    }
}