<?php
    namespace App\Helpers;
    use App\Models\User;
    use Illuminate\Database\Eloquent\ModelNotFoundException;
    use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

    function validateAgent($agent_id) {
        try {
            return User::findOrFail($agent_id);
        } catch (ModelNotFoundException $e) {
            throw new NotFoundHttpException(trans('msg.detail.not-found', ['entity' => 'Agent']));
        }
    }
?>