<?php

namespace Miaoxing\WechatStore;

use Miaoxing\Plugin\BasePlugin;

class Plugin extends BasePlugin
{
    /**
     * {@inheritdoc}
     */
    protected $name = '微信小程序门店';

    /**
     * {@inheritdoc}
     */
    protected $description = '同步小程序门店到本地门店中';

    public function onAdminShopList()
    {
        $this->display();
    }
}
