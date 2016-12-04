<?php
final class Friendlist_Accept extends GWF_Method
{
	public function getHTAccess()
	{
		return 'RewriteRule ^friendship_accept$ index.php?mo=Friendlist&me=Request [QSA]'.PHP_EOL;
	}

	public function execute()
	{
		if (isset($_POST['request']))
		{
			return $this->onRequest();
		}
		return $this->templateRequest();
	}

	public function form()
	{
		$data = array(
				'to' => array(GWF_Form::STRING, Common::getGetString('to'), $this->module->lang('th_buddy')),
				'request' => array(GWF_Form::SUBMIT, $this->module->lang('btn_request')),
		);
		return new GWF_Form($this, $data);
	}

	public function templateRequest()
	{
		$form = $this->form();
		$tVars = array(
				'form' => $form->templateY($this->module->lang('ft_request')),
		);
		return $this->module->template('request.php', $tVars);
	}

	public function onRequest()
	{
		$form = $this->form();
		if (false !== ($error = $form->validate($this->module)))
		{
			return $error . $this->templateRequest();
		}
		return $this->module->message('msg_requested');
	}

}
