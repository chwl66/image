<?php


namespace app\controller\ajax;


use app\BaseController;
use app\model\Storage;
use think\facade\Request;

class AdminStorage extends BaseController
{

    public function get()
    {
        $param = Request::param();
        $page = empty($param['page']) ? 1 : $param['page'];
        $limit = empty($param['limit']) ? 10 : $param['limit'];

        $model = Storage::order('id', 'asc');

        if (!empty($param['cdn'])) {
            $model->where('cdn', 'LIKE', '%' . $param['cdn'] . '%');
        }
        if (!empty($param['name'])) {

            $model->where('name', 'LIKE', '%' . $param['name'] . '%');
        }
        if (!empty($param['driver'])) {

            $model->where('driver', 'LIKE', '%' . $param['driver'] . '%');
        }
        $count = $model->count();
        $data = $model->page($page, $limit)->select();
        return json_table(200, 'success',
            $data,
            $page,
            $limit,
            $count);
    }

    public function update()

    {
        $param = Request::param();
        $retentionDomain = base64_decode(hidove_config_get('system.other.retentionDomain'));
        $retentionDomainArr = explode("\n", $retentionDomain);

        foreach ($param as $key => $value) {
            if ($key != 'cdn' && $key != 'distribute') {
                continue;
            }
            $parse_url = parse_url($value);
            if (!empty($parse_url['host'])) {
                $cdnDomain = $parse_url['host'];
            } else {
                return msg(400, 'CDN加速域名添加错误，正确格式[http://xxx.com]');
            }
            foreach ($retentionDomainArr as $v) {
                $pattern = '~' . str_replace('*.', '[^\s]+', $v) . '$~';
                if (preg_match($pattern, $cdnDomain) != 0 && !empty($v))
                    return msg(400, '[' . $cdnDomain . ']该域名为系统保留域名，禁止绑定');
            }
        }
        $id = $param['id'];
        unset($param['id']);
        $save = Storage::where('id', $id)->save($param);
        if ($save) {
            return msg(200, 'success', $param);
        }
        return msg(400, '更新失败');
    }

    public function create()
    {
        $param = Request::param();
        $model = new Storage();
        $model->name = strtolower(empty($param['name']) ? 'cdn' . mt_rand(1, 1000) : $param['name']);
        $model->cdn = empty($param['cdn']) ? 'https://www.110.cn' : $param['cdn'];
        $model->data = empty($param['data']) ? [] : $param['data'];
        $model->driver = strtolower(empty($param['driver']) ? 'ftp' : $param['driver']);
        $model->create_time = time();
        $model->update_time = time();
        if ($model->save()) {
            return msg(200, 'success');
        } else {
            return msg(400, '新增失败');
        }
    }

    public function delete()
    {
        $id = Request::param('id');
        if (empty($id)) {
            return msg(400, 'The id can\'t be null!');
        }
        $idList = [];
        if(!is_array($id)){
            $idList[] = $id;
        }else{
            $idList = $id;
        }
        foreach ($idList as $value){
            try {
                Storage::where('id', $value)->delete();
            } catch (\Exception $e) {
            }
        }
        return msg(200, 'success');

    }
}