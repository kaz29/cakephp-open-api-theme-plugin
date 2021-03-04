<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Http\Exception\MethodNotAllowedException;

/**
 * Articles Controller
 *
 * @property \App\Model\Table\ArticlesTable $Articles
 * @method \App\Model\Entity\Article[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ArticlesController extends AppController
{
    /**
     * Index method
     *
     * @OA\Get(
     *     path="/api/articles.json",
     *     summary="Articles index",
     *     description="Articles index",
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
     *                  @OA\Items(
     *                      allOf={
     *                          @OA\Schema(ref="#/components/schemas/Article"),
     *                          @OA\Schema(
     *                              @OA\Property(
     *                                  property="user",
     *                                  ref="#/components/schemas/User",
     *                                  description="User Entity",
     *                              ),
     *                          ),
     *                      },
     *                  ),
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

        $this->Crud->on('beforePaginate', function (Event $event) {
            $event->getSubject()->query->contain(['Users']);
        });

        return $this->Crud->execute();
    }

    /**
     * View method
     *
     * @OA\Get(
     *     path="/api/articles/{id}.json",
     *     summary="Get Article",
     *     description="Get Article",
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
     *                      @OA\Schema(ref="#/components/schemas/Article"),
     *                      @OA\Schema(
     *                          @OA\Property(
     *                              property="user",
     *                              ref="#/components/schemas/User",
     *                              description="User Entity",
     *                          ),
     *                          @OA\Property(
     *                              property="tags",
     *                              type="array",
     *                              @OA\Items(ref="#/components/schemas/Tag"),
     *                              description="Tag Entities",
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
     * @param string|null $id Article id.
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        if (!$this->request->is('get')) {
            throw new MethodNotAllowedException();
        }

        $this->Crud->on('beforeFind', function (Event $event) {
            $event->getSubject()->query->contain(['Users', 'Tags']);
        });

        return $this->Crud->execute();
    }

    /**
     * Add method
     *
     * @OA\Post(
     *     path="/api/articles/add.json",
     *     summary="Add article",
     *     description="Add article",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="user_id",
     *                  type="integer",
     *                  format="int32",
     *                  description="",
     *              ),
     *              @OA\Property(
     *                  property="title",
     *                  type="string",
     *                  description="",
     *              ),
     *              @OA\Property(
     *                  property="slug",
     *                  type="string",
     *                  description="",
     *              ),
     *              @OA\Property(
     *                  property="body",
     *                  type="string",
     *                  description="",
     *              ),
     *              @OA\Property(
     *                  property="published",
     *                  type="boolean",
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
     *                      description="Added article id",
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
     *     path="/api/articles/{id}.json",
     *     summary="Edit article",
     *     description="Edit article",
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
     *                  property="user_id",
     *                  type="integer",
     *                  format="int32",
     *                  description="",
     *              ),
     *              @OA\Property(
     *                  property="title",
     *                  type="string",
     *                  description="",
     *              ),
     *              @OA\Property(
     *                  property="slug",
     *                  type="string",
     *                  description="",
     *              ),
     *              @OA\Property(
     *                  property="body",
     *                  type="string",
     *                  description="",
     *              ),
     *              @OA\Property(
     *                  property="published",
     *                  type="boolean",
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
     *                  ref="#/components/schemas/Article",
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
     * @param string|null $id Article id.
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
     *     path="/api/articles/{id}.json",
     *     summary="Delete article",
     *     description="Delete article",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         @OA\Schema(
     *             type="integer",
     *         ),
     *         required=true,
     *         description="Article id",
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
     * @param string|null $id Article id.
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
