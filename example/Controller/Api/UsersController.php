<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Http\Exception\MethodNotAllowedException;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 * @method \App\Model\Entity\User[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class UsersController extends AppController
{
    /**
     * Index method
     *
     * @OA\Get(
     *     path="/api/users.json",
     *     summary="Users index",
     *     description="Users index",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="number",
     *         ),
     *         description=""
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="number",
     *         ),
     *         description=""
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean",
     *                  default=true,
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/components/schemas/User"),
     *              ),
     *              @OA\Property(
     *                  property="pagination",
     *                  ref="#/components/schemas/Pagination",
     *              ),
     *         ),
     *     ),
     * )
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function index()
    {
        if (!$this->request->is('get')) {
            throw new MethodNotAllowedException();
        }

        return $this->Crud->execute();
    }

    /**
     * View method
     *
     * @OA\Get(
     *     path="/api/users/{id}.json",
     *     summary="Get User",
     *     description="Get User",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="number",
     *         ),
     *         description=""
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean",
     *                  default=true,
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  allOf={
     *                      @OA\Schema(ref="#/components/schemas/User"),
     *                      @OA\Schema(
     *                          @OA\Property(
     *                              property="articles",
     *                              type="array",
     *                              @OA\Items(ref="#/components/schemas/Article"),
     *                              description="Article Entities",
     *                          ),
     *                      ),
     *                  },
     *              ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *     ),
     * )
     * @param string|null $id User id.
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        if (!$this->request->is('get')) {
            throw new MethodNotAllowedException();
        }

        $this->Crud->on('beforeFind', function (Event $event) {
            $event->getSubject()->query->contain(['Articles']);
        });

        return $this->Crud->execute();
    }

    /**
     * Add method
     *
     * @OA\Post(
     *     path="/api/users/add.json",
     *     summary="Add user",
     *     description="Add user",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="email",
     *                  type="string",
     *                  description="",
     *              ),
     *              @OA\Property(
     *                  property="password",
     *                  type="string",
     *                  description="",
     *              ),
     *          ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="successful operation",
     *         @OA\JsonContent(
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean",
     *                  default=true,
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(
     *                      property="id",
     *                      type="integer",
     *                      description="Added user id",
     *                  ),
     *              ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not Found",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ValidationErrorResponse",
     *         ),
     *     ),
     * )
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function add()
    {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        
        return $this->Crud->execute();
    }

    /**
     * Edit method
     *
     * @OA\Put(
     *     path="/api/users/{id}.json",
     *     summary="Edit user",
     *     description="Edit user",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         @OA\Schema(
     *             type="integer",
     *         ),
     *         required=true,
     *         description="",
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="email",
     *                  type="string",
     *                  description="",
     *              ),
     *              @OA\Property(
     *                  property="password",
     *                  type="string",
     *                  description="",
     *              ),
     *          ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean",
     *                  default=true,
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/components/schemas/User",
     *              ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             ref="#/components/schemas/ValidationErrorResponse",
     *         ),
     *     ),
     * )
     * @param string|null $id User id.
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        if (!$this->request->is(['patch', 'put'])) {
            throw new MethodNotAllowedException();
        }

        return $this->Crud->execute();
    }

    /**
     * Delete method
     *
     * @OA\Delete(
     *     path="/api/users/{id}.json",
     *     summary="Delete user",
     *     description="Delete user",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         @OA\Schema(
     *             type="integer",
     *         ),
     *         required=true,
     *         description="User id",
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean",
     *                  default=true,
     *              ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found",
     *     ),
     * )
     * @param string|null $id User id.
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        if (!$this->request->is('delete')) {
            throw new MethodNotAllowedException();
        }

        return $this->Crud->execute();
    }
}
