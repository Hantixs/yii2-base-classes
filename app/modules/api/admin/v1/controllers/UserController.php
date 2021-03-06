<?php
/**
 * Created by PhpStorm.
 * User: zvinger
 * Date: 20.12.17
 * Time: 19:19
 */

namespace Zvinger\BaseClasses\app\modules\api\admin\v1\controllers;

use Zvinger\BaseClasses\app\helpers\error\ErrorMessageHelper;
use app\models\work\user\object\UserObject;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\OptionsAction;
use yii\web\BadRequestHttpException;
use Zvinger\BaseClasses\api\controllers\BaseApiController;
use Zvinger\BaseClasses\app\models\work\user\object\VendorUserObject;
use Zvinger\BaseClasses\app\modules\api\admin\v1\actions\user\create\UserCreateRequest;
use Zvinger\BaseClasses\app\modules\api\admin\v1\actions\user\update\UserUpdateRequest;
use Zvinger\BaseClasses\app\modules\api\admin\v1\components\user\models\UserApiAdminV1Model;
use Zvinger\BaseClasses\app\modules\api\admin\v1\controllers\base\BaseVendorAdminV1Controller;

class UserController extends BaseVendorAdminV1Controller
{
    public function actionIndex()
    {
        return $this->module->userComponent->convertUserObjectsToModelMultiple(VendorUserObject::find()->all());
    }

    /**
     * @param $id
     * @return UserApiAdminV1Model
     * @SWG\GET(path="/vendor/users/{userId}",
     *     tags={"user"},
     *     summary="Получение пользователя",
     *     @SWG\Parameter(
     *          in="path",
     *          name="userId",
     *          type="integer",
     *          required=true
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "Модель пользователя",
     *         @SWG\Schema(ref = "#/definitions/UserApiAdminV1Model")
     *     ),
     * )
     */
    public function actionView($id)
    {
        return $this->module->userComponent->convertUserObjectToModel(UserObject::findOne($id));
    }

    /**
     * @param $id
     * @return object
     * @throws BadRequestHttpException
     * @SWG\Put(path="/vendor/users/{userId}",
     *     tags={"user"},
     *     summary="Обновление пользователя",
     *     @SWG\Parameter(
     *          in="path",
     *          name="userId",
     *          type="integer",
     *          required=true
     *     ),
     *     @SWG\Parameter(
     *          in="body",
     *          name="body",
     *          @SWG\Schema(ref = "#/definitions/UserUpdateRequest")
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "Модель пользователя",
     *         @SWG\Schema(ref = "#/definitions/UserApiAdminV1Model")
     *     ),
     * )
     */
    public function actionUpdate($id)
    {
        $user = UserObject::findOne($id);
        /** @var UserUpdateRequest $request */
        $request = \Yii::configure(new UserUpdateRequest(), \Yii::$app->request->post());
        $this->module->userComponent->updateUser($id, $request);

        return $this->module->userComponent->convertUserObjectToModel($user);
    }

    /**
     * @return UserApiAdminV1Model
     * @throws \Zvinger\BaseClasses\app\exceptions\model\ModelValidateException
     * @SWG\Post(path="/vendor/users",
     *     tags={"user"},
     *     summary="Создание пользователя",
     *     @SWG\Parameter(
     *          in="body",
     *          name="body",
     *          @SWG\Schema(ref = "#/definitions/UserCreateRequest")
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "Модель пользователя",
     *         @SWG\Schema(ref = "#/definitions/UserApiAdminV1Model")
     *     ),
     * )
     */
    public function actionCreate()
    {
        /** @var UserCreateRequest $request */
        $request = \Yii::configure(new UserCreateRequest(), \Yii::$app->request->post());
        $userId = $this->module->userComponent->createUser($request);

        return $this->module->userComponent->convertUserIdToModel($userId);
    }
}