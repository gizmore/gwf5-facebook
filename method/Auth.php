<?php
/**
 * Facebook OAuth connector.
 * @author gizmore
 * @since 4.0
 * @version 5.0
 */
final class Facebook_Auth extends GWF_MethodForm
{
	public function getUserType() { return 'ghost'; }
	
	public function execute()
	{
		if (isset($_GET['connectFB']))
		{
			return $this->onConnectFB();
		}
		return parent::execute();
	}
	
	public function createForm(GWF_Form $form)
	{
		$form->addFields(array(
			GDO_FBAuthButton::make(),
		));
	}
	
	private function onConnectFB()
	{
		$fb = Module_Facebook::instance()->getFacebook();
		$helper = $fb->getRedirectLoginHelper();
		$accessToken = $helper->getAccessToken();
		if ($accessToken)
		{
			$response = $fb->get('/me?fields=id,name,email', $accessToken);
			$user = GWF_OAuthToken::refresh($accessToken->getValue(), $response->getGraphUser()->asArray());

			$activated = $user->tempGet('justActivated');

			# Temp is cleared here
			$response = $this->authenticate(method('Login', 'Form'), $user);

			# Temp was in activation state?
			if ($activated)
			{
				GWF_Hook::call('UserActivated', $user);
				GWF_Hook::call('FBUserActivated', $user, substr($user->getVar('user_name'), 4));
			}
				
			return $this->message('msg_facebook_connected')->add($response);
		}
		return $this->error('err_facebook_connect');
	}
	
	private function authenticate(Login_Form $method, GWF_User $user)
	{
		return $method->loginSuccess($user);
	}
}
