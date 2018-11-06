<?php


namespace calderawp\calderaforms\cf2\RestApi;


use calderawp\calderaforms\cf2\Exception;
use calderawp\calderaforms\cf2\Fields\FieldTypes\FileFieldType;
use calderawp\calderaforms\cf2\Fields\Handlers\Cf1FileUploader;
use calderawp\calderaforms\cf2\Fields\Handlers\FileUpload;
use calderawp\calderaforms\cf2\RestApi\Endpoint;
use calderawp\calderaforms\cf2\Services\QueueSchedulerService;
use calderawp\calderaforms\cf2\Transients\Cf1TransientsApi;
use calderawp\calderaforms\cf2\Fields\Handlers\UploaderContract;

class RunQueue extends Endpoint
{


	public function getUri()
	{
		return 'queue';
	}

	/** @inheritdoc */
    protected function getArgs()
    {
        return [

            'methods' => 'POST',
            'callback' => [$this, 'runQueue'],
            'permission_callback' => [$this, 'permissionsCallback' ],
            'args' => [
                'jobs' => [
                    'description' => __('Total jobs to run per back', 'caldera-forms'),
					'required' => false,
					'default' => 10,
					'sanitize_callback' => 'absint'

				],
            ]
        ];
    }

    /**
     * Permissions check for queue runner endpoint
     *
     * @since 1.8.0
     *
     * @param \WP_REST_Request $request Request object
     *
     * @return bool
     */
    public function permissionsCallback(\WP_REST_Request $request ){
        return true;
    }

    /**
     * Trigger queue manger from remote ping
     *
     * @since 1.8.0
     *
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */

    /**
     * @param \WP_REST_Request $request
     * @return mixed|null|\WP_REST_Response
     */
    public function runQueue(\WP_REST_Request $request)
    {
    	$totalJobs = caldera_forms_get_v2_container()
		   ->getService(QueueSchedulerService::class )
		   ->runJobs($request['jobs']);

    	$statusCode = $totalJobs > 0 ? 201 : 200;
        $r =  rest_ensure_response($totalJobs);
        $r->set_status($statusCode);
        return $r;
    }



}
