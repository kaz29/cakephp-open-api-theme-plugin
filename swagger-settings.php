<?php
/**
 * @OA\Info(
 *   version="1.0.0",
 *   title="App",
 *   description="API document",
 *   @OA\Contact(email="info@example.com"),
 *   @OA\License(
 *     name="Example Inc.",
 *     url="https://www.example.com/",
 *   ),
 * )
 *
 * @OA\Server(
 *     description="localhost",
 *     url="http://localhost"
 * )
 * 
 * @OA\Schema(
 *      schema="Pagination",
 *      title="Pagination information",
 *      @OA\Property(
 *          property="page_count",
 *          type="integer",
 *          description="Total page",
 *      ),
 *      @OA\Property(
 *          property="current_page",
 *          type="integer",
 *          description="Current page",
 *      ),
 *      @OA\Property(
 *          property="has_prev_page",
 *          type="boolean",
 *          description="Has previous page",
 *      ),
 *      @OA\Property(
 *          property="has_next_page",
 *          type="boolean",
 *          description="Has next page",
 *      ),
 *      @OA\Property(
 *          property="count",
 *          type="integer",
 *          description="Item count",
 *      ),
 *      @OA\Property(
 *          property="limit",
 *          type="integer",
 *          description="Page limit",
 *      ),
 * )
 * @OA\Schema(
 *      schema="ValidationErrorResponse",
 *      title="Validation error response",
 *      @OA\Property(
 *          property="success",
 *          type="boolean",
 *          default=false,
 *      ),
 *      @OA\Property(
 *          property="message",
 *          type="string",
 *          description="Error message",
 *      ),
 *      @OA\Property(
 *          property="code",
 *          type="integer",
 *          description="Response code",
 *      ),
 * )
 */
