<?php

class BeginRequest extends CBehavior
{

    public function attach($owner)
    {
        $owner->attachEventHandler('onBeginRequest', array($this, 'handleBeginRequest'));
    }

    public function handleBeginRequest($event)
    {
        $app  = Yii::app();
        $user = $app->user;

        if (isset($_POST['_lang']))
        {
            $app->language = $_POST['_lang'];
            $user->setState('_lang', $_POST['_lang']);
            $cookie = new CHttpCookie('_lang', $_POST['_lang']);
            $cookie->expire = time() + (60 * 60 * 24 * 365); // (1 year)
            $app->request->cookies['_lang'] = $cookie;
            Yii::import("application.components.BackendMenu");
            BackendMenu::refreshXmlMenu();
        }
        else if ($user->hasState('_lang'))
            $app->language = $app->user->getState('_lang');
        else if (isset($app->request->cookies['_lang']))
            $app->language = $app->request->cookies['_lang']->value;
    }
}
