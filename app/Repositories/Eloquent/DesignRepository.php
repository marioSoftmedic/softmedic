<?php

namespace App\Repositories\Eloquent;

use App\Models\Design;
use App\Repositories\Contracts\IDesign;
use App\Repositories\Eloquent\BaseRepository;

class DesignRepository extends BaseRepository implements IDesign
{
    public function model()
    {
        return Design:: class;  //App\Models\Design
    }

    public function applyTags($id, array $data)
    {
        $design = $this->find($id);
        $design->retag($data);
    }

    public function addComment($designId, array $data)
    {
        //get the design for which we want to create a comment
        $design = $this->find($designId);

        //create a comment for the design
        $comment = $design->comments()->create($data);

        return $comment;
    }

    public function like($id)
    {
        $design = $this->model->findOrFail($id);
        if ($design->isLikedByUser(auth()->id())) {
            $design->unLike();
        } else {
            $design->like();
        }
    }

    public function isLikedByUser($designId)
    {
        $design = $this->model->findOrFail($designId);

        return $design->isLikedByUser(auth()->id());
    }
}
