<?php


namespace app\controller\ajax;


use app\BaseController;
use app\controller\ajax\provider\ImagesProvider;
use app\model\Folders;
use app\model\Image;
use app\model\User;
use think\facade\Request;
use think\facade\Session;

class UserImages extends BaseController
{
    private $Hidove;

    protected function initialize()
    {
        $userId = Session::get('userId');
        $this->Hidove['user'] = User::where('id', $userId)->find();
    }


    public function imageSearch()
    {

        $page = empty(Request::param('page')) ? 1 : Request::param('page');
        $limit = empty(Request::param('limit')) ? 12 : Request::param('limit');

        $folderId = empty(Request::param('folder')) ? 0 : Request::param('folder');

        $keyword = empty(Request::param('keyword')) ? null : Request::param('keyword');
        $model = Image::order('create_time', 'desc')
            ->where([
                'user_id' => $this->Hidove['user']['id'],
            ]);

        $totalImage = $model->count();
        if (!empty($keyword)) {
            $model = $model->where([
                ['user_id', '=', $this->Hidove['user']['id']],
                ['filename', 'LIKE', '%' . $keyword . '%'],
            ]);
            if ($folderId !== 0){
                $model = $model->where([
                    ['folder_id', '=', $folderId],
                ]);
            }
        } else {
            $model = $model->where([
                ['user_id', '=', $this->Hidove['user']['id']],
                ['folder_id', '=', $folderId],
            ]);
        }
        $count = $model->count();
        $images = $model->page($page, $limit)
            ->select()
            ->toArray();

        array_walk($images, function (&$value) {
            $value['distribute'] = splice_distribute_url($value['signatures']);
            $value['info'] = Request::domain() . '/info/' . $value['signatures'];
        });
        $json = [
            'code' => 200,
            'msg' => 'success',
            'data' => $images,
            'count' => $count,
            'page' => $page,
            'limit' => $limit,
            'keyword' => $keyword,
            'totalImage' => $totalImage,
        ];
        return json($json);
    }


    public function folders()
    {
        $id = empty(Request::param('id')) ? 0 : Request::param('id');
        $parent = Folders::where([
            ['user_id', '=', $this->Hidove['user']['id']],
            ['parent_id', '=', $id]
        ])->select()->toArray();
        $folders = Folders::where([
            ['user_id', '=', $this->Hidove['user']['id']],
            ['parent_id', '<>', $id]
        ])->select()->toArray();
        foreach ($folders as $value) {
            foreach ($parent as $k => &$v) {
                if ($v['id'] == $value['parent_id']) {
                    $v['child'][] = $value;
                }
            }
        }
        $json = [
            'code' => 200,
            'msg' => 'success',
            'data' => $parent,
        ];
        return json($json);
    }

    public function buildFolder()
    {
        $name = empty(Request::param('name')) ? '大姐姐' : Request::param('name');
        $parentId = empty(Request::param('parentId')) ? 0 : Request::param('parentId');
        $result = Folders::create([
            'user_id' => $this->Hidove['user']['id'],
            'name' => $name,
            'parent_id' => $parentId,
        ]);
        if ($result) {
            return msg(200, 'success', ['name' => $name, 'parentId' => $parentId]);
        }
        return msg(400, 'error');
    }

    public function moveImage()
    {
        $imageId = empty(Request::param('imageId')) ? '大姐姐' : Request::param('imageId');
        $folderId = empty(Request::param('folderId')) ? 0 : Request::param('folderId');
        $result = Image::where([
            ['user_id', '=', $this->Hidove['user']['id']],
            ['id', 'in', $imageId],
        ])->update(['folder_id' => $folderId]);
        if ($result) {
            return msg(200, '移动成功', ['id' => $imageId, 'folder' => $folderId]);
        }
        return msg(400, '移动失败');
    }


    public function deleteImage()
    {

        $imageId = Request::param('id');

        //是否强制删除 === 1 强制删除
        $force = Request::param('force');

        if (!is_array($imageId)) {
            $imageId = explode(',', $imageId);
        }
        try {
            (new ImagesProvider())->deleteImages($imageId, $this->Hidove['user']->id, $force);
            return msg(200, '删除成功');

        } catch (\Exception $e) {
            return msg(400, $e->getMessage());
        }
    }

    public function deleteFolder()
    {

        $folderId = Request::param('id');

        $folder = Folders::where([
            ['id', '=', $folderId],
            ['user_id', '=', $this->Hidove['user']->id],
        ])->findOrEmpty();
        if ($folder->isEmpty()) {
            return msg(400, '目录不存在');
        }
        if (!$folder->children->isEmpty() || !$folder->images->isEmpty()) {
            return msg(400, '请先清空目录后再进行删除操作');
        }
        $result = $folder->delete();
        if ($result === true) {
            return msg(200, '删除成功');
        }
        return msg(400, $result);
    }

    public function folderRename()
    {

        $id = Request::param('id');
        if (empty($id)) {
            return msg(400, 'The id can\'t be null!');
        }
        $newName = empty(Request::param('newName')) ? '大姐姐' : Request::param('newName');
        $result = Folders::where([
            ['user_id', '=', $this->Hidove['user']['id']],
            ['id', '=', $id]
        ])->update(['name' => $newName]);
        if (!empty($result)) {
            return msg(200, 'success', $result);
        } else {
            return msg(400, 'error', $result);
        }
    }

    public function updateImageInfo()
    {
        $param = Request::only(['id', 'folder_id', 'filename', 'apiType']);
        $model = Image::where('id', $param['id'])->find();

        $apiType = $param['apiType'];
        unset($param['id']);
        unset($param['apiType']);
        $updateInfo = (new ImagesProvider())->updateInfo($model, $param, $apiType);
        if ($updateInfo === true) {
            return msg(200, 'success');
        }
        return msg(400, $updateInfo);
    }


}