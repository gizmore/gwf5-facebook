<?php
final class GWS_Facebook extends GWS_Command
{
    public function execute(GWS_Message $msg)
    {
        $fbUID = $msg->readString();
        $fbExpire = time() + $msg->read32u();
        $fbAccessToken = $msg->readString();
        
        $fb = Module_Facebook::instance()->getFacebook();
        $fb->setDefaultAccessToken($fbAccessToken);
        $helper = $fb->getRedirectLoginHelper();
        $accessToken = $helper->getAccessToken();
        $this->onAccess($accessToken, method('Facebook', 'Auth'));
    }
    
    public function onAccess($accessToken, Facebook_Auth $method)
    {
        $method->gotAccessToken($accessToken);
        
        GWF_User::$CURRENT = $user = GWF_Session::instance()->getUser();
        GWF_Session::reset();
        $msg->replyBinary($msg->cmd(), $this->userToBinary($user));
    }

    
}

GWS_Commands::register(0x0111, new GWS_Facebook());
