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
			if ($token = GWF_OAuthToken::refresh($accessToken->getValue(), $response->getGraphUser()->asArray()))
			{
				$response = $this->authenticate(method('Login', 'Form'), $token);
				return $this->message('msg_facebook_connected')->add($response);
			}
		}
		return $this->error('err_facebook_connect');
	}
	
	private function authenticate(Login_Form $method, GWF_OAuthToken $token)
	{
		return $method->loginSuccess($token->getUser());
	}
}
