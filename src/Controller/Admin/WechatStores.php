<?php

namespace Miaoxing\WechatStore\Controller\Admin;

use Miaoxing\Plugin\BaseController;

class WechatStores extends BaseController
{
    protected $controllerName = '微信门店管理';

    protected $actionPermissions = [
        'sync' => '同步',
    ];

    protected $displayPageHeader = true;

    public function indexAction($req)
    {
        $shops = wei()->shop();

        // 分页
        $shops->limit($req['rows'])->page($req['page']);

        // 排序
        $shops->desc('id');

        $shops->andWhere('wechat_poi_id != 0');

        $shops->findAll();
        $data = $shops->toArray();

        return $this->suc([
            'data' => $data,
            'page' => $req['page'],
            'rows' => $req['rows'],
            'records' => $shops->count(),
        ]);
    }

    public function syncAction()
    {
        $counts = [
            'created' => 0,
            'updated' => 0,
        ];

        $account = wei()->wechatAccount->getCurrentAccount();
        $api = $account->createApiService();
        $ret = $api->getStoreList([
            'offset' => 0,
            'limit' => 50,
        ]);
        if ($ret['code'] !== 1) {
            return $ret;
        }

        $this->logger->info('Get wechat stores', $ret);
        foreach ($ret['business_list'] as $list) {
            $store = $list['base_info'];

            $shop = wei()->shop()->findOrInit(['wechat_poi_id' => $store['poi_id']]);
            $shop->isNew() ? $counts['created']++ : $counts['updated']++;

            // TODO 省市区
            $shop->create([
                'name' => $store['business_name'],
                'phone' => $store['telephone'],
                'address' => $store['address'],
                'categories' => $store['categories'],
                'lng' => $store['longitude'],
                'lat' => $store['latitude'],
                'city' => rtrim($store['city'], '市'),
                'province' => rtrim($store['province'], '省'),
                'photo_list' => $store['photo_list'],
                'open_time' => $store['open_time'],
            ]);
        }

        $message = vsprintf('同步完成,共本地新增了%s个,更新了%s个', $counts);

        return $this->suc($message);
    }
}
